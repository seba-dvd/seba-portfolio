// The map on harta.php: one marker per row of the points table.
// harta.php picks the provider (includes/config.php) and sends the data as JSON:
//   demo   → Leaflet + OpenStreetMap, no key needed
//   google → Google Maps JavaScript API (calls pornesteHartaGoogle when it is ready)
// The descriptions come from the database, so they go into the page with
// textContent only, never as HTML (no XSS), with either provider.

const DATE_HARTA = JSON.parse(document.getElementById('date-harta').textContent);
const TEXTE_HARTA = JSON.parse(document.getElementById('texte-js')?.textContent || '{}');
const CULOARE_VIZITAT = '#16a34a';               // same green as the legend on the home page
const CENTRU_ROMANIA = { lat: 45.9, lng: 24.97 };

// What a marker shows when clicked: the description and the coordinates
function continutLoc(punct) {
    const el = document.createElement('div');
    el.className = 'popup-loc';
    const titlu = document.createElement('strong');
    titlu.textContent = punct.descriere;
    const coordonate = document.createElement('small');
    coordonate.textContent = punct.lat.toFixed(4) + ', ' + punct.lng.toFixed(4);
    el.append(titlu, document.createElement('br'), coordonate);
    return el;
}

// The map area shows a message instead (library blocked, key refused...)
function arataEroare(text) {
    const harta = document.getElementById('harta');
    harta.replaceChildren();
    const mesaj = document.createElement('p');
    mesaj.className = 'harta-eroare';
    mesaj.textContent = text;
    harta.append(mesaj);
}

// ===== Demo: Leaflet + OpenStreetMap =====
function hartaDemo() {
    if (typeof L === 'undefined') {                  // the CDN did not answer
        arataEroare(TEXTE_HARTA.harta_eroare);
        return;
    }
    const harta = L.map('harta', { scrollWheelZoom: false });
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(harta);

    const marcaje = DATE_HARTA.puncte.map((punct) => {
        // Label on hover, details on click. Both get DOM elements, not HTML strings:
        // Leaflet would insert a string as HTML.
        const eticheta = document.createElement('span');
        eticheta.textContent = punct.descriere;
        return L.circleMarker([punct.lat, punct.lng], {
            radius: 9, color: '#ffffff', weight: 2, fillColor: CULOARE_VIZITAT, fillOpacity: 1,
        })
            .bindTooltip(eticheta)
            .bindPopup(continutLoc(punct))
            .addTo(harta);
    });

    if (marcaje.length > 1) {
        harta.fitBounds(L.featureGroup(marcaje).getBounds(), { padding: [40, 40] });
    } else if (marcaje.length === 1) {
        harta.setView(marcaje[0].getLatLng(), 10);
    } else {
        harta.setView(CENTRU_ROMANIA, 6);
    }
}

// ===== Google Maps (needs a key with billing) =====
// Called by Google's loader (callback=pornesteHartaGoogle in harta.php)
window.pornesteHartaGoogle = async function () {
    const { Map, InfoWindow } = await google.maps.importLibrary('maps');
    const { AdvancedMarkerElement, PinElement } = await google.maps.importLibrary('marker');

    const harta = new Map(document.getElementById('harta'), {
        center: CENTRU_ROMANIA,
        zoom: 6,
        mapId: 'DEMO_MAP_ID',                        // Google's test map id, needed by AdvancedMarkerElement
    });
    const fereastra = new InfoWindow();
    const limite = new google.maps.LatLngBounds();

    DATE_HARTA.puncte.forEach((punct) => {
        const pin = new PinElement({ background: CULOARE_VIZITAT, borderColor: '#0f5132', glyphColor: '#ffffff' });
        const marcaj = new AdvancedMarkerElement({
            map: harta,
            position: { lat: punct.lat, lng: punct.lng },
            title: punct.descriere,                  // label on hover (plain text)
            content: pin.element,
        });
        marcaj.addListener('click', () => {
            fereastra.setContent(continutLoc(punct));
            fereastra.open({ map: harta, anchor: marcaj });
        });
        limite.extend({ lat: punct.lat, lng: punct.lng });
    });

    if (DATE_HARTA.puncte.length > 1) {
        harta.fitBounds(limite, 40);
    } else if (DATE_HARTA.puncte.length === 1) {
        harta.setCenter({ lat: DATE_HARTA.puncte[0].lat, lng: DATE_HARTA.puncte[0].lng });
        harta.setZoom(10);
    }
};

// Google calls this when it refuses the key (wrong key, billing not enabled...)
window.gm_authFailure = function () {
    arataEroare(TEXTE_HARTA.harta_eroare_google);
};

if (DATE_HARTA.furnizor === 'demo') {
    document.addEventListener('DOMContentLoaded', hartaDemo);
}
