const {test} = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const fixture = JSON.parse(fs.readFileSync('tests/fixtures/attendance-geofence.json', 'utf8'));
const window = {};
vm.runInNewContext(fs.readFileSync('public/js/attendance-geofence.js', 'utf8'), {window});
const geofence = window.AttendanceGeofence;

test('browser matches server fixtures on all edges, corners and nearby outside points', () => {
    for (const polygon of [fixture.polygon, [...fixture.polygon].reverse(), [...fixture.polygon, fixture.polygon[0]]]) {
        for (const point of fixture.cases) {
            assert.equal(geofence.inspect(point.lat, point.lng, polygon).inside, point.inside, point.name);
        }
    }
    assert.ok(Math.abs(geofence.inspect(-8.01815, 110.4855, fixture.polygon).distance - 5.56) < 0.02);
});

test('invalid and collapsed polygons cannot admit a point', () => {
    for (const polygon of [[], [[0, 0]], [[0, 0], [0, 0], [0, 0]], [[0, 0], [1, 1], [2, 2]],
        [[0, 0], ['bad', 1], [1, 0]], [[0, 0], [1, 181], [1, 0]], [[0, 0], [null, 1], [1, 0]]]) {
        assert.equal(geofence.inspect(0, 0, polygon).configured, false);
        assert.equal(geofence.inspect(0, 0, polygon).inside, false);
    }
});

test('concave areas keep the excluded part outside', () => {
    const polygon = [[0, 0], [0, 2], [1, 2], [1, 1], [2, 1], [2, 0]];
    assert.equal(geofence.inspect(0.5, 1.5, polygon).inside, true);
    assert.equal(geofence.inspect(1.5, 1.5, polygon).inside, false);
});

test('radius uses meters with the same strict boundary as Laravel', () => {
    const area = {mode: 'radius', center_latitude: -7, center_longitude: 110, radius_meters: 100};
    for (const meters of [0, 10, 50, 99, 100, 101, 130]) {
        const latitude = -7 + meters / 6371000 * 180 / Math.PI;
        const result = geofence.inspectArea(latitude, 110, area);
        assert.ok(Math.abs(result.distance - meters) < 0.0001);
        assert.equal(result.inside, meters <= 100);
    }
    assert.ok(Math.abs(geofence.calculateDistance(0, 0, 0, 1) - 111194.9266) < 0.001);
    assert.ok(Number.isFinite(geofence.calculateDistance(0, 0, 0, 180)));
});

test('missing radius coordinates never become zero or fall back to a polygon', () => {
    for (const invalid of [null, '', 'bad', Infinity, 91]) {
        const result = geofence.inspectArea(0, 0, {mode: 'radius', center_latitude: invalid, center_longitude: 0, radius_meters: 100});
        assert.equal(result.configured, false);
        assert.equal(result.inside, false);
    }
    assert.equal(geofence.inspectArea(0, 0, {mode: 'radius', center_latitude: 0, center_longitude: 0, radius_meters: 100}).inside, true);
});

test('user supplied Karangpilang boundary includes its corners, edges and interior in the browser', () => {
    const {polygon} = JSON.parse(fs.readFileSync('tests/fixtures/attendance-karangpilang.json', 'utf8'));
    const center = polygon.reduce((sum, p) => ({lat: sum.lat + p.lat / 4, lng: sum.lng + p.lng / 4}), {lat: 0, lng: 0});
    assert.equal(geofence.inspectArea(center.lat, center.lng, polygon).inside, true);
    polygon.forEach((p, index) => {
        const next = polygon[(index + 1) % polygon.length];
        for (const point of [p, {lat: (p.lat + next.lat) / 2, lng: (p.lng + next.lng) / 2},
            {lat: (p.lat + center.lat) / 2, lng: (p.lng + center.lng) / 2}]) {
            assert.equal(geofence.inspectArea(point.lat, point.lng, polygon).inside, true);
        }
        assert.equal(geofence.inspectArea(p.lat + (p.lat - center.lat) * 0.1, p.lng + (p.lng - center.lng) * 0.1, polygon).inside, false);
    });
});
