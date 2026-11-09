# Generatorul raportului Word

Construiește `docs/Documentatie_Proiect.docx` din șablonul universității și din textul raportului.

| Fișier | Rol |
|---|---|
| `continut.py` | Textul raportului: capitole, paragrafe, tabele, coduri, figuri. Aici se fac modificările. |
| `genereaza.py` | Pune textul în șablon: stiluri, numerotarea figurilor / tabelelor / codurilor, listele, imaginile. |
| `sablon.docx` | Șablonul original UniTBv (din commit-ul `f669ce1`). Nu se modifică. |
| `img/` | Capturile de ecran folosite ca figuri (`arhitectura.png` este desenată din `arhitectura.html`). |

## Rulare

Din acest folder, cu Python 3.12 (are biblioteca `lxml`):

```bash
py -3.12 genereaza.py
```

Rezultatul suprascrie `docs/Documentatie_Proiect.docx`. Fără `lxml`: `py -3.12 -m pip install lxml`.
Folderul `build/` este temporar și ignorat de git.

## Adăugarea unei secțiuni

1. Scrie textul în `continut.py` (funcțiile `p`, `bullet`, `titlu`, `tabel`, `cod`, `figura`).
2. Pune o captură nouă în `img/` și folosește-o cu `fig(img('nume.png'), 'Legenda')`.
3. Rulează generatorul și deschide raportul în Word.

Numerele figurilor, tabelelor și codurilor sunt date automat, în ordinea în care apar.
Dacă textul le pomenește („Figura 3”), verifică trimiterile după ce adaugi o figură nouă.
