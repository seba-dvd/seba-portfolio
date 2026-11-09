# Builds docs/Documentatie_Proiect.docx from the UniTBv template (sablon.docx)
# and the report text in continut.py. Run from this folder: python genereaza.py
# (or: python genereaza.py <template.docx> <out.docx>). Needs lxml: pip install lxml
import copy, re, shutil, struct, sys, zipfile, os
from pathlib import Path
from xml.sax.saxutils import escape
from lxml import etree

AICI = Path(__file__).parent
sys.path.insert(0, str(AICI))
import continut as C

W = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'
R = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships'
NS = {'w': W, 'r': R}
q = lambda t: '{%s}%s' % (W, t)

SRC = Path(sys.argv[1]) if len(sys.argv) > 1 else AICI / 'sablon.docx'
OUT = Path(sys.argv[2]) if len(sys.argv) > 2 else AICI.parent / 'Documentatie_Proiect.docx'
LUCRU = AICI / 'build'
if LUCRU.exists():
    shutil.rmtree(LUCRU)
with zipfile.ZipFile(SRC) as z:
    z.extractall(LUCRU)

doc_path = LUCRU / 'word' / 'document.xml'
tree = etree.parse(str(doc_path))
root = tree.getroot()
body = root.find(q('body'))
els = list(body)

WNS = ' '.join('xmlns:%s="%s"' % (k, v) for k, v in root.nsmap.items() if k)

def frag(xml):
    """Parse a fragment of w: XML (one element) with the document's namespaces."""
    wrapped = '<root %s>%s</root>' % (WNS, xml)
    return list(etree.fromstring(wrapped))

def set_text(p, text):
    """Replace all runs of paragraph p with one run holding text (keeps first run's rPr)."""
    runs = p.findall(q('r'))
    rpr = runs[0].find(q('rPr')) if runs else None
    for child in list(p):
        if child.tag != q('pPr'):
            p.remove(child)
    r = etree.SubElement(p, q('r'))
    if rpr is not None:
        r.append(copy.deepcopy(rpr))
    t = etree.SubElement(r, q('t'))
    t.text = text
    t.set('{http://www.w3.org/XML/1998/namespace}space', 'preserve')
    return p

# ---------- inline text: **bold**, `code`, plain ----------
FONT = '<w:rFonts w:ascii="UT Sans" w:hAnsi="UT Sans"/>'
COD_FONT = '<w:rFonts w:ascii="Courier New" w:hAnsi="Courier New" w:cs="Courier New"/>'

def runs(text, size=None, extra=''):
    out = []
    sz = '<w:sz w:val="%d"/><w:szCs w:val="%d"/>' % (size, size) if size else ''
    for parte in re.split(r'(\*\*[^*]+\*\*|`[^`]+`)', text):
        if not parte:
            continue
        if parte.startswith('**'):
            rpr, s = FONT + '<w:b/><w:bCs/>' + extra + sz, parte[2:-2]
        elif parte.startswith('`'):
            csz = '<w:sz w:val="%d"/><w:szCs w:val="%d"/>' % ((size or 24) - 2, (size or 24) - 2)
            rpr, s = COD_FONT + extra + csz, parte[1:-1]
        else:
            rpr, s = FONT + extra + sz, parte
        out.append('<w:r><w:rPr>%s</w:rPr><w:t xml:space="preserve">%s</w:t></w:r>' % (rpr, escape(s)))
    return ''.join(out)

def fld(instr, cached, rpr=FONT):
    return ('<w:r><w:rPr>%s</w:rPr><w:fldChar w:fldCharType="begin"/></w:r>'
            '<w:r><w:rPr>%s</w:rPr><w:instrText xml:space="preserve"> %s </w:instrText></w:r>'
            '<w:r><w:rPr>%s</w:rPr><w:fldChar w:fldCharType="separate"/></w:r>'
            '<w:r><w:rPr>%s</w:rPr><w:t>%s</w:t></w:r>'
            '<w:r><w:rPr>%s</w:rPr><w:fldChar w:fldCharType="end"/></w:r>') % (
                rpr, rpr, escape(instr), rpr, rpr, escape(cached), rpr)

