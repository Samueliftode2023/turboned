var preventServer = 0
var angajatObiect = ''
var conditiePentruTipActApatrid = 1

function listaAngajati(selectEl,mode) {
    if(preventServer == 0){
        preventServer = 1
        $('#loading-id').removeClass('dis-none')
        $.ajax({
            url: '../../../included/function/exe/edit-employee.php',
            type: 'POST',
            data: {'lista-angajati':'read','selecteaza':selectEl},
            success: function(result) {
                result = '<option value="" disabled selected hidden>Salariati</option>' + stergeSpatiiInceput(result)
                $('#angajat').html(result)
                preventServer = 0

                if(mode == 'get-info'){
                    readEmployee(selectEl, 'no')
                }

                setTimeout(function() {
                    $('#loading-id').addClass('dis-none');
                }, 1000);
            },
            error: function(result){
                preventServer = 0
                setTimeout(function() {
                    $('#loading-id').addClass('dis-none');
                }, 1000);
            }
        })
    }
}

function uploadPhoto(el){
    closeDelete()
    if(preventServer == 0){
        $('#loading-id').removeClass('dis-none')
        preventServer = 1
        var imagine = el.files[0];
        var formData = new FormData();
        formData.append('file', imagine);
        formData.append('photo-access', 'true');
        formData.append('id', angajatObiect['id']);

        $.ajax({
            url: '../../../included/function/exe/edit-employee.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(result) {
                result = stergeSpatiiInceput(result)
                result = result.slice(0, -1)
                var imageRespons = result.split(':')
                if(imageRespons[0] == 'SUCCESS'){
                    photoUser = imageRespons[1]
                    var folderEmployee = '../../../included/gallery/employee/'
                    imageRespons = "url('" + folderEmployee + imageRespons[1] + "')"
                    $('#imagine-salariat').css('background-image', imageRespons);
                }
                else if(imageRespons[0] == 'ERROR'){
                    $('#dialog').removeClass('dis-none')
                    $('#dialog-mess').text(imageRespons[1]);
                }
                preventServer = 0
                setTimeout(function() {
                    $('#loading-id').addClass('dis-none');
                }, 1000);
            },
            error: function(result) {
                preventServer = 0
                setTimeout(function() {
                    $('#loading-id').addClass('dis-none');
                }, 1000);
            }
        });
    }
}

function readEmployee(el, mode){
    closeDelete()
    if(preventServer == 0){
        closeEditor()
        el.disabled = true
        $('#loading-id').removeClass('dis-none')
        preventServer = 1
        angajatObiect = ''
        var angajat = el

        if(mode == 'start'){
            angajat = el.value
        }

        $('#cititor-angajati').addClass('dis-none')
        $('#cititor-angajati').html('')
        $.ajax({
            url: '../../../included/function/exe/edit-employee.php',
            type: 'POST',
            data: {'angajat':angajat},
            success: function(result) {
                result = stergeSpatiiInceput(result)
                result = result.split('||')
                if(result[0] == 'ok'){
                    $('#cititor-angajati').removeClass('dis-none')
                    $('#delete-angajat').removeClass('dis-none')
                    $('#new-photo').removeClass('dis-none')
                    
                    angajatObiect = JSON.parse(result[1])
                    for (var coloana in angajatObiect) {
                        var editButon = '<span class="material-symbols-outlined editButon">edit</span>'
                        var numeLabel = coloana.replace(/_/g, ' ')
                        var valoareInput = angajatObiect[coloana]

                        if(coloana == 'tip_aviz'){
                            if(valoareInput in coduriAviz){
                                valoareInput = coduriAviz[valoareInput]
                            }
                        }

                        if(coloana == 'tip_act'){
                            if(valoareInput in coduriCarte){
                                valoareInput = coduriCarte[valoareInput]
                            }
                        }

                        if(coloana == 'apatrid'){
                            if(valoareInput in coduriApatrid){
                                valoareInput = coduriApatrid[valoareInput]
                            }
                        }

                        var compunereCamp = '<div onclick="editareCampuri(this)" id="' + coloana + '" class="editor-camp">' + '<h3 class="titlu-date">' + numeLabel + '</h3><div class="paragraf-date">' + valoareInput + '</div>' + editButon + '</div>'
                        
                        if(valoareInput != 'nevalid' && coloana != 'id' && coloana != 'imagine' && valoareInput != 'none'){
                            $('#cititor-angajati').append(compunereCamp)
                        }
                        else if(coloana == 'imagine'){
                            var folderEmployee = '../../../included/gallery/employee/'
                            var imageRespons = "url('" + folderEmployee + valoareInput + "')"
                            $('#imagine-salariat').css('background-image', imageRespons);
                        }

                        $('#mesaj-ajutor').addClass('dis-none')
                    }
                }
                else{
                    $('#dialog').removeClass('dis-none')
                    $('#dialog-mess').text(result[1]);
                }
                preventServer = 0
                el.disabled = false
                setTimeout(function() {
                    $('#loading-id').addClass('dis-none');
                }, 1000);
            },
            error: function(result) {
                preventServer = 0
                setTimeout(function() {
                    $('#loading-id').addClass('dis-none');
                }, 1000);
            }
        });
    }
}

