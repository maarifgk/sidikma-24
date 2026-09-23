const {test} = require('node:test');
const assert = require('node:assert/strict');
const {execFileSync} = require('node:child_process');
const fs = require('node:fs');
const vm = require('node:vm');

const script = execFileSync('php', ['tests/render-attendance-mobile.php'], {encoding: 'utf8'});
const geometry = fs.readFileSync('public/js/attendance-geofence.js', 'utf8');
const fix = (latitude = -6.5) => ({coords: {latitude, longitude: 110.5, accuracy: 5}});

function setup(acquire, options = {}) {
    const elements = new Map();
    function element(id) {
        if (!elements.has(id)) elements.set(id, {
            value: '', textContent: '', hidden: false, disabled: false, handlers: {},
            addEventListener(type, fn) {this.handlers[type] = fn;}
        });
        return elements.get(id);
    }
    const buttons = [element('datang'), element('pulang')];
    element('attendanceForm').dataset = {
        checkOutTime: '00:00', polygon: JSON.stringify([[-7, 110], [-7, 111], [-6, 111], [-6, 110]]),
        maxAccuracy: '30', userId: '1', csrfToken: 'test-token',
        locationUrl: '/mobile/role-2/presensi/lokasi', storeUrl: '/mobile/role-2/presensi'
    };
    const calls = [], posts = [], alerts = [], reads = [];
    const window = {location: {reload() {}}};
    const document = {
        getElementById: id => id === 'selfie' ? null : element(id),
        querySelectorAll: () => buttons
    };
    const context = vm.createContext({window, document, AbortController, setInterval() {}, setTimeout() {}, clearTimeout() {},
        AttendanceLocation: {
            acquire(...args) {calls.push(args); return acquire(calls.length);},
            message: () => 'Izin lokasi belum diberikan.'
        },
        Swal: {
            fire(...args) {alerts.push(args); return Promise.resolve({isConfirmed: false});},
            showLoading() {}
        },
        FormData: class {constructor() {
            this.latitude = element('latitude').value;
            this.version = element('locationSettingsVersion').value;
        }},
        fetch: async (url, request) => {
            if (request.method !== 'POST') {
                reads.push({url, request});
                if (options.read) return options.read(reads.length);
                return {ok: true, json: async () => ({success: true, user_id: 1, school_name: 'Sekolah Uji', location_context: {
                    school_id: 101, polygon: [[-7, 110], [-7, 111], [-6, 111], [-6, 110]],
                    max_gps_accuracy: 30, version: 'a'.repeat(64)
                }})};
            }
            posts.push({url, options: request});
            if (options.post) return options.post(posts.length);
            return {ok: false, json: async () => ({success: false, message: 'Di luar batas sekolah.'})};
        }
    });
    vm.runInContext(geometry, context);
    context.AttendanceGeofence = window.AttendanceGeofence;
    vm.runInContext(script, context);
    return {element, buttons, calls, posts, alerts, reads, context,
        check: () => element('checkGps').handlers.click(),
        attend: () => vm.runInContext("handleAttendanceClick('datang')", context)};
}

test('GPS check shows actual coordinates and distance without submitting attendance', async () => {
    const s = setup(async () => fix(-7.00005));
    await s.check();
    assert.equal(s.posts.length, 0);
    assert.equal(s.element('latitude').value, '');
    assert.match(s.element('gpsResult').textContent, /5.6 meter di luar batas/);
    assert.match(s.element('gpsCoordinates').textContent, /-7.0000500, 110.5000000/);
    assert.match(decodeURIComponent(s.element('gpsMapLink').href), /query=-7.00005,110.5$/);
    assert.equal(s.element('gpsMapLink').hidden, false);
    assert.equal(s.calls[0][0], 30);
    assert.equal(s.calls[0][1].length, 4);
});

test('attendance obtains a new fix instead of reusing a successful diagnostic fix', async () => {
    const s = setup(async count => count === 1 ? fix() : fix(-8));
    await s.check();
    assert.match(s.element('gpsResult').textContent, /berada di area sekolah/);
    await s.attend();
    assert.equal(s.calls.length, 2);
    assert.equal(s.posts.length, 1);
    assert.equal(s.posts[0].options.body.latitude, -8);
    assert.match(s.element('gpsCoordinates').textContent, /-8.0000000/);
    assert.match(s.element('gpsResult').textContent, /di luar batas sekolah/);
    assert.deepEqual(s.alerts.at(-1), ['Gagal', 'Di luar batas sekolah.', 'error']);
    assert.equal(s.element('checkGps').disabled, false);
});

test('GPS denial clears stale coordinates and permits another check', async () => {
    const s = setup(async count => {if (count === 2) throw {code: 1}; return fix();});
    await s.check();
    await s.check();
    assert.equal(s.element('gpsMapLink').hidden, true);
    assert.equal(s.element('gpsCoordinates').textContent, '');
    assert.match(s.element('gpsResult').textContent, /Izin lokasi/);
    assert.equal(s.element('checkGps').disabled, false);
    assert.ok(s.buttons.every(button => !button.disabled));
    await s.check();
    assert.equal(s.element('gpsMapLink').hidden, false);
    assert.equal(s.posts.length, 0);
});

test('checking GPS and submitting attendance cannot acquire location concurrently', async () => {
    let resolve;
    const s = setup(() => new Promise(done => {resolve = done;}));
    const pending = s.check();
    assert.equal(s.element('checkGps').disabled, true);
    assert.ok(s.buttons.every(button => button.disabled));
    await s.check();
    await s.attend();
    await new Promise(setImmediate);
    assert.equal(s.calls.length, 1);
    assert.equal(s.posts.length, 0);
    resolve(fix());
    await pending;
    assert.ok(s.buttons.every(button => !button.disabled));
});

