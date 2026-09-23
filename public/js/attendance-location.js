(function (root) {
    'use strict';

    root.AttendanceLocation = {
        acquire(maxAccuracy, geofence = []) {
            const configuredAccuracy = Number(maxAccuracy);
            const maximumAccuracy = Number.isFinite(configuredAccuracy) && configuredAccuracy > 0
                ? configuredAccuracy : 100;

            return new Promise((resolve, reject) => {
                if (!root.isSecureContext) {
                    reject({code: 'https'});
                    return;
                }
                if (!root.navigator.geolocation) {
                    reject({code: 'unsupported'});
                    return;
                }

                let watchId;
                let finished = false;
                let bestAccuracy = Infinity;
                let bestOutsidePosition = null;
                const finish = (error, position) => {
                    if (finished) return;
                    finished = true;
                    root.clearTimeout(timer);
                    if (watchId !== undefined) root.navigator.geolocation.clearWatch(watchId);
                    if (error) reject(error);
                    else resolve(position);
                };
                const timer = root.setTimeout(() => {
                    // Send the best eligible outside fix for server validation/audit
                    // only after giving transient GPS drift a chance to recover.
                    if (bestOutsidePosition) {
                        finish(null, bestOutsidePosition);
                        return;
                    }
                    finish({
                        code: Number.isFinite(bestAccuracy) ? 'accuracy' : 3,
                        accuracy: bestAccuracy,
                        maxAccuracy: maximumAccuracy
                    });
                }, 30000);

                try {
                    watchId = root.navigator.geolocation.watchPosition(position => {
                        const {latitude, longitude, accuracy} = position.coords;
                        if (!Number.isFinite(latitude) || !Number.isFinite(longitude)
                            || Math.abs(latitude) > 90 || Math.abs(longitude) > 180
                            || !Number.isFinite(accuracy) || accuracy < 0) return;
                        bestAccuracy = Math.min(bestAccuracy, accuracy);
                        if (accuracy <= maximumAccuracy) {
                            const area = root.AttendanceGeofence?.inspectArea(latitude, longitude, geofence);
                            if (area?.configured && !area.inside) {
                                if (!bestOutsidePosition || accuracy < bestOutsidePosition.coords.accuracy) {
                                    bestOutsidePosition = position;
                                }
                                return;
                            }
                            finish(null, position);
                        }
                    }, error => {
                        // A temporary unavailable/timeout error may recover on the next fix.
                        if (error.code === 1) finish(error);
                    }, {enableHighAccuracy: true, timeout: 15000, maximumAge: 0});
                    if (finished) root.navigator.geolocation.clearWatch(watchId);
                } catch (error) {
                    finish({code: 'unsupported'});
                }
            });
        },

        message(error) {
            switch (error.code) {
                case 'https':
                    return 'Lokasi membutuhkan koneksi HTTPS. Buka alamat HTTPS aplikasi; jika belum tersedia, hubungi admin sekolah.';
                case 'unsupported':
                    return 'Browser ini tidak dapat mengakses lokasi. Buka aplikasi langsung di Chrome atau Safari terbaru.';
                case 1:
                    return 'Izin lokasi belum diberikan atau diblokir. Buka ikon pengaturan situs di samping alamat, pilih Izin > Lokasi > Izinkan. Aktifkan Lokasi/GPS dan izin lokasi presisi untuk browser di pengaturan HP. Jika dibuka dari WhatsApp atau aplikasi lain, buka tautan langsung di Chrome/Safari. Setelah itu, coba lagi. Jika tetap diblokir, hubungi admin.';
                case 'accuracy':
                    return 'Lokasi masih kurang akurat. Ketidakpastian GPS terbaik: ' + Number(error.accuracy.toFixed(2))
                        + ' meter; batas sekolah: ' + error.maxAccuracy
                        + ' meter. Aktifkan lokasi presisi dan Wi-Fi, pindah ke tempat terbuka di area sekolah, lalu coba lagi.';
                default:
                    return 'Lokasi belum tersedia dalam 30 detik. Pastikan GPS dan koneksi internet aktif, pindah ke tempat terbuka di area sekolah, lalu coba lagi.';
            }
        }
    };
})(window);
