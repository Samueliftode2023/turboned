var coduriApatrid = {'none':'nu','0':'Cu drept de sedere in alta tara decat Romania','1':'Cu drept de sedera pe termen lung in Romania'}
var coduriCarte = {'0':'Carte de identitate','1':'Pasaport','2':'Buletin de identitate','3':'Pasaport','4':'Carte rezidentiala','5':'Permis de sedere','6':'Alt tip de act identitate romanesc','7':'Alt tip act identitate (apatrid tolerat)'}
var coduriAviz = {'1':'Exceptie','7':'Lucrator inalt calificat','8':'Lucratori au pair','0':'Lucratori permanenti','2':'Lucratori sezonieri','3':'Lucratori stagiari','6':'Lucratori transfrontalieri'}
var preventSign = 0;

function logOut(ids){
    var idul = ids.id
    var root = idul.split('||')
    root = root[1]
    if(preventSign == 0){
        preventSign = 1
        $.ajax({
            type: "POST",
            url: root + "included/function/exe/logout.php",
            data: {
                'logout':'1'
            },
            success: function (result) {
                location.reload();
            }
        })
    }
}

function stergeSpatiiInceput(str) {
    let index = 0;
    while (str[index] === ' ' && index < str.length) {
        index++;
    }
    return str.substring(index);
}

function cleanSpaces(str) {
    // Elimină spațiile de la început și sfârșit
    str = str.trim();
    // Înlocuiește spațiile multiple cu un singur spațiu
    return str.replace(/\s+/g, ' ');
}

function closeDialog(){
    document.getElementById('dialog').classList.add('dis-none')
}

function areCaractereInterzise(text) {
    // Definim lista de caractere interzise
    var caractereInterzise = ['<', '>', ';', "'", ',', ':', '"', '/', '\\', '|', '?', '*', '.'];

    // Parcurgem fiecare caracter din text
    for (var i = 0; i < text.length; i++) {
        // Verificăm dacă caracterul curent este în lista de caractere interzise
        if (caractereInterzise.includes(text[i])) {
            // Dacă găsim un caracter interzis, returnăm true (textul are caractere interzise)
            return true;
        }
    }

    // Dacă nu găsim niciun caracter interzis, returnăm false (textul nu are caractere interzise)
    return false;
}

function removeAllEvents(element) {
    var eventTypes = ['click', 'mouseover', 'mouseout','onfocus','focusout','keydown','paste'];

    eventTypes.forEach(function(eventType) {
        element.removeEventListener(eventType, function() {
        });
    });
}