function closeEditor(){
    $('#campuri-form').addClass('dis-none')
    $('#campuri-container').html('')
    document.getElementById('angajat').disabled = false
    conditiePentruTipActApatrid = 1
}

function citireTari(selectat){
    if(preventServer == 0){
        document.getElementById('lista-tari').disabled = true
        preventServer == 1
        var tari = ''
        var sel = ''
        fetch('../../../included/json/tari.json')
        .then(response => {
            if (!response.ok) {
            throw new Error('Nu s-a putut încărca fișierul JSON');
            }
            return response.json();
        })
        .then(data => {
            for (let index = 0; index < data['tari'].length; index++) {
                sel = ''
                if(selectat == data['tari'][index]){
                    sel = 'selected'
                }
                tari += '<option '+ sel +'>' + data['tari'][index] + '</option>'
            }
            $('#lista-tari').append(tari)
            preventServer = 0
            document.getElementById('lista-tari').disabled = false
        })
        .catch(error => {
            tari = '<option>România</option>'
            $('#lista-tari').append(tari)
            preventServer = 0
            document.getElementById('lista-tari').disabled = false
        });
    }
}

function citireJudete(mode){
    if(preventServer == 0){
        document.getElementById('lista-judete').disabled = true
        preventServer == 1
        var tari = ''
        fetch('../../../included/json/judete.json')
        .then(response => {
            if (!response.ok) {
            throw new Error('Nu s-a putut încărca fișierul JSON');
            }
            return response.json();
        })
        .then(data => {
            for (let index = 0; index < data.length; index++) {
                tari += '<option>' + data[index] + '</option>'
            }
            $('#lista-judete').append(tari)
            preventServer = 0
            document.getElementById('lista-judete').disabled = false
            if(mode == 'waterfall'){
                citireLocalitati('Alba')
            }
        })
        .catch(error => {
            tari = '<option>Eroare</option>'
            $('#lista-judete').append(tari)
            preventServer = 0
            document.getElementById('lista-judete').disabled = false
        });
    }
}

function citireLocalitati(judet){
    if(preventServer == 0){
        document.getElementById('lista-localitati').disabled = true
        preventServer = 1
        var localitati = ''

        fetch('../../../included/json/localitati/' + judet + '.json')
        .then(response => {
            if (!response.ok) {
            throw new Error('Nu s-a putut încărca fișierul JSON');
            }
            return response.json();
        })
        .then(data => {
            data.sort()
            for (let index = 0; index < data.length; index++) {
                localitati += '<option>' + data[index] + '</option>'
            }
            $('#lista-localitati').append(localitati)
            preventServer = 0
            document.getElementById('lista-localitati').disabled = false
        })
        .catch(error => {
            localitati += '<option>Eorare</option>'
            $('#lista-localitati').append(localitati)
            preventServer = 0
            document.getElementById('lista-localitati').disabled = false
        });
    }
}

