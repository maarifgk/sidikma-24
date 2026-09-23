(function (root) {
    'use strict';

    root.AttendanceGeofence = {
        calculateDistance(latFrom, lngFrom, latTo, lngTo) {
            const radians = value => value * Math.PI / 180;
            const a = Math.sin(radians(latTo - latFrom) / 2) ** 2
                + Math.cos(radians(latFrom)) * Math.cos(radians(latTo))
                * Math.sin(radians(lngTo - lngFrom) / 2) ** 2;
            return 6371000 * 2 * Math.asin(Math.sqrt(Math.max(0, Math.min(1, a))));
        },

        normalizeArea(area) {
            if (Array.isArray(area)) return {mode: 'polygon', polygon: this.normalize(area)};
            if (area?.mode === 'polygon') return {mode: 'polygon', polygon: this.normalize(area.polygon)};
            const number = value => (typeof value === 'number'
                || (typeof value === 'string' && value.trim() !== '')) ? Number(value) : NaN;
            if (area?.mode === 'radius') {
                return {mode: 'radius', center_latitude: number(area.center_latitude),
                    center_longitude: number(area.center_longitude), radius_meters: number(area.radius_meters)};
            }
            return {mode: 'invalid'};
        },

        inspectArea(latitude, longitude, geofence) {
            const area = this.normalizeArea(geofence);
            if (area.mode === 'polygon') return {...this.inspect(latitude, longitude, area.polygon), mode: 'polygon'};
            const configured = area.mode === 'radius' && Number.isFinite(area.center_latitude)
                && Math.abs(area.center_latitude) <= 90 && Number.isFinite(area.center_longitude)
                && Math.abs(area.center_longitude) <= 180 && Number.isFinite(area.radius_meters) && area.radius_meters > 0;
            if (!configured || !Number.isFinite(latitude) || Math.abs(latitude) > 90
                || !Number.isFinite(longitude) || Math.abs(longitude) > 180) {
                return {configured, inside: false, distance: null, mode: area.mode};
            }
            const distance = this.calculateDistance(area.center_latitude, area.center_longitude, latitude, longitude);
            return {configured: true, inside: distance <= area.radius_meters + 1e-7,
                distance, radius: area.radius_meters, mode: 'radius'};
        },

        normalize(polygon) {
            if (!Array.isArray(polygon)) return [];
            const number = value => (typeof value === 'number'
                || (typeof value === 'string' && value.trim() !== '')) ? Number(value) : NaN;
            const points = polygon.map(point => ({
                lat: number(Array.isArray(point) ? point[0] : point?.lat),
                lng: number(Array.isArray(point) ? point[1] : point?.lng)
            }));
            if (points.length < 3 || !points.every(point => Number.isFinite(point.lat) && Math.abs(point.lat) <= 90
                && Number.isFinite(point.lng) && Math.abs(point.lng) <= 180)) return [];
            const origin = points[0];
            let area = 0;
            for (let i = 0, j = points.length - 1; i < points.length; j = i++) {
                area += (points[j].lng - origin.lng) * (points[i].lat - origin.lat)
                    - (points[i].lng - origin.lng) * (points[j].lat - origin.lat);
            }
            return Math.abs(area) > 1e-16 ? points : [];
        },

        inspect(latitude, longitude, polygon) {
            const points = this.normalize(polygon);
            if (points.length < 3 || !Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                return {configured: false, inside: false, distance: null};
            }

            const metersPerDegree = 6371000 * Math.PI / 180;
            const longitudeScale = metersPerDegree * Math.cos(latitude * Math.PI / 180);
            let distance = Infinity;
            let inside = false;
            for (let i = 0, j = points.length - 1; i < points.length; j = i++) {
                const a = points[j], b = points[i];
                const ax = (a.lng - longitude) * longitudeScale;
                const ay = (a.lat - latitude) * metersPerDegree;
                const dx = (b.lng - a.lng) * longitudeScale;
                const dy = (b.lat - a.lat) * metersPerDegree;
                const lengthSquared = dx * dx + dy * dy;
                const projection = lengthSquared > 0
                    ? Math.max(0, Math.min(1, -(ax * dx + ay * dy) / lengthSquared)) : 0;
                distance = Math.min(distance, Math.hypot(ax + projection * dx, ay + projection * dy));

                if (((b.lng > longitude) !== (a.lng > longitude))
                    && latitude < (a.lat - b.lat) * (longitude - b.lng) / (a.lng - b.lng) + b.lat) {
                    inside = !inside;
                }
            }

            return {configured: true, inside: inside || distance <= 0.01, distance};
        }
    };
})(window);