# ---------- paragraph builders ----------
P_NORMAL = ('<w:pPr><w:spacing w:after="120" w:line="288" w:lineRule="auto"/><w:ind w:firstLine="709"/>'
            '<w:jc w:val="both"/><w:rPr>%s</w:rPr></w:pPr>' % FONT)

def p_normal(text):
    return '<w:p>%s%s</w:p>' % (P_NORMAL, runs(text))

def p_gol():
    return '<w:p><w:pPr><w:rPr>%s</w:rPr></w:pPr></w:p>' % FONT

def p_pagina_noua():
    return ('<w:p><w:pPr><w:spacing w:line="288" w:lineRule="auto"/><w:rPr>%s</w:rPr></w:pPr>'
            '<w:r><w:rPr>%s</w:rPr><w:br w:type="page"/></w:r></w:p>') % (FONT, FONT)

def p_bullet(text):
    return ('<w:p><w:pPr><w:pStyle w:val="ListParagraph"/><w:numPr><w:ilvl w:val="0"/><w:numId w:val="20"/></w:numPr>'
            '<w:spacing w:after="80" w:line="288" w:lineRule="auto"/><w:jc w:val="both"/></w:pPr>%s</w:p>') % runs(text)

def p_pas(nr, text):
    return ('<w:p><w:pPr><w:spacing w:after="80" w:line="288" w:lineRule="auto"/><w:ind w:left="1069" w:hanging="360"/>'
            '<w:jc w:val="both"/></w:pPr>%s<w:r><w:rPr>%s</w:rPr><w:tab/></w:r>%s</w:p>') % (
                runs('%d.' % nr), FONT, runs(text))

BM = [9000]
TOC_INTRARI = []          # (level, number, text, bookmark)
NUMAR = [0, 0, 0]

def bookmark():
    BM[0] += 1
    return BM[0], '_Toc%d' % (91000000 + BM[0])

H1_NUM = ('<w:pPr><w:pStyle w:val="Heading1"/><w:pBdr><w:top w:val="none" w:sz="0" w:space="0" w:color="auto"/>'
          '<w:left w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:bottom w:val="double" w:sz="4" w:space="1" w:color="auto"/>'
          '<w:right w:val="none" w:sz="0" w:space="0" w:color="auto"/><w:between w:val="none" w:sz="0" w:space="0" w:color="auto"/></w:pBdr>'
          '<w:ind w:right="169" w:firstLine="709"/><w:jc w:val="right"/><w:rPr><w:color w:val="auto"/></w:rPr></w:pPr>')
H1_FARA = H1_NUM.replace('<w:pStyle w:val="Heading1"/>',
                         '<w:pStyle w:val="Heading1"/><w:numPr><w:ilvl w:val="0"/><w:numId w:val="0"/></w:numPr>')
H2 = ('<w:pPr><w:pStyle w:val="Heading2"/><w:spacing w:line="288" w:lineRule="auto"/><w:ind w:left="0" w:firstLine="0"/>'
      '<w:rPr><w:b w:val="0"/><w:color w:val="auto"/></w:rPr></w:pPr>')
H3 = ('<w:pPr><w:pStyle w:val="Heading3"/><w:spacing w:line="288" w:lineRule="auto"/><w:ind w:left="0" w:firstLine="709"/>'
      '<w:rPr><w:color w:val="auto"/><w:lang w:val="ro-RO"/></w:rPr></w:pPr>')

