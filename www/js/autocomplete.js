// Autocomplete on the username field of the login form (M6), with jQuery UI.
// As the visitor types, jQuery UI asks api/utilizatori.php?term=... and shows
// the names it gets back. It writes them with .text(), so a name can't inject HTML.

$(function () {
    $('#login-user').autocomplete({
        source: 'api/utilizatori.php',
        minLength: 2,          // same limit as the server: no list for a single letter
        delay: 250,            // wait for a short pause in typing, not one request per key
        select: function () {
            // A name was picked: the password is next
            setTimeout(function () { $('#login-parola').trigger('focus'); }, 0);
        }
    });
});
