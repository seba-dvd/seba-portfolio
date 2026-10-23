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

echo "9. Accounts and sessions"
MYSQL="${MYSQL:-/c/xampp/mysql/bin/mysql.exe}"
J1="$(mktemp)"; J2="$(mktemp)"
trap 'rm -f "$COOKIES" "$J1" "$J2"' EXIT
U="zz_test_$RANDOM$RANDOM"                  # removed again at the end
P="parola-de-test-123"
token() { curl -s -c "$1" -b "$1" "$SITE/login.php" | grep -o 'name="csrf" value="[^"]*"' | head -1 | cut -d'"' -f4; }
trimite() { local jar="$1"; shift; curl -s -c "$jar" -b "$jar" "$@" "$SITE/login.php"; }
sid() { grep 'pai_sesiune' "$1" | awk '{print $7}'; }
are() { echo "$1" | grep -c -F "$2" | sed 's/[2-9]/1/'; }   # 1 if the text is there, 0 if not

verifica "login.php answers" 200 "$(cod "$SITE/login.php")"
verifica "harta.php without login → login.php" "303 login.php" \
    "$(curl -s -o /dev/null -w '%{http_code} %{redirect_url}' "$SITE/harta.php" | sed 's#http[^ ]*/##')"
COOKIE=$(curl -s -D - -o /dev/null "$SITE/login.php" | grep -i '^set-cookie: pai_sesiune')
verifica "session cookie is HttpOnly"     1 "$(echo "$COOKIE" | grep -c -i 'httponly')"
verifica "session cookie is SameSite=Lax" 1 "$(echo "$COOKIE" | grep -c -i 'samesite=lax')"
verifica "personal site pages set no session cookie" 0 \
    "$(curl -s -D - -o /dev/null "$SITE/index.php" | grep -c -i 'pai_sesiune')"

T=$(token "$J1")
verifica "register without CSRF token is refused" 1 "$(are "$(trimite "$J1" -d actiune=inregistrare \
    --data-urlencode "user=$U" -d "password=$P" -d "password2=$P")" 'Formularul a expirat')"
verifica "register name <b>x</b> is refused" 1 "$(are "$(trimite "$J1" -d actiune=inregistrare -d "csrf=$T" \
    --data-urlencode 'user=<b>x</b>' -d "password=$P" -d "password2=$P")" 'Numele de utilizator trebuie')"
verifica "register password of 7 characters is refused" 1 "$(are "$(trimite "$J1" -d actiune=inregistrare -d "csrf=$T" \
    --data-urlencode "user=$U" -d 'password=1234567' -d 'password2=1234567')" 'Parola trebuie')"
verifica "register with different passwords is refused" 1 "$(are "$(trimite "$J1" -d actiune=inregistrare -d "csrf=$T" \
    --data-urlencode "user=$U" -d "password=$P" -d "password2=${P}x")" 'nu coincid')"

INAINTE=$(sid "$J1")
verifica "register a new user → harta.php" "303 harta.php" "$(trimite "$J1" -o /dev/null -w '%{http_code} %{redirect_url}' \
    -d actiune=inregistrare -d "csrf=$T" --data-urlencode "user=$U" -d "password=$P" -d "password2=$P" | sed 's#http[^ ]*/##')"
verifica "new session id after register" 1 "$([ -n "$INAINTE" ] && [ "$INAINTE" != "$(sid "$J1")" ] && echo 1 || echo 0)"
HARTA=$(curl -s -D - -c "$J1" -b "$J1" "$SITE/harta.php")
verifica "harta.php greets the user" 1 "$(are "$HARTA" "Bine ai venit, $U!")"
verifica "harta.php is not cached (no-store)" 1 "$(echo "$HARTA" | grep -c -i '^cache-control: no-store')"
verifica "the password is stored as a bcrypt hash" '$2y$' \
    "$("$MYSQL" -u root -N -e "SELECT LEFT(password, 4) FROM pai_proiect.users WHERE user = '$U'" 2>/dev/null)"

T2=$(token "$J2")
verifica "the same name twice is refused" 1 "$(are "$(trimite "$J2" -d actiune=inregistrare -d "csrf=$T2" \
    --data-urlencode "user=$U" -d "password=$P" -d "password2=$P")" 'deja folosit')"
verifica "the same name in capitals is refused" 1 "$(are "$(trimite "$J2" -d actiune=inregistrare -d "csrf=$T2" \
    --data-urlencode "user=${U^^}" -d "password=$P" -d "password2=$P")" 'deja folosit')"

verifica "logout without token does nothing" 200 "$(curl -s -o /dev/null -c "$J1" -b "$J1" -L -w '%{http_code}' "$SITE/logout.php")"
verifica "… the user is still logged in" 1 "$(are "$(curl -s -c "$J1" -b "$J1" "$SITE/harta.php")" "Bine ai venit")"
LT=$(curl -s -c "$J1" -b "$J1" "$SITE/harta.php" | grep -o 'logout.php?token=[a-f0-9]*' | head -1)
verifica "logout with token → login.php" "303 login.php?iesire=1" \
    "$(curl -s -o /dev/null -c "$J1" -b "$J1" -w '%{http_code} %{redirect_url}' "$SITE/$LT" | sed 's#http[^ ]*/##')"
verifica "after logout harta.php → login.php" 303 "$(cod -c "$J1" -b "$J1" "$SITE/harta.php")"

T=$(token "$J1")
verifica "login with a wrong password is refused" 1 "$(are "$(trimite "$J1" -d actiune=login -d "csrf=$T" \
    --data-urlencode "user=$U" -d 'password=gresita')" 'Utilizator sau parol')"
verifica "login with an unknown name: same message" 1 "$(are "$(trimite "$J1" -d actiune=login -d "csrf=$T" \
    -d 'user=nu_exista_deloc' -d "password=$P")" 'Utilizator sau parol')"
verifica "login with x' OR '1'='1 is refused" 1 "$(are "$(trimite "$J1" -d actiune=login -d "csrf=$T" \
    --data-urlencode "user=x' OR '1'='1" --data-urlencode "password=x' OR '1'='1")" 'Utilizator sau parol')"
INAINTE=$(sid "$J1")
verifica "login with the right password → harta.php" "303 harta.php" "$(trimite "$J1" -o /dev/null -w '%{http_code} %{redirect_url}' \
    -d actiune=login -d "csrf=$T" --data-urlencode "user=$U" -d "password=$P" | sed 's#http[^ ]*/##')"
verifica "new session id after login" 1 "$([ "$INAINTE" != "$(sid "$J1")" ] && echo 1 || echo 0)"
verifica "login.php while logged in → harta.php" 303 "$(cod -c "$J1" -b "$J1" "$SITE/login.php")"

"$MYSQL" -u root -e "DELETE FROM pai_proiect.users WHERE user LIKE 'zz\\_test\\_%'" 2>/dev/null \
    || echo "  (could not remove the test users: run as root: DELETE FROM pai_proiect.users WHERE user LIKE 'zz\\_test\\_%')"

# 10. Database (teste/verificare-bd.php prints its own ok / FAIL lines)
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
