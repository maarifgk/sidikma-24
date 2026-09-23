const {test} = require('node:test');
const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');
const code = fs.readFileSync('public/js/attendance-location.js', 'utf8');

function setup(secure = true) {
    let success, failure, timeout;
    const cleared = [];
    const window = {
        isSecureContext: secure,
        setTimeout(fn) { timeout = fn; return 1; },
        clearTimeout() {},
        navigator: {geolocation: {
            watchPosition(ok, fail, options) {
                success = ok; failure = fail;
                assert.equal(options.maximumAge, 0);
                assert.equal(options.enableHighAccuracy, true);
                return 42;
            },
            clearWatch(id) { cleared.push(id); }
        }}
    };
    vm.runInNewContext(fs.readFileSync('public/js/attendance-geofence.js', 'utf8'), {window});
    vm.runInNewContext(code, {window});
    return {
        api: window.AttendanceLocation, cleared,
        fix(accuracy, latitude = -6.5, longitude = 110.5) { success({coords: {latitude, longitude, accuracy}}); },
        fail(code) { failure({code}); },
        expire() { timeout(); }
    };
}

test('waits for a better fix and releases GPS after success', async () => {
    const s = setup();
    const result = s.api.acquire(100);
    s.fix(500);
    assert.deepEqual(s.cleared, []);
    s.fail(2);
    s.fix(1);
    assert.equal((await result).coords.accuracy, 1);
    assert.deepEqual(s.cleared, [42]);
});

test('radius GPS acquisition waits for an inside fix and still rejects poor accuracy', async () => {
    const s = setup();
    const area = {mode: 'radius', center_latitude: -7, center_longitude: 110, radius_meters: 100};
    const result = s.api.acquire(30, area);
    s.fix(5, -7 + 101 / 6371000 * 180 / Math.PI, 110);
    assert.deepEqual(s.cleared, []);
    s.fix(80, -7, 110);
    assert.deepEqual(s.cleared, []);
    s.fix(10, -7, 110);
    assert.equal((await result).coords.accuracy, 10);
    assert.deepEqual(s.cleared, [42]);
});

test('denied permission stops GPS and permits a fresh attempt', async () => {
    const s = setup();
    const denied = s.api.acquire(100);
    s.fail(1);
    await assert.rejects(denied, error => error.code === 1);
    assert.match(s.api.message({code: 1}), /Izinkan/);
    const retry = s.api.acquire(100);
    s.fix(20);
    assert.equal((await retry).coords.accuracy, 20);
    assert.deepEqual(s.cleared, [42, 42]);
});

test('poor accuracy is never submitted after the deadline', async () => {
    const s = setup();
    const result = s.api.acquire(100);
    s.fix(500);
    s.fix(200);
    s.expire();
    await assert.rejects(result, error => error.code === 'accuracy' && error.accuracy === 200);
    assert.deepEqual(s.cleared, [42]);
});

test('missing location times out and insecure pages explain HTTPS', async () => {
    const s = setup();
    const result = s.api.acquire(100);
    s.expire();
    await assert.rejects(result, error => error.code === 3);
    const insecure = setup(false);
    await assert.rejects(insecure.api.acquire(100), error => error.code === 'https');
    assert.match(insecure.api.message({code: 'https'}), /HTTPS/);
});

test('a 30 meter school limit accepts more precise fixes and the exact boundary', async () => {
    for (const accuracy of [0, 0.5, 1, 5, 10, 20, 29.99, 30]) {
        const s = setup();
        const result = s.api.acquire(30);
        s.fix(accuracy);
        assert.equal((await result).coords.accuracy, accuracy);
        assert.deepEqual(s.cleared, [42]);
    }
});

test('a school limit of 30 waits through poor fixes until the GPS improves', async () => {
    const s = setup();
    const result = s.api.acquire('30');
    s.fix(100);
    s.fail(3);
    s.fix(30.1);
    assert.deepEqual(s.cleared, []);
    s.fix(10);
    assert.equal((await result).coords.accuracy, 10);
});

test('accuracy failure states the measured uncertainty and the school limit', async () => {
    const s = setup();
    const result = s.api.acquire(30);
    s.fix(80);
    s.fix(30.1);
    s.expire();
    await assert.rejects(result, error => {
        assert.equal(error.code, 'accuracy');
        assert.match(s.api.message(error), /30\.1 meter/);
        assert.match(s.api.message(error), /batas sekolah: 30 meter/);
        return true;
    });
});

test('invalid school limits fall back to the same maximum as the server', async () => {
    for (const limit of [null, 0, -1, Infinity, NaN]) {
        const s = setup();
        const result = s.api.acquire(limit);
        s.fix(101);
        assert.deepEqual(s.cleared, []);
        s.fix(100);
        assert.equal((await result).coords.accuracy, 100);
    }
});

test('an accurate outside fix waits for GPS drift to recover inside the school', async () => {
    const s = setup();
    const polygon = [[-7, 110], [-7, 111], [-6, 111], [-6, 110]];
    const result = s.api.acquire(30, polygon);
    s.fix(5, -7.00001);
    assert.deepEqual(s.cleared, []);
    s.fix(10, -6.5);
    assert.equal((await result).coords.latitude, -6.5);
    assert.deepEqual(s.cleared, [42]);
});

test('persistently outside fixes reach the server for rejection and audit after the deadline', async () => {
    const s = setup();
    const polygon = [[-7, 110], [-7, 111], [-6, 111], [-6, 110]];
    const result = s.api.acquire(30, polygon);
    s.fix(20, -8);
    s.fix(5, -8.1);
    assert.deepEqual(s.cleared, []);
    s.expire();
    const position = await result;
    assert.equal(position.coords.latitude, -8.1);
    assert.equal(position.coords.accuracy, 5);
    assert.deepEqual(s.cleared, [42]);
});
