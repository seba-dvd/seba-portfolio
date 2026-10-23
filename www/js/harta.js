// The map on harta.php: one marker per row of the points table.
// harta.php picks the provider (includes/config.php) and sends the data as JSON:
//   demo   → Leaflet + OpenStreetMap, no key needed
//   google → Google Maps JavaScript API (calls pornesteHartaGoogle when it is ready)
// The descriptions come from the database, so they go into the page with
// textContent only, never as HTML (no XSS), with either provider.
// Explore mode (◀ ▶ under the map) works the same with both: each provider gives
// explorare.mergiLa(i), which moves the map to place i and opens it.

const DATE_HARTA = JSON.parse(document.getElementById('date-harta').textContent);
const TEXTE_HARTA = JSON.parse(document.getElementById('texte-js')?.textContent || '{}');
const CULOARE_VIZITAT = '#16a34a';               // same green as the legend on the home page
const CENTRU_ROMANIA = { lat: 45.9, lng: 24.97 };
const ZOOM_LOC = 12;
const FARA_ANIMATII = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// ===== Explore mode: go through the places one by one, like levels on a world map =====
const explorare = {
    index: -1,                                       // -1: the tour has not started yet
    mergiLa: null,                                   // set by the map provider

    pornesteInterfata() {
        this.bara = document.getElementById('explorare');
        if (!this.bara) {                            // no places: no explore bar
            return;
        }
        this.inapoi = document.getElementById('explorare-inapoi');
        this.inainte = document.getElementById('explorare-inainte');
        this.inapoi.addEventListener('click', () => this.alege(this.index - 1));
        this.inainte.addEventListener('click', () => this.alege(this.index + 1));
        // Left / right arrows while the bar (or one of its buttons) has focus
        this.bara.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                e.preventDefault();
                this.alege(this.index + (e.key === 'ArrowRight' ? 1 : -1));
            }
        });
        this.arata();
        this.bara.hidden = false;
    },

    alege(i) {
        if (!this.mergiLa || i < 0 || i >= DATE_HARTA.puncte.length) {
            return;
        }
        this.index = i;
        this.mergiLa(i);
        this.arata();
    },

    // A marker was clicked on the map: the tour continues from there
    selectat(i) {
        this.index = i;
        this.arata();
    },

    // The texts come from the page (data-text-*), already in the right language
    arata() {
        if (!this.bara) {
            return;
        }
        const n = DATE_HARTA.puncte.length;
        const pozitie = document.getElementById('explorare-pozitie');
        const nume = document.getElementById('explorare-nume');
        const distanta = document.getElementById('explorare-distanta');

        if (this.index < 0) {
            pozitie.textContent = this.bara.dataset.textPozitie.replace('{i}', '0').replace('{n}', n);
            nume.textContent = this.bara.dataset.textStart;
            distanta.textContent = '';
        } else {
            const punct = DATE_HARTA.puncte[this.index];
            pozitie.textContent = this.bara.dataset.textPozitie.replace('{i}', this.index + 1).replace('{n}', n);
            nume.textContent = punct.descriere;
            distanta.textContent = punct.distanta_km < 0.05
                ? this.bara.dataset.textAcasa
                : this.bara.dataset.textDistanta.replace('{km}',
                    punct.distanta_km.toLocaleString(TEXTE_HARTA.locale, { maximumFractionDigits: 0 }));
        }
        this.inapoi.disabled = this.index <= 0;
        this.inainte.disabled = this.index >= n - 1;
    },
};

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
    marcaje.forEach((marcaj, i) => marcaj.on('click', () => explorare.selectat(i)));

    explorare.mergiLa = (i) => {
        const marcaj = marcaje[i];
        harta.once('moveend', () => marcaj.openPopup());   // open it when the map has arrived
        if (FARA_ANIMATII) {
            harta.setView(marcaj.getLatLng(), ZOOM_LOC);
        } else {
            harta.flyTo(marcaj.getLatLng(), ZOOM_LOC, { duration: 1.2 });
        }
    };

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
    const marcaje = [];

    DATE_HARTA.puncte.forEach((punct, i) => {
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
            explorare.selectat(i);
        });
        marcaje.push(marcaj);
        limite.extend({ lat: punct.lat, lng: punct.lng });
    });

    explorare.mergiLa = (i) => {
        const punct = DATE_HARTA.puncte[i];
        harta.panTo({ lat: punct.lat, lng: punct.lng });   // Google animates short moves by itself
        harta.setZoom(ZOOM_LOC);
        fereastra.setContent(continutLoc(punct));
        fereastra.open({ map: harta, anchor: marcaje[i] });
    };

    if (DATE_HARTA.puncte.length > 1) {
        harta.fitBounds(limite, 40);
    } else if (DATE_HARTA.puncte.length === 1) {
        harta.setCenter({ lat: DATE_HARTA.puncte[0].lat, lng: DATE_HARTA.puncte[0].lng });
        harta.setZoom(10);
    }
};

// Loads Leaflet only when needed (Google mode fallback), with the same SRI hashes as harta.php
function incarcaLeaflet() {
    const leaflet = DATE_HARTA.leaflet;
    return new Promise((gata, esec) => {
        const stil = document.createElement('link');
        stil.rel = 'stylesheet';
        stil.href = leaflet.css;
        stil.integrity = leaflet.css_hash;
        stil.crossOrigin = 'anonymous';
        const script = document.createElement('script');
        script.src = leaflet.js;
        script.integrity = leaflet.js_hash;
        script.crossOrigin = 'anonymous';
        script.onload = gata;
        script.onerror = esec;
        document.head.append(stil, script);
    });
}

// Google calls this when it refuses the key (wrong key, billing ended, credit used up...).
// The visitor still gets a map: the demo one, with a short note under it.
window.gm_authFailure = function () {
    const vechi = document.getElementById('harta');
    const nou = vechi.cloneNode(false);              // an empty container, without Google's leftovers
    vechi.replaceWith(nou);

    incarcaLeaflet()
        .then(() => {
            hartaDemo();
            const nota = document.createElement('p');
            nota.className = 'harta-nota';
            nota.textContent = TEXTE_HARTA.harta_rezerva;
            nou.after(nota);
        })
        .catch(() => arataEroare(TEXTE_HARTA.harta_eroare_google));
};

if (DATE_HARTA.furnizor === 'demo') {
    document.addEventListener('DOMContentLoaded', hartaDemo);
}
explorare.pornesteInterfata();                       // the script is at the end of <body>: the bar exists