def titlu(nivel, text, numerotat=True):
    bid, nume = bookmark()
    if nivel == 1:
        ppr = H1_NUM if numerotat else H1_FARA
        if numerotat:
            NUMAR[0] += 1; NUMAR[1] = NUMAR[2] = 0
            nr = str(NUMAR[0])
        else:
            nr = ''
    elif nivel == 2:
        ppr = H2; NUMAR[1] += 1; NUMAR[2] = 0
        nr = '%d.%d' % (NUMAR[0], NUMAR[1])
    else:
        ppr = H3; NUMAR[2] += 1
        nr = '%d.%d.%d' % tuple(NUMAR)
    TOC_INTRARI.append((nivel, nr, text, nume))
    r = '<w:r><w:rPr><w:color w:val="auto"/></w:rPr><w:t xml:space="preserve">%s</w:t></w:r>' % escape(text)
    return '<w:p>%s<w:bookmarkStart w:id="%d" w:name="%s"/>%s<w:bookmarkEnd w:id="%d"/></w:p>' % (ppr, bid, nume, r, bid)

def capitol(text, numerotat=True):
    return p_pagina_noua() + p_gol() * 4 + titlu(1, text, numerotat)

# ---------- captions (SEQ fields, for the lists of figures / tables / code) ----------
LEGENDE = {'Figura': [], 'Tabelul': [], 'Codul': []}

def legenda(eticheta, text, aliniere='center'):
    nr = len(LEGENDE[eticheta]) + 1
    bid, nume = bookmark()
    LEGENDE[eticheta].append((nr, text, nume))
    rpr = '<w:color w:val="auto"/><w:lang w:val="ro-RO"/>'
    keep = '<w:keepNext/>' if eticheta == 'Tabelul' else ''
    return ('<w:p><w:pPr><w:pStyle w:val="Caption"/>%s<w:spacing w:after="200" w:line="288" w:lineRule="auto"/>'
            '<w:jc w:val="%s"/><w:rPr>%s</w:rPr></w:pPr><w:bookmarkStart w:id="%d" w:name="%s"/>'
            '<w:r><w:rPr>%s</w:rPr><w:t xml:space="preserve">%s </w:t></w:r>%s'
            '<w:r><w:rPr>%s</w:rPr><w:t xml:space="preserve">. %s</w:t></w:r><w:bookmarkEnd w:id="%d"/></w:p>') % (
                keep, aliniere, rpr, bid, nume, rpr, eticheta,
                fld('SEQ %s \\* ARABIC' % eticheta, str(nr), rpr), rpr, escape(text), bid)

# ---------- code ----------
def cod(linii, text_legenda):
    out = []
    for i, linie in enumerate(linii):
        keep = '<w:keepNext/>' if i < len(linii) - 1 else ''
        out.append(('<w:p><w:pPr><w:pStyle w:val="PlainText"/>%s<w:shd w:val="clear" w:color="auto" w:fill="F2F2F2"/>'
                    '<w:spacing w:after="0" w:line="240" w:lineRule="auto"/><w:ind w:left="284" w:firstLine="0"/></w:pPr>'
                    '<w:r><w:rPr>%s<w:sz w:val="18"/><w:szCs w:val="18"/><w:lang w:val="en-US"/></w:rPr>'
                    '<w:t xml:space="preserve">%s</w:t></w:r></w:p>') % (keep, COD_FONT, escape(linie) if linie else ' '))
    out.append(legenda('Codul', text_legenda))
    return ''.join(out)

# ---------- tables ----------
LATIME = 9639