function savedContext(version = 'b', userId = 1) {
    return {ok: true, json: async () => ({success: true, user_id: userId, school_name: 'Sekolah Terbaru', location_context: {
        school_id: 101, polygon: [[-8, 110], [-8, 111], [-7, 111], [-7, 110]],
        max_gps_accuracy: 10, version: version.repeat(64)
    }})};
}

test('GPS check uses the newly saved server polygon instead of the one embedded in the open page', async () => {
    const s = setup(async () => fix(-7.5), {read: () => savedContext()});
    await s.check();
    assert.match(s.element('gpsResult').textContent, /berada di area sekolah/);
    assert.equal(s.element('gpsSchoolName').textContent, 'Sekolah Terbaru');
    assert.equal(s.calls[0][0], 10);
    assert.equal(s.calls[0][1][0][0], -8);
    assert.equal(s.reads[0].request.cache, 'no-store');
    assert.equal(s.reads[0].request.credentials, 'same-origin');
    assert.equal(s.posts.length, 0);
});

test('settings changed during GPS acquisition cause a fresh context and fresh fix before retrying once', async () => {
    const s = setup(async count => count === 1 ? fix(-7.2) : fix(-7.5), {
        read: count => savedContext(count === 1 ? 'a' : 'b'),
        post: count => count === 1
            ? {ok: false, json: async () => ({rejection_code: 'location_settings_changed', message: 'Area berubah.'})}
            : {ok: true, json: async () => ({success: true, message: 'Presensi berhasil'})}
    });
    await s.attend();
    assert.equal(s.reads.length, 2);
    assert.equal(s.calls.length, 2);
    assert.deepEqual(s.posts.map(post => post.options.body.version), ['a'.repeat(64), 'b'.repeat(64)]);
    assert.deepEqual(s.posts.map(post => post.options.body.latitude), [-7.2, -7.5]);
    assert.equal(s.alerts.at(-1)[0], 'Berhasil');
    assert.equal(s.element('checkGps').disabled, false);
});

test('repeated settings changes stop after one automatic retry and clear the obsolete location display', async () => {
    const s = setup(async () => fix(), {
        post: () => ({ok: false, json: async () => ({rejection_code: 'location_settings_changed', message: 'Area berubah.'})})
    });
    await s.attend();
    assert.equal(s.posts.length, 2);
    assert.match(s.element('gpsResult').textContent, /Area berubah/);
    assert.equal(s.element('gpsMapLink').hidden, true);
    assert.equal(s.element('checkGps').disabled, false);
});

test('failed context loading never falls back to a stale polygon or sends attendance', async () => {
    const s = setup(async () => fix(), {read: () => {throw new Error('Network failed');}});
    await s.attend();
    assert.equal(s.posts.length, 0);
    assert.equal(s.calls.length, 0);
    assert.match(s.element('gpsResult').textContent, /Pengaturan lokasi terbaru belum dapat dimuat/);
    assert.ok(s.buttons.every(button => !button.disabled));
});

test('an account change requires reloading the page instead of submitting under the new account', async () => {
    const s = setup(async () => fix(), {read: () => savedContext('a', 2)});
    await s.attend();
    assert.match(s.element('gpsResult').textContent, /Sesi akun berubah/);
    assert.equal(s.calls.length, 0);
    assert.equal(s.posts.length, 0);
});

function radiusContext(radius, version = 'a', centerLatitude = -7) {
    return {ok: true, json: async () => ({success: true, user_id: 1, school_name: 'Sekolah Radius', location_context: {
        school_id: 101, polygon: [], max_gps_accuracy: 30, version: version.repeat(64),
        geofence: {mode: 'radius', center_latitude: centerLatitude, center_longitude: 110, radius_meters: radius}
    }})};
}

test('mobile displays radius distance and loads the enlarged radius before the next location check', async () => {
    const position = {coords: {latitude: -7 + 100 / 6371000 * 180 / Math.PI, longitude: 110, accuracy: 8}};
    const s = setup(async () => position, {read: count => radiusContext(count === 1 ? 50 : 150)});
    await s.check();
    assert.match(s.element('gpsResult').textContent, /Di Luar Area Presensi/);
    await s.check();
    assert.match(s.element('gpsResult').textContent, /Jarak dari titik sekolah: 100 meter/);
    assert.match(s.element('gpsResult').textContent, /Radius presensi: 150 meter/);
    assert.match(s.element('gpsResult').textContent, /Di Dalam Area Presensi/);
    assert.equal(s.calls[1][1].radius_meters, 150);
    assert.equal(s.posts.length, 0);
});

test('radius changes during GPS acquisition refresh the context and coordinates before retrying', async () => {
    const s = setup(async () => ({coords: {latitude: -7, longitude: 110, accuracy: 5}}), {
        read: count => radiusContext(count === 1 ? 50 : 150, count === 1 ? 'a' : 'b'),
        post: count => count === 1 ? {ok: false, json: async () => ({rejection_code: 'location_settings_changed'})}
            : {ok: true, json: async () => ({success: true})}
    });
    await s.attend();
    assert.equal(s.calls.length, 2);
    assert.equal(s.calls[1][1].radius_meters, 150);
    assert.equal(s.posts[1].options.body.version, 'b'.repeat(64));
    assert.equal(s.alerts.at(-1)[0], 'Berhasil');
});

test('missing school center is explained before attempting GPS or submitting attendance', async () => {
    const s = setup(async () => fix(), {read: () => radiusContext(100, 'a', null)});
    await s.attend();
    assert.match(s.element('gpsResult').textContent, /belum dikonfigurasi/);
    assert.equal(s.posts.length, 0);
    assert.equal(s.calls.length, 0);
});