function editareCampuri(el){
    closeDelete()
    document.getElementById('angajat').disabled = true
    var idCamp = el.id
    var stocareCampuri = ''

    if(idCamp == 'nume' || idCamp == 'cnp' || idCamp == 'pasaport'|| idCamp == 'seria' || idCamp == 'numar_serie' || idCamp == 'telefon_1' || idCamp == 'telefon_2' || idCamp == 'email_1' || idCamp == 'email_2'){
        tipul = 'type="text"'
        if(idCamp == 'email_1' || idCamp == 'email_2'){
            tipul = 'type="email"'
        }
        stocareCampuri = '<label>' + idCamp.replace(/_/g, ' ') +'<input ' + tipul + ' name="' + idCamp + '" required value="' + angajatObiect[idCamp] + '"></label>'
    }
    else if(idCamp == 'genul'){
        stocareCampuri = '<label>Genul<select name="' + idCamp + '"><option value="" disabled selected hidden>' + angajatObiect[idCamp] + '</option><option>Masculin</option><option>Feminin</option></select></label>'
    }
    else if(idCamp == 'apatrid'){
        stocareCampuri = "<option value='' disabled selected hidden>" + coduriApatrid[angajatObiect[idCamp]] + "</option><option value='none'>Nu</option><option value='0'>Cu drept de sedere in alta tara decat Romania</option><option value='1'>Cu drept de sedera pe termen lung in Romania</option>"
        stocareCampuri = '<label>Apatrid<select onchange="checkApatrid(this)" name="' + idCamp + '">' + stocareCampuri + '</select></label>'
    }
    else if(idCamp == 'cetatenie' || idCamp == 'tara'){
        var functie = ''
        if(idCamp == 'tara'){
            functie = 'onchange="checkCountry(this)"'
        }
        else if(idCamp == 'cetatenie'){
            functie = 'onchange="checkTara(this)"'
        }
        stocareCampuri = '<label>' + idCamp + '<select ' + functie + ' id="lista-tari" name="' + idCamp + '"><option value="" disabled selected hidden>' + angajatObiect[idCamp] + '</option></select></label>'
    }
    else if(idCamp == 'emitere_aviz' || idCamp == 'expirare_aviz' || idCamp == 'data_emiterii' || idCamp == 'data_expirarii'){
        var data_aviz = ''
        if(angajatObiect['apatrid'] != 'nespecificat'){
            if(angajatObiect[idCamp] != 'nespecificat'){
                data_aviz = 'value="' + angajatObiect[idCamp] + '"'
            }
        }
        stocareCampuri = '<label>' + idCamp.replace(/_/g, ' ') + '<input name="' + idCamp + '" type="date" ' + data_aviz + '></label>'
    }
    else if(idCamp == 'tip_aviz'){
        stocareCampuri = "<label>Tip aviz<select onchange='checkAviz(this)' name='tip_aviz'><option value='' disabled selected hidden>" + coduriAviz[angajatObiect[idCamp]] + "</option><option value='1'>Exceptie</option><option value='7'>Lucrator inalt calificat</option><option value='8'>Lucratori au pair</option><option value='0'>Lucratori permanenti</option><option value='2'>Lucratori sezonieri</option><option value='3'>Lucratori stagiari</option><option value='6'>Lucratori transfrontalieri</option></select></label>" 
    }
    else if(idCamp == 'judetul'){
        stocareCampuri = '<label>' + idCamp + '<select id="lista-judete" name="' + idCamp + '"><option value="" disabled selected hidden>' + angajatObiect[idCamp] + '</option></select></label>'
    }
    else if(idCamp == 'localitatea'){
        stocareCampuri = '<label>' + idCamp + '<select id="lista-localitati" name="' + idCamp + '"><option value="" disabled selected hidden>' + angajatObiect[idCamp] + '</option></select></label>'
    }
    else if(idCamp == 'adresa' || idCamp == 'emis_de' || idCamp == 'observatii'){
        stocareCampuri = '<label>' + idCamp.replace(/_/g, ' ') +'<br><br><textarea name="' + idCamp + '">' + angajatObiect[idCamp] + '</textarea></label>'
    }
    else if(idCamp == 'persoane_in_intretinere' || idCamp == 'copii'){
        stocareCampuri = '<label>' + idCamp + '<input type="number" min="0" max="20" name="' + idCamp + '" value="' + angajatObiect[idCamp] + '"></label>'
    }
    else if(idCamp == 'casatorit'){
        stocareCampuri = '<label>Casatorit<select name="' + idCamp + '"><option value="" disabled selected hidden>' + angajatObiect[idCamp] + '</option><option>Da</option><option>Nu</option></select></label>'
    }
    else if(idCamp == 'tip_act'){
        var optiuniCarte = ''
        var tipCnpObject = '<option value = "0">Carte de identitate</option><option value = "2">Buletin de identitate</option><option value = "6">Alt tip de identitate romanesc</option>'
        var tipPassObject = '<option value = "1">Pasaport</option><option value = "4">Carte rezidentiala</option><option value = "5">Permis de sedere</option><option value = "7">Alt tip act identitate (apatrid tolerat)</option><option value = "3">Alt tip act identitate</option>'
        if(angajatObiect['apatrid'] != 'none'){
            optiuniCarte = tipCnpObject + tipPassObject
        }
        else if(angajatObiect['cetatenie'] != 'România'){
            optiuniCarte = tipPassObject
        }
        else {
            optiuniCarte = tipCnpObject 
        }
        stocareCampuri = "<label>Tip act<select onchange='checkAct(this)' name='tip_act'><option disabled selected hidden>" + coduriCarte[angajatObiect[idCamp]] + "</option>" + optiuniCarte + "</select></label>"
        conditiePentruTipActApatrid = 0
    }

    $('#campuri-form').removeClass('dis-none')
    $('#campuri-container').html(stocareCampuri)
    if(document.getElementById('lista-tari')){
        citireTari('no') 
    }
    else if(document.getElementById('lista-judete')){
        citireJudete('no')
    }
    else if(document.getElementById('lista-localitati')){
        citireLocalitati(angajatObiect['judetul'])
    }
}

