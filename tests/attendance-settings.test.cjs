const {test} = require('node:test');
const assert = require('node:assert/strict');
const {execFileSync} = require('node:child_process');
const fs = require('node:fs');
const vm = require('node:vm');

const script = execFileSync('php', ['tests/render-attendance-settings.php'], {encoding: 'utf8'});
const geometry = fs.readFileSync('public/js/attendance-geofence.js', 'utf8');
const polygon = [{lat: -7, lng: 110}, {lat: -7, lng: 111}, {lat: -6, lng: 111}, {lat: -6, lng: 110}];

function setup(locationResult, search = '', inputPolygon = polygon, radius = null) {
    const elements = new Map();
    const element = id => {
        if (!elements.has(id)) elements.set(id, {
            _value: '', get value() {return this._value;}, set value(value) {this._value = String(value);},
            textContent: '', className: '', innerHTML: '', disabled: false, handlers: {},
            addEventListener(type, callback) { this.handlers[type] = callback; },
            querySelectorAll() { return []; }
        });
        return elements.get(id);
    };
    element('geofencePolygon').value = JSON.stringify(inputPolygon);
    element('attendanceSettingsData').dataset = {savedPolygon: JSON.stringify(polygon)};
    element('geofenceMode').value = radius === null ? 'polygon' : 'radius';
    if (radius !== null) {
        element('schoolLatitude').value = '-7';
        element('schoolLongitude').value = '110';
        element('schoolRadius').value = String(radius);
        Object.assign(element('attendanceSettingsData').dataset, {mode: 'radius', latitude: '-7', longitude: '110', radius: String(radius)});
    }
    element('accuracyLimit').value = '30';
    const document = {
        getElementById: element,
        querySelector(selector) { return element(selector.startsWith('input') ? 'accuracyLimit' : 'form'); }
    };
    const markers = [], circles = [], fitted = [], alerts = [], limits = [];
    const layer = () => ({
        addTo() { return this; },
        bindTooltip() { return this; },
        on(type, callback) { this[type] = callback; return this; },
        clearLayers() {}
    });
    const map = {...layer(), removeLayer() {}, invalidateSize() {}, fitBounds(bounds) { fitted.push(bounds); }};
    const L = {
        map: () => map, tileLayer: layer, layerGroup: layer, polygon: layer,
        latLngBounds: points => points, divIcon: options => options,
        marker(point, options) { const marker = {...layer(), point, options}; markers.push(marker); return marker; },
        circleMarker: layer,
        circle(point, options) { circles.push({point, options}); return {...layer(), getBounds: () => [point, options.radius]}; }
    };
    const window = {location: {search}};
    const context = vm.createContext({window, document, L, URLSearchParams, setTimeout() {},
        Swal: {fire: (...args) => {alerts.push(args); return Promise.resolve();}, close() {}, showLoading() {}},
        AttendanceLocation: {
            async acquire(limit) {limits.push(limit); if (locationResult instanceof Error) throw locationResult; return locationResult;},
            message: () => 'Izin lokasi belum diberikan.'
        }
    });
    vm.runInContext(geometry, context);
    context.AttendanceGeofence = window.AttendanceGeofence;
    vm.runInContext(script, context);
    return {element, markers, circles, fitted, alerts, limits, map};
}

test('checking current GPS compares location without changing or saving the school boundary', async () => {
    const s = setup({coords: {latitude: -8, longitude: 110.5, accuracy: 5}});
    const original = s.element('geofencePolygon').value;
    const button = s.element('useCurrentLocation');
    await button.handlers.click.call(button);
    assert.deepEqual(s.limits, ['30']);
    assert.equal(Number(s.element('diagnosticLatitude').value), -8);
    assert.equal(Number(s.element('diagnosticAccuracy').value), 5);
    assert.match(s.element('diagnosticResult').textContent, /di luar batas/);
    assert.equal(s.element('geofencePolygon').value, original);
    assert.equal(s.circles.at(-1).options.radius, 5);
    assert.equal(button.disabled, false);
    assert.equal(s.fitted.at(-1).length, 5);
});