def tabel(capete, randuri, procente, text_legenda):
    lat = [round(LATIME * p / 100) for p in procente]
    lat[-1] = LATIME - sum(lat[:-1])
    grid = ''.join('<w:gridCol w:w="%d"/>' % l for l in lat)

    def celula(text, l, cap):
        shd = '<w:shd w:val="clear" w:color="auto" w:fill="D9D9D9"/>' if cap else ''
        paragrafe = ''.join(
            '<w:p><w:pPr><w:spacing w:before="30" w:after="30" w:line="240" w:lineRule="auto"/><w:ind w:firstLine="0"/></w:pPr>%s</w:p>'
            % runs(('**%s**' % bucata) if cap else bucata, size=20)
            for bucata in str(text).split('\n'))
        return '<w:tc><w:tcPr><w:tcW w:w="%d" w:type="dxa"/>%s</w:tcPr>%s</w:tc>' % (l, shd, paragrafe)

    rows = ['<w:tr><w:trPr><w:cantSplit/><w:tblHeader/></w:trPr>%s</w:tr>' % ''.join(
        celula(c, l, True) for c, l in zip(capete, lat))]
    for rand in randuri:
        rows.append('<w:tr><w:trPr><w:cantSplit/></w:trPr>%s</w:tr>' % ''.join(
            celula(c, l, False) for c, l in zip(rand, lat)))
    tbl = ('<w:tbl><w:tblPr><w:tblStyle w:val="TableGrid"/><w:tblW w:w="%d" w:type="dxa"/><w:jc w:val="center"/>'
           '<w:tblLayout w:type="fixed"/><w:tblCellMar><w:left w:w="85" w:type="dxa"/><w:right w:w="85" w:type="dxa"/></w:tblCellMar>'
           '<w:tblLook w:val="04A0" w:firstRow="1" w:lastRow="0" w:firstColumn="1" w:lastColumn="0" w:noHBand="0" w:noVBand="1"/>'
           '</w:tblPr><w:tblGrid>%s</w:tblGrid>%s</w:tbl>') % (LATIME, grid, ''.join(rows))
    return legenda('Tabelul', text_legenda, 'right') + tbl + p_gol()

# ---------- images ----------
IMAGINI = []   # (rId, src path, name)
DOCPR = [5000]

def dim_png(path):
    with open(path, 'rb') as f:
        f.read(16)
        return struct.unpack('>II', f.read(8))

def figura(path, text_legenda, latime_cm=15.5):
    w, h = dim_png(path)
    cx = int(latime_cm * 360000)
    cy = int(cx * h / w)
    rid = 'rIdImg%d' % (len(IMAGINI) + 1)
    nume = 'figura%d.png' % (len(IMAGINI) + 1)
    IMAGINI.append((rid, path, nume))
    DOCPR[0] += 1
    desen = ('<w:drawing><wp:inline distT="0" distB="0" distL="0" distR="0"><wp:extent cx="%d" cy="%d"/>'
             '<wp:docPr id="%d" name="%s" descr="%s"/><wp:cNvGraphicFramePr>'
             '<a:graphicFrameLocks xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" noChangeAspect="1"/></wp:cNvGraphicFramePr>'
             '<a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">'
             '<a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">'
             '<pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:nvPicPr><pic:cNvPr id="0" name="%s"/><pic:cNvPicPr/></pic:nvPicPr>'
             '<pic:blipFill><a:blip r:embed="%s"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill>'
             '<pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="%d" cy="%d"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr>'
             '</pic:pic></a:graphicData></a:graphic></wp:inline></w:drawing>') % (
                 cx, cy, DOCPR[0], nume, escape(text_legenda, {'"': '&quot;'}), nume, rid, cx, cy)
    p = ('<w:p><w:pPr><w:keepNext/><w:spacing w:before="120" w:after="60"/><w:jc w:val="center"/></w:pPr>'
         '<w:r><w:rPr><w:noProof/></w:rPr>%s</w:r></w:p>') % desen
    return p + legenda('Figura', text_legenda)

# ---------- bibliography ----------
def p_bib(nr, text):
    return ('<w:p><w:pPr><w:spacing w:after="120" w:line="288" w:lineRule="auto"/><w:ind w:left="567" w:hanging="567"/>'
            '<w:jc w:val="left"/></w:pPr><w:r><w:rPr>%s<w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr><w:t>[%d]</w:t></w:r>'
            '<w:r><w:rPr>%s<w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr><w:tab/></w:r>%s</w:p>') % (
                FONT, nr, FONT, runs(text, size=22))

# ================= build the content =================
API = dict(p=p_normal, bullet=p_bullet, pas=p_pas, titlu=titlu, capitol=capitol, cod=cod, tabel=tabel,
           figura=figura, bib=p_bib, gol=p_gol, pagina_noua=p_pagina_noua, img=lambda n: str(AICI / 'img' / n))
continut_xml = C.construieste(API)