function saveChange(e){
    e.preventDefault()
    if(angajatObiect['id'] !== undefined && preventServer == 0){
        preventServer = 1
        var formular = document.getElementById('editare-campuri')
        var continut = new FormData(formular)
        var cheia = ''

        for (var key of continut.keys()) {
            cheia = key;
            break;
        }

        if(continut.get(cheia) != angajatObiect[cheia]){
            $('#loading-id').removeClass('dis-none')
            continut.append('id', angajatObiect['id'])
            $.ajax({
                url: '../../../included/function/exe/edit-employee.php',
                type: 'POST',
                data: continut,
                processData: false,
                contentType: false,
                success: function(result) {
                    result = stergeSpatiiInceput(result)
                    preventServer = 0
                    setTimeout(function() {
                        $('#loading-id').addClass('dis-none');
                    }, 1000);
                    if(!Number.isNaN(Number(result))){
                        listaAngajati(result, 'get-info')
                    }
                    else{
                        $('#dialog').removeClass('dis-none')
                        $('#dialog-mess').text(result);
                    }
                },
                error: function(result){
                    preventServer = 0
                    setTimeout(function() {
                        $('#loading-id').addClass('dis-none');
                    }, 1000);
                }
            })
        }
        else{
            preventServer = 0
            $('#dialog').removeClass('dis-none')
            $('#dialog-mess').text('Nu au fost sesizate modificari!');
        }
    }
}

