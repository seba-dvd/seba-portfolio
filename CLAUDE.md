# Seba. – site personal (PAI)

## Scop
Site personal și aplicație web pentru disciplina Programarea Aplicațiilor Internet (PAI), UNITBV 2026–2027: pagini publice RO/EN (Acasă, Despre, Proiecte, CV, Contact), conturi cu autentificare, pagina protejată cu harta locurilor vizitate și panoul de administrare. „Gata” = cerințele din enunț (`docs/Proiect - PAI 2025.pdf`) îndeplinite și prezentarea M10 susținută.

## Decizii (cu data și cine a decis)
- 2026-10-21 — PHP 8.2 + MariaDB pe XAMPP, `www/` legat la `htdocs\pai` printr-un junction (David)
- Acces la bază prin contul dedicat `pai_app` (doar citire și adăugare) și interogări pregătite `mysqli` (David)
- Harta implicită: Leaflet (`harta_furnizor = 'demo'`); Google Maps doar cu cheie în `config.php` — în așteptarea acordului profesorului (David)
- 2026-11-13 — comutatoarele `harta_publica` și `inregistrare_deschisa` în `config.php`; valorile implicite păstrează cerințele cursului (David)

## Mediu de lucru
- XAMPP cu PHP 8.2 (testat cu 8.2.12), MariaDB; site-ul la `http://localhost/pai/`.
- `www/includes/config.php` se face din `config.example.php` și nu intră în git.
- Teste: cu Apache și MySQL pornite, `bash teste/verificare.sh` (251 de verificări) și `php teste/verificare-bd.php`.

## Structura repo-ului
- `www/` — rădăcina web: paginile, `includes/` (cod comun, blocat din browser), `api/`, `css/`, `js/`, `img/`
- `sql/` — `schema.sql`, `date-initiale.sql`, `utilizator.sql`, `admin.sql`
- `teste/` — scripturile de verificare
- `docs/` — enunțul, carta, documentația, `jurnal.md` (jurnalul de dezvoltare pentru curs), `jurnal-sesiuni.md` (sesiunile de lucru cu Claude)

## Cifre de referință
- v0.13.0 (13.11.2026); 251 de verificări în `teste/verificare.sh` (269 cu fluxul de administrare).
- Lighthouse: 100 la accesibilitate, bune practici și SEO pe paginile publice (măsurat la v0.13.0).

## Capcane cunoscute
- `teste/verificare.sh` rulează ca `root` pe baza reală `pai_proiect`: golește `incercari_login` și inserează date de test care rămân dacă scriptul se oprește la jumătate.
- Rolul de admin e doar un nume din `config.php` (`'admini' => ['seba']`): pe o instalare nouă cu înregistrarea deschisă, primul care își face contul „seba” devine admin.
- Modul Google al hărții folosește `mapId: 'DEMO_MAP_ID'` (`www/js/harta.js`).
- `sql/date-initiale.sql` are doar 2 locuri.
- README-ul spune „depozit privat”, dar repo-ul e public.

## Unde am rămas
M0–M9 sunt încheiate (v0.13.0). Rămân M7 (culori pentru locurile vizitate / de vizitat și fotografii, după acordul profesorului) și M10 (prezentarea). **Următorul pas:** răspunsul scris al profesorului despre Leaflet vs Google Maps; până atunci, Google Maps ca mod implicit, cu cheie restricționată și Map ID propriu. Sesiunile sunt în `docs/jurnal-sesiuni.md`.
