// Admin panel (admin.php): delete confirmations, the map for picking a place
// and, with Google Maps, a search box that fills in the place form.

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
const campDescriere = document.getElementById('loc-descriere');
let aratePeHarta = () => {};                         // set below when the Leaflet map exists

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

    // A place found by the search: marker there and zoom in
    aratePeHarta = (lat, lng) => {
        puneMarcaj(lat, lng);
        harta.setView([lat, lng], 12);
    };

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

// ===== Place search (Google Places): pick a result and the form fills itself =====
// Google's loader calls this (callback=pornesteCautarea in admin.php), which happens only
// when the site uses Google Maps with a key. The box goes into #cautare-loc, which stays
// hidden if anything fails, so the form is never left with a search box that does nothing.
const cautareLoc = document.getElementById('cautare-loc');

window.pornesteCautarea = async function () {
    try {
        const { PlaceAutocompleteElement } = await google.maps.importLibrary('places');
        const cautare = new PlaceAutocompleteElement();
        cautare.id = 'cautare-google';
        cautare.setAttribute('aria-labelledby', 'cautare-eticheta');

        cautare.addEventListener('gmp-select', async ({ placePrediction }) => {
            try {
                const loc = placePrediction.toPlace();
                await loc.fetchFields({ fields: ['displayName', 'location'] });
                const lat = loc.location.lat();
                const lng = loc.location.lng();

                campLat.value = lat.toFixed(6);
                campLng.value = lng.toFixed(6);
                campDescriere.value = (loc.displayName || '').slice(0, 255);   // same limit as the column
                aratePeHarta(lat, lng);
            } catch {
                cautareLoc.hidden = true;            // the details request was refused
            }
        });
        // Google refused a request (Places API not enabled, quota used up...)
        cautare.addEventListener('gmp-error', () => {
            cautareLoc.hidden = true;
        });

        document.getElementById('cautare-eticheta').after(cautare);
        cautareLoc.hidden = false;
    } catch {
        cautareLoc.hidden = true;                    // library blocked or failed: the form still works by hand
    }
};

// Google calls this when it refuses the key (wrong key, billing ended...)
window.gm_authFailure = function () {
    if (cautareLoc) {
        cautareLoc.hidden = true;
    }
};