function checkApatrid(el){
    var valoare = el.value
    var stocareCampuri = ''
    var valoareAct = ''
    var data_em = ''
    var data_ex = ''

    if(document.getElementById('cetatenie-label')){
        document.getElementById('cetatenie-label').remove()
    }
    if(document.getElementById('aviz-label')){
        document.getElementById('aviz-label').remove()
    }
    if(document.getElementById('time-em-aviz')){
        document.getElementById('time-em-aviz').remove()
    }
    if(document.getElementById('time-ex-aviz')){
        document.getElementById('time-ex-aviz').remove()
    }
    if(document.getElementById('act-label')){
        document.getElementById('act-label').remove()
    }
    if(document.getElementById('numar-act-label')){
        document.getElementById('numar-act-label').remove()
    }
    if(document.getElementById('ex-label')){
        document.getElementById('ex-label').remove()
    }
    if(document.getElementById('em-label')){
        document.getElementById('em-label').remove()
    }
    if(angajatObiect['cnp'] != 'nevalid' && angajatObiect['cnp'] != 'nespecificat'){
        valoareAct = angajatObiect['cnp']
    }
    if(angajatObiect['data_emiterii'] != 'nespecificat'){
        data_em = 'value = "' + angajatObiect['data_emiterii'] + '"'
    }

    if(angajatObiect['data_expirarii'] != 'nespecificat'){
        data_ex = 'value = "' + angajatObiect['data_expirarii'] + '"'
    }

    if(valoare != angajatObiect['apatrid']){
        if(valoare != 'none' && angajatObiect['apatrid'] == 'none'){
            stocareCampuri += '<label id="act-label">Tip act<select onchange="checkAct(this)" name="tip_act"><option value = "0">Carte de identitate</option><option value = "2">Buletin de identitate</option><option value = "6">Alt tip de identitate romanesc</option><option value = "1">Pasaport</option><option value = "4">Carte rezidentiala</option><option value = "5">Permis de sedere</option><option value = "7">Alt tip act identitate (apatrid tolerat)</option><option value = "3">Alt tip act identitate</option></select></label>'
            stocareCampuri += '<label id="numar-act-label">Cnp<input name="cnp" required value="' + valoareAct + '"></label>'
            stocareCampuri += '<label id="em-label">Data emiterii act<input name="data_emiterii" type="date" ' + data_ex + '></label>'
            stocareCampuri += '<label id="ex-label">Data expirarii act<input name="data_expirarii" type="date" ' + data_em + '></label>'
            stocareCampuri += "<label id='aviz-label'>Tip aviz<select onchange='checkAviz(this)' name='tip_aviz'><option value='1'>Exceptie</option><option value='7'>Lucrator inalt calificat</option><option value='8'>Lucratori au pair</option><option value='0'>Lucratori permanenti</option><option value='2'>Lucratori sezonieri</option><option value='3'>Lucratori stagiari</option><option value='6'>Lucratori transfrontalieri</option></select></label>" 
            $('#campuri-container').append(stocareCampuri)
        }
        else if(valoare == 'none' && angajatObiect['apatrid'] != 'none'){
            stocareCampuri = '<label id="cetatenie-label">Cetatenie<select onchange="checkTara(this)" id="lista-tari" name="cetatenie"></select></label>'
            stocareCampuri += '<label id="act-label">Tip act<select onchange="checkAct(this)" name="tip_act"><option value = "0">Carte de identitate</option><option value = "2">Buletin de identitate</option><option value = "6">Alt tip de identitate romanesc</option></select></label>'
            stocareCampuri += '<label id="numar-act-label">Cnp<input name="cnp" required value="' + valoareAct + '"></label>'
            stocareCampuri += '<label id="em-label">Data emiterii<input name="data_emiterii" type="date" ' + data_ex + '></label>'
            stocareCampuri += '<label id="ex-label">Data expirarii<input name="data_expirarii" type="date" ' + data_em + '></label>'

            $('#campuri-container').append(stocareCampuri)
            if(document.getElementById('lista-tari')){
                citireTari('România') 
            }
        }
    }
}

