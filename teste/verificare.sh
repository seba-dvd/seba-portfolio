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
for f in "$DIR"/www/*.php "$DIR"/www/api/*.php "$DIR"/www/includes/*.php "$DIR"/www/includes/lang/*.php; do
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
# Start with an empty failed-login log, so earlier runs can't trigger the login limit
"$MYSQL" -u root -e "DELETE FROM pai_proiect.incercari_login" 2>/dev/null
J1="$(mktemp)"; J2="$(mktemp)"
trap 'rm -f "$COOKIES" "$J1" "$J2"' EXIT
U="zz_test_$RANDOM$RANDOM"                  # removed again at the end
P="parola-de-test-123"
token() { curl -s -c "$1" -b "$1" "$SITE/login.php" | grep -o 'name="csrf" value="[^"]*"' | head -1 | cut -d'"' -f4; }
trimite() { local jar="$1"; shift; curl -s -c "$jar" -b "$jar" "$@" "$SITE/login.php"; }
sid() { grep 'pai_sesiune' "$1" | awk '{print $7}'; }
are() { printf '%s\n' "$1" | grep -c -F "$2" | sed 's/[2-9]/1/'; }   # 1 if the text is there, 0 if not

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
verifica "harta.php has the map and its data" 2 "$(echo "$HARTA" | grep -c -E 'id="harta"|id="date-harta"')"
verifica "harta.php shows the level, the XP bar and 6 achievements" 1 \
    "$([ "$(printf '%s\n' "$HARTA" | grep -c '<li class="realizare')" = 6 ] \
        && printf '%s\n' "$HARTA" | grep -q '<progress class="bara-xp"' \
        && printf '%s\n' "$HARTA" | grep -q 'class="nivel-insigna"' && echo 1 || echo 0)"
verifica "harta.php has the explore bar (hidden until JavaScript starts it)" 1 \
    "$(printf '%s\n' "$HARTA" | grep -c '<div class="explorare" id="explorare" tabindex="0" hidden')"
verifica "the map data has the distance from home" 1 "$(are "$HARTA" '"distanta_km":')"
verifica "the map library is Leaflet with SRI or Google Maps" 1 \
    "$(echo "$HARTA" | grep -c -E 'leaflet\.js" integrity="sha256-|maps\.googleapis\.com' | sed 's/[2-9]/1/')"
# A description with HTML in the database must stay text, in the list and in the JSON for the map
"$MYSQL" -u root -e "INSERT INTO pai_proiect.points (ID, lat, \`long\`, description)
    VALUES (999999, 45, 25, '<img src=x onerror=alert(1)>')" 2>/dev/null
HARTA_XSS=$(curl -s -c "$J1" -b "$J1" "$SITE/harta.php")
verifica "a description with HTML is escaped in the list" 1 "$(are "$HARTA_XSS" '&lt;img src=x onerror=alert(1)&gt;')"
verifica "… and in the map JSON (\u003C)" 1 "$(are "$HARTA_XSS" '\u003Cimg src=x')"
verifica "… and appears nowhere as a real tag" 0 "$(printf '%s\n' "$HARTA_XSS" | grep -c '<img src=x')"
"$MYSQL" -u root -e "DELETE FROM pai_proiect.points WHERE ID = 999999" 2>/dev/null
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

echo "10. Autocomplete (api/utilizatori.php)"
API="$SITE/api/utilizatori.php"
numara() { "$PHP" -r 'echo count(json_decode(stream_get_contents(STDIN)) ?? [9999]);'; }
# 12 names with the same start, and one where "_" is another letter (zzXtest_...), to test the LIKE escaping
"$MYSQL" -u root -e "INSERT INTO pai_proiect.users (user, password) VALUES
    $(for i in 01 02 03 04 05 06 07 08 09 10 11 12; do printf "('zz_test_lim_%s','x')," $i; done) ('zzXtest_ac','x')" 2>/dev/null

verifica "answers JSON" "application/json; charset=utf-8" "$(curl -s -o /dev/null -w '%{content_type}' "$API?term=zz")"
verifica "has X-Content-Type-Options: nosniff" 1 "$(curl -s -D - -o /dev/null "$API?term=zz" | grep -c -i '^x-content-type-options: nosniff')"
verifica "the start of a name finds it" 1 "$(are "$(curl -s "$API?term=${U:0:12}")" "\"$U\"")"
verifica "names in other case are found too" 1 "$(are "$(curl -s "$API?term=$(echo "${U:0:12}" | tr a-z A-Z)")" "\"$U\"")"
verifica "at most 10 names" 10 "$(curl -s "$API?term=zz_test_lim" | numara)"
verifica "one letter gives nothing" '[]' "$(curl -s "$API?term=z")"
verifica "\"_\" is a plain character, not a wildcard" 0 "$(curl -s "$API?term=zz_test_ac" | grep -c 'zzXtest')"
for v in "%25%25" "__" "zz%27%20OR%20%271%27%3D%271" "%3Cscript%3E" ""; do
    verifica "term=$v gives nothing" '[]' "$(curl -s "$API?term=$v")"
done
verifica "term[]=zz (array) gives nothing" '[]' "$(curl -s "$API?term[]=zz")"
verifica "login.php loads jQuery UI with an SRI hash" 2 \
    "$(curl -s "$SITE/login.php" | grep -c 'code.jquery.com.*integrity="sha256-')"

echo "11. Admin panel"
# $J1 is still logged in as the ordinary test user $U
verifica "admin.php without login → login.php" 303 "$(cod "$SITE/admin.php")"
verifica "admin.php for an ordinary user → 403" 403 "$(cod -c "$J1" -b "$J1" "$SITE/admin.php")"
verifica "no admin link on the map for an ordinary user" 0 "$(curl -s -c "$J1" -b "$J1" "$SITE/harta.php" | grep -c 'href="admin.php"')"
TU=$(curl -s -c "$J1" -b "$J1" "$SITE/harta.php" | grep -o 'logout.php?token=[a-f0-9]*' | head -1 | cut -d= -f2)
verifica "an ordinary user can't delete a place (POST → 403)" 403 "$(cod -c "$J1" -b "$J1" \
    -d actiune=sterge_loc -d id=1 -d "csrf=$TU" "$SITE/admin.php")"
verifica "… and the place is still there" 1 "$("$MYSQL" -u root -N -e "SELECT COUNT(*) FROM pai_proiect.points WHERE ID = 1" 2>/dev/null)"
verifica "403.php answers 403" 403 "$(cod "$SITE/403.php")"

# The full flow needs an admin account (listed in config.php 'admini'):
#     ADMIN_USER=seba ADMIN_PASS='...' bash teste/verificare.sh
if [ -n "$ADMIN_USER" ] && [ -n "$ADMIN_PASS" ]; then
    J3="$(mktemp)"
    T3=$(token "$J3")
    # Create the account if it doesn't exist yet (an existing one just gives "taken")
    trimite "$J3" -o /dev/null -d actiune=inregistrare -d "csrf=$T3" --data-urlencode "user=$ADMIN_USER" \
        --data-urlencode "password=$ADMIN_PASS" --data-urlencode "password2=$ADMIN_PASS"
    if [ "$(cod -c "$J3" -b "$J3" "$SITE/harta.php")" != 200 ]; then
        T3=$(token "$J3")
        trimite "$J3" -o /dev/null -d actiune=login -d "csrf=$T3" --data-urlencode "user=$ADMIN_USER" --data-urlencode "password=$ADMIN_PASS"
    fi
    verifica "admin.php for an admin → 200" 200 "$(cod -c "$J3" -b "$J3" "$SITE/admin.php")"
    verifica "the map shows the admin link to an admin" 1 "$(curl -s -c "$J3" -b "$J3" "$SITE/harta.php" | grep -c 'href="admin.php"')"
    TA=$(curl -s -c "$J3" -b "$J3" "$SITE/admin.php" | grep -o 'name="csrf" value="[^"]*"' | head -1 | cut -d'"' -f4)
    admin_post() { curl -s -c "$J3" -b "$J3" "$@" "$SITE/admin.php"; }

    verifica "a change without CSRF token is refused" 1 "$(are "$(admin_post -d actiune=salveaza_loc -d lat=45 -d long=25 \
        -d description=zz_test_loc)" 'Formularul a expirat')"
    verifica "latitude 100 is refused" 1 "$(are "$(admin_post -d actiune=salveaza_loc -d "csrf=$TA" -d lat=100 -d long=25 \
        -d description=zz_test_loc)" 'Latitudinea trebuie')"
    verifica "an empty description is refused" 1 "$(are "$(admin_post -d actiune=salveaza_loc -d "csrf=$TA" -d lat=45 -d long=25 \
        -d description=)" 'Descrierea trebuie')"
    verifica "add a place → 303" 303 "$(admin_post -o /dev/null -w '%{http_code}' -d actiune=salveaza_loc -d "csrf=$TA" \
        -d lat=45,5 -d long=25.1 --data-urlencode 'description=zz_test_loc <b>x</b>')"
    IDL=$("$MYSQL" -u root -N -e "SELECT ID FROM pai_proiect.points WHERE description LIKE 'zz\\_test\\_loc%'" 2>/dev/null)
    verifica "… it is in the database (a comma works as decimal point)" "45.500000" \
        "$("$MYSQL" -u root -N -e "SELECT lat FROM pai_proiect.points WHERE ID = '$IDL'" 2>/dev/null)"
    verifica "… and shown escaped in the panel" 1 "$(are "$(admin_post)" 'zz_test_loc &lt;b&gt;x&lt;/b&gt;')"
    verifica "edit the place → 303" 303 "$(admin_post -o /dev/null -w '%{http_code}' -d actiune=salveaza_loc -d "csrf=$TA" \
        -d "id=$IDL" -d lat=46 -d long=24 -d description=zz_test_loc_modificat)"
    verifica "… the change is saved" "zz_test_loc_modificat" \
        "$("$MYSQL" -u root -N -e "SELECT description FROM pai_proiect.points WHERE ID = '$IDL'" 2>/dev/null)"
    verifica "delete the place → 303" 303 "$(admin_post -o /dev/null -w '%{http_code}' -d actiune=sterge_loc -d "csrf=$TA" -d "id=$IDL")"
    verifica "… it is gone" 0 "$("$MYSQL" -u root -N -e "SELECT COUNT(*) FROM pai_proiect.points WHERE ID = '$IDL'" 2>/dev/null)"

    IDA=$("$MYSQL" -u root -N -e "SELECT ID FROM pai_proiect.users WHERE user = '$ADMIN_USER'" 2>/dev/null)
    admin_post -o /dev/null -d actiune=sterge_utilizator -d "csrf=$TA" -d "id=$IDA"
    verifica "an admin account can't be deleted" 1 "$("$MYSQL" -u root -N -e "SELECT COUNT(*) FROM pai_proiect.users WHERE ID = '$IDA'" 2>/dev/null)"
    IDU=$("$MYSQL" -u root -N -e "SELECT ID FROM pai_proiect.users WHERE user = '$U'" 2>/dev/null)
    verifica "delete an ordinary user → 303" 303 "$(admin_post -o /dev/null -w '%{http_code}' -d actiune=sterge_utilizator \
        -d "csrf=$TA" -d "id=$IDU")"
    verifica "… the deleted user is logged out" 303 "$(cod -c "$J1" -b "$J1" "$SITE/harta.php")"
    rm -f "$J3"
    "$MYSQL" -u root -e "DELETE FROM pai_proiect.points WHERE description LIKE 'zz\\_test\\_loc%'" 2>/dev/null
else
    echo "  (skipped the admin flow: set ADMIN_USER and ADMIN_PASS to an admin account to run it)"
fi

"$MYSQL" -u root -e "DELETE FROM pai_proiect.users WHERE user LIKE 'zz\\_test\\_%' OR user = 'zzXtest_ac'" 2>/dev/null \
    || echo "  (could not remove the test users: run as root: DELETE FROM pai_proiect.users WHERE user LIKE 'zz\\_test\\_%')"

# 12. Database (teste/verificare-bd.php prints its own ok / FAIL lines)
IESIRE_BD=$("$PHP" "$DIR/teste/verificare-bd.php" 2>&1)
echo "$IESIRE_BD" | grep -v '^Database passed' | sed '/^$/d'
TRECUTE=$((TRECUTE + $(echo "$IESIRE_BD" | grep -c '^  ok ')))
PICATE=$((PICATE + $(echo "$IESIRE_BD" | grep -c '^  FAIL ')))
if ! echo "$IESIRE_BD" | grep -q '^Database passed'; then
    PICATE=$((PICATE + 1))            # the script stopped early (MySQL off, no config.php)
    echo "  FAIL  database checks did not run (is MySQL started?)"
fi

# 13. Travel progress (teste/verificare-progres.php, no server needed)
IESIRE_PROGRES=$("$PHP" "$DIR/teste/verificare-progres.php" 2>&1)
echo "$IESIRE_PROGRES" | grep -v '^Progress passed' | sed '/^$/d'
TRECUTE=$((TRECUTE + $(echo "$IESIRE_PROGRES" | grep -c '^  ok ')))
PICATE=$((PICATE + $(echo "$IESIRE_PROGRES" | grep -c '^  FAIL ')))
if ! echo "$IESIRE_PROGRES" | grep -q '^Progress passed'; then
    PICATE=$((PICATE + 1))
    echo "  FAIL  progress checks did not run"
fi

echo "14. Clean design: home page, About page, footer"
ACASA=$(curl -s "$SITE/index.php")
ACASA_EN=$(curl -s -b 'lang=en' "$SITE/index.php")
DESPRE=$(curl -s "$SITE/despre.php")
CONTACT=$(curl -s "$SITE/contact.php")
CV=$(curl -s "$SITE/cv.php")
verifica "home: the full greeting is in the HTML (works without JS)" 1 "$(are "$ACASA" 'Salut, sunt Seba!')"
verifica "home (EN): the greeting is translated" 1 "$(are "$ACASA_EN" 'Hi, I&#039;m Seba!')"
verifica "home: the typewriter script is loaded" 1 "$(are "$ACASA" 'src="js/acasa.js"')"
verifica "home: one link onward, to the About page" 1 "$(are "$ACASA" 'class="link-parcurs" href="despre.php"')"
verifica "home: the GitHub icon link" 1 "$(are "$ACASA" 'aria-label="GitHub"')"
verifica "home: no bento grid any more" 0 "$(are "$ACASA" 'class="bento"')"
verifica "about: the typewriter script is not loaded" 0 "$(are "$DESPRE" 'js/acasa.js')"
verifica "about: four info tiles" 4 "$(printf '%s\n' "$DESPRE" | grep -c 'class="card info-tile"')"
verifica "about: the profile photo" 1 "$(are "$DESPRE" 'src="img/profil-rotund.jpg"')"
verifica "about: the timeline, one card per step" 1 "$(are "$DESPRE" 'class="timeline-carduri"')"
verifica "about: link cards to the projects and the map" 2 \
    "$(printf '%s\n' "$DESPRE" | grep -c -E 'class="card card-link[^"]*" href="(proiecte|harta)\.php"')"
for img in avatar.jpg profil-rotund.jpg; do
    verifica "img/$img is served" 200 "$(cod "$SITE/img/$img")"
done
for p in index.php despre.php proiecte.php contact.php cv.php nu-exista.php; do
    PAGINA=$(curl -s "$SITE/$p")
    verifica "$p: footer menu has all 6 links" 6 \
        "$(printf '%s\n' "$PAGINA" | sed -n '/class="subsol-meniu"/,/<\/nav>/p' | grep -c '<a href=')"
done
verifica "footer menu in English" 1 \
    "$(curl -s -b 'lang=en' "$SITE/despre.php" | sed -n '/class="subsol-meniu"/,/<\/nav>/p' | grep -c '>About<')"
for pagina in ACASA CONTACT CV; do
    verifica "${pagina,,}: no [EMAIL] / [LINKEDIN] placeholders" 0 \
        "$(printf '%s\n' "${!pagina}" | grep -c -E '\[(EMAIL|LINKEDIN)\]')"
done
CONTACTE=$("$PHP" -r 'require $argv[1]; echo count(linkuriContact()) + 1;' "$DIR/www/includes/contacte.php")
verifica "contact: a card per filled-in contact + location" "$CONTACTE" \
    "$(printf '%s\n' "$CONTACT" | grep -c 'class="card contact-card"')"

echo "15. Limit on repeated logins (includes/limitare.php)"
"$MYSQL" -u root -e "DELETE FROM pai_proiect.incercari_login" 2>/dev/null
J3="$(mktemp)"; J4="$(mktemp)"; J5="$(mktemp)"
U2="zz_test_lim_$RANDOM"                    # removed again at the end of this section
cheie() { printf '%s' "$1" | sha256sum | cut -d' ' -f1; }     # like cheiIncercare() in PHP
esecuri() { "$MYSQL" -u root -N -e "SELECT COUNT(*) FROM pai_proiect.incercari_login WHERE cheie = '$(cheie "nume:$1")'" 2>/dev/null; }
greseste() { trimite "$1" -o /dev/null -w '%{http_code}' -d actiune=login -d "csrf=$2" --data-urlencode "user=$3" -d 'password=gresita'; }

T=$(token "$J3")
verifica "a user for these checks" "303 harta.php" "$(trimite "$J3" -o /dev/null -w '%{http_code} %{redirect_url}' \
    -d actiune=inregistrare -d "csrf=$T" --data-urlencode "user=$U2" -d "password=$P" -d "password2=$P" | sed 's#http[^ ]*/##')"