test('location denial leaves the boundary intact and allows another attempt', async () => {
    const s = setup(new Error('denied'));
    const original = s.element('geofencePolygon').value;
    const button = s.element('useCurrentLocation');
    await button.handlers.click.call(button);
    assert.equal(button.disabled, false);
    assert.equal(s.element('geofencePolygon').value, original);
    assert.equal(s.element('diagnosticLatitude').value, '');
    assert.match(s.alerts.at(-1)[1], /Izin lokasi/);
});

test('numbered markers remain draggable and update only the selected vertex', () => {
    const s = setup();
    const marker = s.markers[1];
    assert.equal(marker.options.icon.html, '2');
    assert.equal(marker.options.draggable, true);
    assert.equal(marker.options.icon.iconUrl, undefined);
    marker.dragend({target: {getLatLng: () => ({lat: -7, lng: 111.01})}});
    assert.match(s.element('geofenceSaveState').textContent, /belum disimpan/);
    const updated = JSON.parse(s.element('geofencePolygon').value);
    assert.equal(updated[1].lng, 111.01);
    assert.deepEqual(updated[0], polygon[0]);
    assert.deepEqual(updated[2], polygon[2]);
});

test('opening a location from a report shows the comparison without relocating the polygon', () => {
    const s = setup(undefined, '?latitude=-8.018018&longitude=110.4854835&accuracy=10');
    assert.match(s.element('diagnosticResult').textContent, /di luar batas/);
    assert.deepEqual(JSON.parse(s.element('geofencePolygon').value), polygon);
    assert.equal(s.circles.at(-1).options.radius, 10);
});

test('an inside point on an edited polygon is explicitly described as an unsaved comparison', () => {
    const s = setup(undefined, '?latitude=-6.5&longitude=110.5&accuracy=5');
    assert.equal(s.element('geofenceSaveState').textContent, '');
    s.markers[0].dragend({target: {getLatLng: () => ({lat: -7.01, lng: 110})}});
    assert.match(s.element('diagnosticResult').textContent, /di dalam/);
    assert.match(s.element('diagnosticResult').textContent, /perubahan batas yang belum disimpan/);
});

test('unsaved polygon restored after form validation failure is never presented as the saved area', () => {
    const draft = polygon.map(point => ({...point, lat: point.lat - 0.1}));
    const s = setup(undefined, '?latitude=-6.5&longitude=110.5&accuracy=5', draft);
    assert.match(s.element('geofenceSaveState').textContent, /belum disimpan/);
    assert.match(s.element('diagnosticResult').textContent, /belum disimpan/);
});

test('radius map uses the configured meters and updates when the admin enlarges the area', () => {
    const s = setup(undefined, '', polygon, 50);
    assert.equal(s.circles.at(-1).options.radius, 50);
    assert.equal(s.element('geofenceSaveState').textContent, '');
    assert.equal(s.element('polygonFields').hidden, true);
    assert.equal(s.element('schoolRadius').required, true);
    s.element('schoolRadius').value = '150';
    s.element('schoolRadius').handlers.input();
    assert.equal(s.circles.at(-1).options.radius, 150);
    assert.match(s.element('geofenceSaveState').textContent, /belum disimpan/);
    assert.deepEqual(JSON.parse(s.element('geofencePolygon').value), polygon);
});

test('radius center can be moved on the map without editing the saved polygon', () => {
    const s = setup(undefined, '', polygon, 100);
    s.map.click({latlng: {lat: -7.0012345, lng: 110.0098765}});
    assert.equal(s.element('schoolLatitude').value, '-7.0012345');
    assert.equal(s.element('schoolLongitude').value, '110.0098765');
    assert.equal(s.circles.at(-1).options.radius, 100);
    s.markers.at(-1).dragend({target: {getLatLng: () => ({lat: -7, lng: 110})}});
    assert.equal(Number(s.element('schoolLatitude').value), -7);
});

test('radius diagnosis shows distance and keeps poor accuracy separate from area membership', () => {
    const s = setup(undefined, '?latitude=-7&longitude=110&accuracy=40', polygon, 100);
    assert.match(s.element('diagnosticResult').textContent, /Jarak dari titik sekolah: 0.00 meter/);
    assert.match(s.element('diagnosticResult').textContent, /Di dalam area/);
    assert.match(s.element('diagnosticResult').textContent, /Akurasi belum memenuhi/);
});