function checkTara(el){
    var valoare = el.value
    var stocareCampuri = ''

    if(document.getElementById('aviz-label')){
        document.getElementById('aviz-label').remove()
    }
    if(document.getElementById('time-em-aviz')){
        document.getElementById('time-em-aviz').remove()
    }
    if(document.getElementById('time-ex-aviz')){
        document.getElementById('time-ex-aviz').remove()
    }
    if(document.getElementById('act-label')){
        document.getElementById('act-label').remove()
    }
    if(document.getElementById('numar-act-label')){
        document.getElementById('numar-act-label').remove()
    }
    if(document.getElementById('ex-label')){
        document.getElementById('ex-label').remove()
    }
    if(document.getElementById('em-label')){
        document.getElementById('em-label').remove()
    }

    if(valoare != angajatObiect['cetatenie']){
        var valoareAct = ''
        var data_ex = ''
        var data_em = ''

        if(angajatObiect['data_emiterii'] != 'nespecificat'){
            data_em = 'value = "' + angajatObiect['data_emiterii'] + '"'
        }

        if(angajatObiect['data_expirarii'] != 'nespecificat'){
            data_ex = 'value = "' + angajatObiect['data_expirarii'] + '"'
        }

        if(valoare != 'România'){
            if(angajatObiect['pasaport'] != 'nevalid' && angajatObiect['pasaport'] != 'nespecificat'){
                valoareAct = angajatObiect['pasaport']
            }
            stocareCampuri = '<label id="act-label">Tip act<select onchange="checkAct(this)" name="tip_act"><option value = "1">Pasaport</option><option value = "4">Carte rezidentiala</option><option value = "5">Permis de sedere</option></select><option value = "7">Alt tip act identitate (apatrid tolerat)</option><option value = "3">Alt tip act identitate</option></label>'
            stocareCampuri += '<label id="numar-act-label">Pasaport<input name="pasaport" required value="' + valoareAct + '"></label>'
            stocareCampuri += '<label id="em-label">Data emiterii act<input name="data_emiterii" type="date" ' + data_ex + '></label>'
            stocareCampuri += '<label id="ex-label">Data expirarii act<input name="data_expirarii" type="date" ' + data_em + '></label>'
            stocareCampuri += "<label id='aviz-label'>Tip aviz<select onchange='checkAviz(this)' name='tip_aviz'><option value='1'>Exceptie</option><option value='7'>Lucrator inalt calificat</option><option value='8'>Lucratori au pair</option><option value='0'>Lucratori permanenti</option><option value='2'>Lucratori sezonieri</option><option value='3'>Lucratori stagiari</option><option value='6'>Lucratori transfrontalieri</option></select></label>" 
            $('#campuri-container').append(stocareCampuri)
        }
        else if(valoare == 'România'){
            if(angajatObiect['cnp'] != 'nevalid' && angajatObiect['cnp'] != 'nespecificat'){
                valoareAct = angajatObiect['cnp']
            }
            stocareCampuri = '<label id="act-label">Tip act<select onchange="checkAct(this)" name="tip_act"><option value = "0">Carte de identitate</option><option value = "2">Buletin de identitate</option><option value = "6">Alt tip de identitate romanesc</option></select></label>'
            stocareCampuri += '<label id="numar-act-label">Cnp<input name="cnp" required value="' + valoareAct + '"></label>'
            stocareCampuri += '<label id="em-label">Data emiterii<input name="data_emiterii" type="date" ' + data_ex + '></label>'
            stocareCampuri += '<label id="ex-label">Data expirarii<input name="data_expirarii" type="date" ' + data_em + '></label>'

            $('#campuri-container').append(stocareCampuri)
        }
    }
}

function checkAviz(el){
    var valoare = el.value
    var stocareCampuri = ''

    if(document.getElementById('time-em-aviz')){
        document.getElementById('time-em-aviz').remove()
    }
    if(document.getElementById('time-ex-aviz')){
        document.getElementById('time-ex-aviz').remove()
    }

    if(valoare != angajatObiect['tip_aviz']){
        if(valoare != '1' && valoare != '0'){
            var data_aviz = ''
            if(angajatObiect['emitere_aviz'] != 'nespecificat' && angajatObiect['emitere_aviz'] != 'nevalid'){
                data_aviz = 'value="' + angajatObiect['emitere_aviz'] + '"'
            }
            else{
                data_aviz = ''
            }
            stocareCampuri += '<label id="time-em-aviz">Emitere aviz<input name="emitere_aviz" type="date" ' + data_aviz + '></label>'

            if(angajatObiect['expirare_aviz'] != 'nespecificat' && angajatObiect['expirare_aviz'] != 'nevalid'){
                data_aviz = 'value="' + angajatObiect['expirare_aviz'] + '"'
            }
            else{
                data_aviz = ''
            }
            stocareCampuri += '<label id="time-ex-aviz">Expirare aviz<input name="expirare_aviz" type="date" ' + data_aviz + '></label>'

            $('#campuri-container').append(stocareCampuri)
        }
        else if(valoare == '0'){
            var data_aviz = ''
            if(angajatObiect['emitere_aviz'] != 'nespecificat' && angajatObiect['emitere_aviz'] != 'nevalid'){
                data_aviz = 'value="' + angajatObiect['emitere_aviz'] + '"'
            }
            stocareCampuri += '<label id="time-em-aviz">Emitere aviz<input name="emitere_aviz" type="date" ' + data_aviz + '></label>'
            $('#campuri-container').append(stocareCampuri)
        }
    }
}

