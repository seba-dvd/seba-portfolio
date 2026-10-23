#!/usr/bin/env bash
# Smoke tests for the site: pages, languages, 404s, security rules and the database.
#
# Needs: Apache and MySQL running (XAMPP) with the site at http://localhost/pai/, curl, php,
# the database imported from sql/ and www/includes/config.php (see README).
# Run from the project folder, in Git Bash:
#     bash teste/verificare.sh
# Another address:  SITE=http://localhost:8080/pai bash teste/verificare.sh

SITE="${SITE:-http://localhost/pai}"
DIR="$(cd "$(dirname "$0")/.." && pwd)"
PHP="${PHP:-php}"
command -v "$PHP" >/dev/null 2>&1 || PHP=/c/xampp/php/php.exe

TRECUTE=0
PICATE=0
COOKIES="$(mktemp)"
trap 'rm -f "$COOKIES"' EXIT

verifica() {   # verifica "description" "expected" "actual"
    if [ "$2" = "$3" ]; then
        TRECUTE=$((TRECUTE + 1))
        printf '  ok    %s\n' "$1"
    else
        PICATE=$((PICATE + 1))
        printf '  FAIL  %s (expected: %s, got: %s)\n' "$1" "$2" "$3"
    fi
}

cod()   { curl -s -o /dev/null -w '%{http_code}' "$@"; }
limba() { curl -s "$@" | grep -o '<html lang="[a-z]*"' | head -1 | cut -d'"' -f2; }

if [ "$(cod "$SITE/")" = "000" ]; then
    echo "The site does not answer at $SITE/. Start Apache in XAMPP first."
    exit 2
fi

echo "1. PHP syntax"
for f in "$DIR"/www/*.php "$DIR"/www/includes/*.php "$DIR"/www/includes/lang/*.php; do
    "$PHP" -l "$f" >/dev/null 2>&1
    verifica "${f#"$DIR"/}" 0 $?
done

echo "2. Both dictionaries have the same keys"
DIF=$("$PHP" -r '
    function chei($a, $p = "") { $r = []; foreach ($a as $k => $v) { $c = "$p/$k"; $r[] = $c;
        if (is_array($v) && !array_is_list($v)) $r = array_merge($r, chei($v, $c)); } return $r; }
    $ro = chei(require $argv[1]); $en = chei(require $argv[2]);
    echo count(array_diff($ro, $en)) + count(array_diff($en, $ro));
' "$DIR/www/includes/lang/ro.php" "$DIR/www/includes/lang/en.php")
verifica "missing keys in ro.php / en.php" 0 "$DIF"

echo "3. Pages answer"
for p in "" index.php despre.php proiecte.php cv.php contact.php "proiect.php?id=1" "proiect.php?id=8"; do
    verifica "/$p" 200 "$(cod "$SITE/$p")"
done

echo "4. Missing pages and bad ids give 404"
for p in nu-exista.php proiect.php "proiect.php?id=0" "proiect.php?id=-1" "proiect.php?id=999" \
         "proiect.php?id=abc" "proiect.php?id=%3Cscript%3E"; do
    verifica "/$p" 404 "$(cod "$SITE/$p")"
done

echo "5. Private folders are blocked (403)"
for p in includes/ includes/functii.php includes/lang/ro.php includes/db.php includes/config.php \
         includes/config.example.php css/ js/ img/ .htaccess; do
    verifica "/$p" 403 "$(cod "$SITE/$p")"
done

echo "6. Language switch"
verifica "default language" ro "$(limba "$SITE/")"
verifica "?lang=en redirects (303)" 303 "$(cod -c "$COOKIES" "$SITE/index.php?lang=en")"
verifica "the cookie keeps English" en "$(limba -b "$COOKIES" "$SITE/despre.php")"
verifica "?lang=en keeps ?id=" "proiect.php?id=4" \
    "$(curl -s -o /dev/null -w '%{redirect_url}' "$SITE/proiect.php?id=4&lang=en" | sed 's#.*/##')"
COOKIE=$(curl -s -D - -o /dev/null "$SITE/index.php?lang=en" | grep -i '^set-cookie')
verifica "cookie is HttpOnly"       1 "$(echo "$COOKIE" | grep -c -i 'httponly')"
verifica "cookie is SameSite=Lax"   1 "$(echo "$COOKIE" | grep -c -i 'samesite=lax')"

echo "7. Bad language values are ignored (page stays Romanian)"
for v in "../../../xampp/apache/conf/httpd.conf" "en%00" "EN" "fr" "%3Cscript%3E"; do
    verifica "?lang=$v" ro "$(limba "$SITE/index.php?lang=$v")"
done
verifica "forged cookie lang=../../x" ro "$(limba -b 'lang=../../x' "$SITE/")"

echo "8. Output is escaped"
verifica "no raw <script> from the address" 0 \
    "$(curl -s "$SITE/proiect.php?id=4&x=%3Cscript%3Ealert(1)%3C/script%3E" | grep -c '<script>alert')"

# 9. Database (teste/verificare-bd.php prints its own ok / FAIL lines)
IESIRE_BD=$("$PHP" "$DIR/teste/verificare-bd.php" 2>&1)
echo "$IESIRE_BD" | grep -v '^Database passed' | sed '/^$/d'
TRECUTE=$((TRECUTE + $(echo "$IESIRE_BD" | grep -c '^  ok ')))
PICATE=$((PICATE + $(echo "$IESIRE_BD" | grep -c '^  FAIL ')))
if ! echo "$IESIRE_BD" | grep -q '^Database passed'; then
    PICATE=$((PICATE + 1))            # the script stopped early (MySQL off, no config.php)
    echo "  FAIL  database checks did not run (is MySQL started?)"
fi

echo
echo "Passed: $TRECUTE   Failed: $PICATE"
[ "$PICATE" -eq 0 ]