T=$(token "$J4")
greseste "$J4" "$T" "$U2" >/dev/null; greseste "$J4" "$T" "$U2" >/dev/null
verifica "2 wrong passwords are counted for the name" 2 "$(esecuri "$U2")"
verifica "a correct login still works → harta.php" "303 harta.php" "$(trimite "$J4" -o /dev/null -w '%{http_code} %{redirect_url}' \
    -d actiune=login -d "csrf=$T" --data-urlencode "user=$U2" -d "password=$P" | sed 's#http[^ ]*/##')"
verifica "… and clears the name's failures" 0 "$(esecuri "$U2")"

T=$(token "$J5")
CODURI=""
for i in 1 2 3 4 5; do CODURI="$CODURI $(greseste "$J5" "$T" "$U2")"; done
verifica "5 wrong passwords get the normal answer (200)" " 200 200 200 200 200" "$CODURI"
R=$(trimite "$J5" -D - -d actiune=login -d "csrf=$T" --data-urlencode "user=$U2" -d "password=$P")
verifica "the 6th attempt, even with the right password: 429" 1 "$(printf '%s\n' "$R" | grep -c '^HTTP/1.1 429')"
verifica "… says 'Prea multe încercări greșite'" 1 "$(are "$R" 'Prea multe încercări greșite')"
verifica "… sends Retry-After: 900 (15 minutes)" 1 "$(printf '%s\n' "$R" | grep -c -i '^retry-after: 900')"
verifica "… and does not log in (harta.php → login.php)" 303 "$(cod -c "$J5" -b "$J5" "$SITE/harta.php")"
N="zz_test_nimeni_$RANDOM"
for i in 1 2 3 4 5; do greseste "$J5" "$T" "$N" >/dev/null; done
verifica "an unknown name is blocked the same way (nothing revealed)" 429 "$(greseste "$J5" "$T" "$N")"
verifica "another name from the same address still gets the normal answer" 200 "$(greseste "$J5" "$T" zz_test_altul)"
for i in 1 2 3 4 5 6 7; do greseste "$J5" "$T" "zz_test_ip_$i" >/dev/null; done      # 20 failures from this address
verifica "after 20 failures from one address, every name is blocked" 429 "$(greseste "$J5" "$T" zz_test_ip_nou)"
verifica "the log holds hashes, not names" 0 \
    "$("$MYSQL" -u root -N -e "SELECT COUNT(*) FROM pai_proiect.incercari_login WHERE cheie LIKE '%zz%'" 2>/dev/null)"
verifica "… 64 hex characters each" 0 \
    "$("$MYSQL" -u root -N -e "SELECT COUNT(*) FROM pai_proiect.incercari_login WHERE cheie NOT REGEXP '^[0-9a-f]{64}\$'" 2>/dev/null)"
"$MYSQL" -u root -e "DELETE FROM pai_proiect.incercari_login; DELETE FROM pai_proiect.users WHERE user = '$U2'" 2>/dev/null
rm -f "$J3" "$J4" "$J5"

echo
echo "Passed: $TRECUTE   Failed: $PICATE"
[ "$PICATE" -eq 0 ]