function checkAct(el){
    var valoare = el.value
    var stocareCampuri = ''
    var cnpSerie = ''

    if(conditiePentruTipActApatrid == 0){
        if(valoare != angajatObiect['tip_act']){
            if(document.getElementById('numar-act-label')){
    
            }
            else{
                stocareCampuri = '<label id="numar-act-label">Cnp<input name="cnp" required></label>'
                $('#campuri-container').append(stocareCampuri)
                stocareCampuri = ''
            }
        }
        else{
            if(document.getElementById('numar-act-label')){
                document.getElementById('numar-act-label').remove()
            }
        }
    }

    if(valoare != '1'){
        if(angajatObiect['cnp'] != 'nespecificat' && angajatObiect['cnp'] != 'nevalid'){
            cnpSerie = angajatObiect['cnp']
        }
        stocareCampuri = 'Cnp<input name="cnp" required value="' + cnpSerie + '">'
        $('#numar-act-label').html(stocareCampuri)
    }
    else if(valoare == '1'){
        if(angajatObiect['pasaport'] != 'nespecificat' && angajatObiect['pasaport'] != 'nevalid'){
            cnpSerie = angajatObiect['pasaport']
        }
        stocareCampuri = 'Pasaport<input name="pasaport" required value="' + cnpSerie + '">'
        $('#numar-act-label').html(stocareCampuri)
    }
}

function checkCountry(el){
    var valoare = el.value
    var stocareCampuri = ''

    if(document.getElementById('judetul-label')){
        document.getElementById('judetul-label').remove()
    }
    if(document.getElementById('localitatea-label')){
        document.getElementById('localitatea-label').remove()
    }

    if(valoare != angajatObiect['tara']){
        if(valoare != 'România'){

        }
        else{
            stocareCampuri = '<label id="judetul-label">Judetul<select onchange="changeLoc(this)" id="lista-judete" name="judetul"></select></label>'
            stocareCampuri += '<label id="localitatea-label">Localitatea<select id="lista-localitati" name="localitatea"></select></label>'
            $('#campuri-container').append(stocareCampuri)
            citireJudete('waterfall')
        }
    }

}

function newPhoto(){

}

function changeLoc(judet){
    $('#lista-localitati').html('')
    citireLocalitati(judet.value)
}

function stergeAngajat(){
    if(preventServer == 0){
        $('#loading-id').removeClass('dis-none');
        preventServer = 1
        $.ajax({
            url: '../../../included/function/exe/edit-employee.php',
            type: 'POST',
            data: {'delete':'true','id':angajatObiect['id']},
            success: function(result) {
                result = stergeSpatiiInceput(result)
                result = result.split(':')
                if(result[0] == 'SUCCES'){
                    location.reload()
                }
                else{
                    $('#dialog').removeClass('dis-none')
                    $('#dialog-mess').text(result[1]);
                }
                preventServer = 0
                setTimeout(function() {
                    $('#loading-id').addClass('dis-none');
                }, 1000);
            },
            error: function(result){
                preventServer = 0
                setTimeout(function() {
                    $('#loading-id').addClass('dis-none');
                }, 1000);
            }
        })
    }
}

function openDelete(){
    $('#panou-stergere').removeClass('dis-none')
}

function closeDelete(){
    $('#panou-stergere').addClass('dis-none')
}

$(document).ready(function () {
    listaAngajati('no','')
    $('#editare-campuri').submit(saveChange)
})