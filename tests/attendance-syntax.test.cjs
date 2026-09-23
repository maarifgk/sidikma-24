const {test} = require('node:test');
const assert = require('node:assert/strict');
const {execFileSync} = require('node:child_process');
const fs = require('node:fs');
const vm = require('node:vm');

const rendered = JSON.parse(execFileSync('php', ['tests/render-attendance-views.php'], {encoding: 'utf8'}));

test('all attendance page scripts parse before Blade rendering, including editor diagnostics', () => {
    const paths = fs.readdirSync('resources/views/backend/presensi').map(name => 'presensi/' + name);
    paths.push('mobile_role2/presensi.blade.php', 'mobile_role2/izin.blade.php');
    for (const path of paths) {
        const source = fs.readFileSync('resources/views/backend/' + path, 'utf8');
        for (const [, script] of source.matchAll(/<script\b[^>]*>([\s\S]*?)<\/script>/g)) {
            assert.doesNotThrow(() => new vm.Script(script, {filename: path}));
            assert.doesNotMatch(script, /@json|\{\{|\{!!/);
        }
    }
});

test('rendered attendance scripts parse with quotes, newlines, and HTML in server data', () => {
    for (const [page, {scripts}] of Object.entries(rendered.pages)) {
        for (const script of scripts) assert.doesNotThrow(() => new vm.Script(script, {filename: page}));
    }
    for (const page of ['presensi/settings', 'presensi/permissions', 'mobile_role2/izin']) {
        const {scripts, datasets} = rendered.pages[page];
        let alert;
        vm.runInNewContext(scripts[0], {
            document: {
                addEventListener: (_name, callback) => callback(),
                getElementById: id => ({dataset: datasets[id]})
            },
            Swal: {fire: (...args) => {alert = args;}}
        });
        assert.equal(alert[1], rendered.message);
    }
});

test('server data reaches chart, map, saved polygon, and mobile request configuration intact', () => {
    const dashboard = rendered.pages['presensi/dashboard'].datasets.attendanceDashboardData;
    assert.deepEqual(JSON.parse(dashboard.hadir), [1]);
    assert.deepEqual(JSON.parse(dashboard.izin), [0]);
    assert.deepEqual(JSON.parse(dashboard.dates), ['16/09']);
    assert.equal(JSON.parse(dashboard.mapPoints)[0].name, rendered.message);
    assert.deepEqual(JSON.parse(dashboard.polygon), rendered.polygon);
    const settings = rendered.pages['presensi/settings'].datasets.attendanceSettingsData;
    assert.deepEqual(JSON.parse(settings.savedPolygon), rendered.polygon);
    const mobile = rendered.pages['mobile_role2/presensi'].datasets.attendanceForm;
    assert.equal(mobile.checkOutTime, '14:00');
    assert.equal(mobile.userId, '1');
    assert.equal(mobile.maxAccuracy, '30');
    assert.deepEqual(JSON.parse(mobile.polygon), rendered.polygon);
    assert.match(mobile.locationUrl, /\/mobile\/role-2\/presensi\/lokasi$/);
    assert.match(mobile.storeUrl, /\/mobile\/role-2\/presensi$/);
    assert.ok(mobile.csrfToken);
    const radiusSettings = rendered.pages['presensi/settings:radius'].datasets.attendanceSettingsData;
    assert.equal(radiusSettings.mode, 'radius');
    assert.equal(radiusSettings.latitude, '-7');
    assert.equal(radiusSettings.longitude, '110');
    assert.equal(radiusSettings.radius, '150');
    const radiusDashboard = rendered.pages['presensi/dashboard:radius'].datasets.attendanceDashboardData;
    assert.equal(JSON.parse(radiusDashboard.geofence).radius_meters, 150);
});
