import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

export default function initOverlayMap(mapElementId = 'map', latInputId = 'latitude', lngInputId = 'longitude') {
    const mapElement = document.getElementById(mapElementId);

    if (!mapElement) return;

    const map = L.map(mapElementId).setView([51.505, -0.09], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    map.on('click', function (e) {
        const { lat, lng } = e.latlng;
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }

        const latInput = document.getElementById(latInputId);
        const lngInput = document.getElementById(lngInputId);

        if (latInput && lngInput) {
            latInput.value = lat;
            lngInput.value = lng;
        }
    });
}