# lists of figures / tables / code (TOC \c fields; Word refreshes them on open)
def lista(eticheta):
    intrari = LEGENDE[eticheta]
    if not intrari:
        return p_normal('(nu este cazul)')
    out = []
    for i, (nr, text, nume) in enumerate(intrari):
        inceput = fld_begin('TOC \\h \\z \\c "%s"' % eticheta) if i == 0 else ''
        sfarsit = '<w:r><w:fldChar w:fldCharType="end"/></w:r>' if i == len(intrari) - 1 else ''
        out.append(('<w:p><w:pPr><w:spacing w:after="60" w:line="288" w:lineRule="auto"/><w:ind w:left="709"/></w:pPr>'
                    '%s<w:hyperlink w:anchor="%s" w:history="1">%s</w:hyperlink>%s</w:p>') % (
                        inceput, nume, runs('%s %d. %s' % (eticheta, nr, text)), sfarsit))
    return ''.join(out)

def fld_begin(instr):
    return ('<w:r><w:fldChar w:fldCharType="begin"/></w:r><w:r><w:instrText xml:space="preserve"> %s </w:instrText></w:r>'
            '<w:r><w:fldChar w:fldCharType="separate"/></w:r>') % escape(instr)

def subtitlu_lista(text):
    return ('<w:p><w:pPr><w:keepNext/><w:spacing w:before="240" w:after="120"/><w:ind w:firstLine="709"/></w:pPr>%s</w:p>'
            % runs('**%s**' % text))

liste_xml = (subtitlu_lista('FIGURI') + lista('Figura') + subtitlu_lista('TABELE') + lista('Tabelul')
             + subtitlu_lista('CODURI SURSĂ') + lista('Codul'))
continut_xml = continut_xml.replace('<!--LISTE-->', liste_xml)

# ---------- cover page ----------
coperta = els[0:32]
set_text(els[15], 'PROIECT')
p16 = copy.deepcopy(els[15]); set_text(p16, 'Programarea Aplicațiilor Internet')
for sz in p16.iter(q('sz'), q('szCs')):
    sz.set(q('val'), '36')
p17 = copy.deepcopy(els[15]); set_text(p17, C.TITLU)
for sz in p17.iter(q('sz'), q('szCs')):
    sz.set(q('val'), '32')
for b in list(p17.iter(q('b'), q('bCs'))):
    b.getparent().remove(b)
coperta[16] = p16
coperta[17] = p17
set_text(els[19], 'Cadru didactic îndrumător:')
set_text(els[20], C.PROFESOR)
set_text(els[23], 'Student:')
set_text(els[24], C.AUTOR)
p25 = copy.deepcopy(els[24]); set_text(p25, C.PROGRAM)
for sz in p25.iter(q('sz'), q('szCs')):
    sz.set(q('val'), '24')
for b in list(p25.iter(q('b'), q('bCs'))):
    b.getparent().remove(b)
coperta[25] = p25
set_text(els[31], 'BRAȘOV, 2026')

# section 1 end (cover): keep its sectPr, drop the form code text
sect1 = els[67]
for r in sect1.findall(q('r')):
    sect1.remove(r)

cuprins_titlu, toc_sdt, dupa_toc = els[68], els[70], els[71]

# ---------- table of contents (cached entries, Word recomputes page numbers on open) ----------
sdt_content = toc_sdt.find(q('sdtContent'))
for child in list(sdt_content):
    sdt_content.remove(child)
toc_ps = []
for i, (nivel, nr, text, nume) in enumerate(TOC_INTRARI):
    stil = 'TOC%d' % nivel
    inceput = fld_begin('TOC \\o "1-3" \\h \\z \\u') if i == 0 else ''
    sfarsit = '<w:r><w:fldChar w:fldCharType="end"/></w:r>' if i == len(TOC_INTRARI) - 1 else ''
    eticheta = (runs(nr) + '<w:r><w:rPr>%s</w:rPr><w:tab/></w:r>' % FONT) if nr else ''
    toc_ps.append('<w:p><w:pPr><w:pStyle w:val="%s"/></w:pPr>%s<w:hyperlink w:anchor="%s" w:history="1">%s%s</w:hyperlink>%s</w:p>'
                  % (stil, inceput, nume, eticheta, runs(text), sfarsit))
