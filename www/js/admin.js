// Admin panel (admin.php): delete confirmations and the map for picking a place.

// "Delete" buttons: ask first. The question comes from the page (data-confirmare),
// already in the right language and with the name of what is deleted.
document.querySelectorAll('form[data-confirmare]').forEach((formular) => {
    formular.addEventListener('submit', (e) => {
        if (!window.confirm(formular.dataset.confirmare)) {
            e.preventDefault();
        }
    });
});

// ===== Place picker: click on the map (or drag the marker) to fill in the coordinates =====
const PUNCTE_ADMIN = JSON.parse(document.getElementById('date-admin').textContent);
const campLat = document.getElementById('loc-lat');
const campLng = document.getElementById('loc-long');

if (typeof L !== 'undefined') {                      // without Leaflet the form still works by hand
    const harta = L.map('harta-admin');
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(harta);

    // The places already saved, as small green dots with their name on hover (as text, not HTML)
    const existente = PUNCTE_ADMIN.map((punct) => {
        const eticheta = document.createElement('span');
        eticheta.textContent = punct.descriere;
        return L.circleMarker([punct.lat, punct.lng], {
            radius: 7, color: '#ffffff', weight: 2, fillColor: '#16a34a', fillOpacity: 1,
        }).bindTooltip(eticheta).addTo(harta);
    });

    // The place being added or edited: a marker that can be dragged
    let marcaj = null;
    const scrieCoordonate = (lat, lng) => {
        campLat.value = lat.toFixed(6);
        campLng.value = lng.toFixed(6);
    };
    const puneMarcaj = (lat, lng) => {
        if (marcaj) {
            marcaj.setLatLng([lat, lng]);
            return;
        }
        marcaj = L.marker([lat, lng], { draggable: true }).addTo(harta);
        marcaj.on('dragend', () => {
            const p = marcaj.getLatLng();
            scrieCoordonate(p.lat, p.lng);
        });
    };
    const citesteCampuri = () => {
        const lat = parseFloat(campLat.value.replace(',', '.'));
        const lng = parseFloat(campLng.value.replace(',', '.'));
        return Number.isFinite(lat) && Number.isFinite(lng) && Math.abs(lat) <= 90 && Math.abs(lng) <= 180
            ? [lat, lng] : null;
    };

    harta.on('click', (e) => {
        puneMarcaj(e.latlng.lat, e.latlng.lng);
        scrieCoordonate(e.latlng.lat, e.latlng.lng);
    });

    // Typing coordinates moves the marker too
    [campLat, campLng].forEach((camp) => camp.addEventListener('input', () => {
        const pozitie = citesteCampuri();
        if (pozitie) {
            puneMarcaj(...pozitie);
            harta.panTo(pozitie);
        }
    }));

    // First view: the place being edited, otherwise all places, otherwise Romania
    const inceput = citesteCampuri();
    if (inceput) {
        puneMarcaj(...inceput);
        harta.setView(inceput, 12);
    } else if (existente.length) {
        harta.fitBounds(L.featureGroup(existente).getBounds(), { padding: [40, 40], maxZoom: 10 });
    } else {
        harta.setView([45.9, 24.97], 6);
    }
}