for el in frag(''.join(toc_ps)):
    sdt_content.append(el)

# ---------- final section: body pages use section 2's headers and footers ----------
sect2 = None
for el in els[268:280]:
    s = el.find('.//' + q('sectPr'))
    if s is not None:
        sect2 = copy.deepcopy(s)
        break
assert sect2 is not None

for child in list(body):
    body.remove(child)
for el in coperta + [sect1, cuprins_titlu, toc_sdt, dupa_toc]:
    body.append(el)
for el in frag(continut_xml):
    body.append(el)
body.append(sect2)
tree.write(str(doc_path), xml_declaration=True, encoding='UTF-8', standalone=True)

# ---------- headers ----------
for nume, text in (('header1.xml', C.AUTOR), ('header2.xml', C.TITLU_SCURT)):
    hp = LUCRU / 'word' / nume
    ht = etree.parse(str(hp))
    ps = [p for p in ht.getroot().iter(q('p')) if ''.join(p.itertext()).strip()]
    set_text(ps[0], text)
    ht.write(str(hp), xml_declaration=True, encoding='UTF-8', standalone=True)

# ---------- images: files + relationships ----------
rels_path = LUCRU / 'word' / '_rels' / 'document.xml.rels'
rels = rels_path.read_text(encoding='utf-8')
noi = ''
for rid, src, nume in IMAGINI:
    shutil.copy(src, LUCRU / 'word' / 'media' / nume)
    noi += ('<Relationship Id="%s" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" '
            'Target="media/%s"/>' % (rid, nume))
rels_path.write_text(rels.replace('</Relationships>', noi + '</Relationships>'), encoding='utf-8')

# ---------- settings: ask Word to refresh fields (TOC, lists, page numbers) on open ----------
sp = LUCRU / 'word' / 'settings.xml'
s = sp.read_text(encoding='utf-8')
if 'w:updateFields' not in s:
    for ancora in ('<w:hdrShapeDefaults', '<w:footnotePr', '<w:endnotePr', '<w:compat'):
        if ancora in s:
            s = s.replace(ancora, '<w:updateFields w:val="true"/>' + ancora, 1)
            break
sp.write_text(s, encoding='utf-8')

# ---------- document properties ----------
cp = LUCRU / 'docProps' / 'core.xml'
c = cp.read_text(encoding='utf-8')
c = re.sub(r'<dc:creator>.*?</dc:creator>', '<dc:creator>%s</dc:creator>' % escape(C.AUTOR_PROP), c)
c = re.sub(r'<cp:lastModifiedBy>.*?</cp:lastModifiedBy>', '<cp:lastModifiedBy>%s</cp:lastModifiedBy>' % escape(C.AUTOR_PROP), c)
if '<dc:title>' not in c:
    c = c.replace('<dc:creator>', '<dc:title>%s</dc:title><dc:creator>' % escape(C.TITLU), 1)
c = re.sub(r'<dcterms:modified xsi:type="dcterms:W3CDTF">.*?</dcterms:modified>',
           '<dcterms:modified xsi:type="dcterms:W3CDTF">%s</dcterms:modified>' % C.DATA_MODIFICARE, c)
cp.write_text(c, encoding='utf-8')

# ---------- zip ----------
if OUT.exists():
    OUT.unlink()
with zipfile.ZipFile(OUT, 'w', zipfile.ZIP_DEFLATED) as z:
    ct = LUCRU / '[Content_Types].xml'
    z.write(ct, '[Content_Types].xml')
    for f in sorted(LUCRU.rglob('*')):
        if f.is_file() and f != ct:
            z.write(f, f.relative_to(LUCRU).as_posix())
print('ok', OUT, 'figuri', len(LEGENDE['Figura']), 'tabele', len(LEGENDE['Tabelul']), 'coduri', len(LEGENDE['Codul']),
      'titluri', len(TOC_INTRARI))
