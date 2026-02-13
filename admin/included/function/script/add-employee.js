var preventServer = 0
var indexForm = 0
var formulare = ["date-salariat","date-adresa","date-buletin","date-stare",
                "date-poza","date-observatii"];
var avizApatrid = "<label id='emitere-aviz'>Data emitere aviz<input name='emitere-aviz' type='date'></label><label id='expirare-aviz'></label><label>Tip aviz<select onchange='typeAviz(this)' name='tip-aviz'><option value='1'>Exceptie</option><option value='7'>Lucrator inalt calificat</option><option value='8'>Lucratori au pair</option><option value='0' selected>Lucratori permanenti</option><option value='2'>Lucratori sezonieri</option><option value='3'>Lucratori stagiari</option><option value='6'>Lucratori transfrontalieri</option></select></label>"  
var cetatenieObject = "<label>Cetatenie<select id='cetatenie-id' name='cetatenie' onchange = 'adresaChange(this)' disabled></select></label>"
var judetLocalObject = "<label>Judetul<select onchange = 'citireLocalitati(this,\"no\")' id='judetul-id' name='judetul' disabled></select></label><label>Localitatea<select id='localitatea-id' name='localitatea'></select></label>"
var tipCnpObject = '<option value = "0">Carte de identitate</option><option value = "2">Buletin de identitate</option><option value = "6">Alt tip de identitate romanesc</option>'
var tipPassObject = '<option value = "1">Pasaport</option><option value = "4">Carte rezidentiala</option><option value = "5">Permis de sedere</option><option value = "7">Alt tip act identitate (apatrid tolerat)</option><option value = "3">Alt tip act identitate</option>'
var pasaportForm = '<label>Serie pasaport <input type="text" name="pasaport"></label><label>Data emiterii<input type="date" name="data-emiterii"></label><label>Data expirarii<input type="date" name="data-expirarii"></label><label>Emis de<br><textarea rows="4" cols="50" name="emis-de"></textarea></label>'
var simpleCnp = '<label>CNP <input type="text" name="cnp"></label><label>Data emiterii<input type="date" name="data-emiterii"></label><label>Data expirarii<input type="date" name="data-expirarii"></label><label>Emis de<br><textarea rows="4" cols="50" name="emis-de"></textarea></label>'
var detailsCnp = "<label>CNP<input type='text' name='cnp'></label><label>Serie<input type='text' name='seria'></label><label>Numar<input type='text' name='numar-serie'></label><label>Data emiterii<input type='date' name='data-emiterii'></label><label>Data expirarii<input type='date' name='data-expirarii'></label><label>Emis de<br><textarea rows='4' cols='50' name='emis-de'></textarea></label>"
var photoUser = 'user.png'

function addDisNoneForm(){
    $('#add-emplolyee').scrollTop(0);
    for (let index = 0; index < formulare.length; index++) {
        $("#"+formulare[index]).addClass('dis-none')
    }
}

function next(){
    if(indexForm < 5){
        $('#back-form').removeClass('dis-none')
        addDisNoneForm()
        var nextPage = indexForm + 1
        indexForm = nextPage
        $("#" + formulare[nextPage]).removeClass('dis-none')
        if(indexForm == 5){
            $('#next-form').addClass('dis-none')
            $('#save-form').removeClass('dis-none')
        }
    }
}

function disable_select(){
    $('#apatrid-id, #cetatenie-id, #country-id, #judetul-id, #localitatea-id').prop("disabled", true)
}

function enable_select(){
    $('#apatrid-id, #cetatenie-id, #country-id, #judetul-id, #localitatea-id').prop("disabled", false)
}

function back(){
    if(indexForm > 0){
        $('#save-form').addClass('dis-none')
        $('#next-form').removeClass('dis-none')
        addDisNoneForm()
        var nextPage = indexForm - 1
        indexForm = nextPage
        $("#" + formulare[nextPage]).removeClass('dis-none')
        if(indexForm == 0){
            $('#next-form').removeClass('dis-none')
            $('#back-form').addClass('dis-none')
        }
    }
}

function uploadPhoto(){
    if(preventServer == 0){
        $('#loading-id').removeClass('dis-none')
        preventServer = 1
        var imagine = this.files[0];
        var formData = new FormData();
        formData.append('file', imagine);
        formData.append('photo-access', 'true');
        $.ajax({
            url: '../../../included/function/exe/add-employee.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(result) {
                result = stergeSpatiiInceput(result)
                var imageRespons = result.split(':')
                if(imageRespons[0] == 'SUCCESS'){
                    photoUser = imageRespons[1]
                    var folderEmployee = '../../../included/gallery/employee/'
                    imageRespons = "url('" + folderEmployee + imageRespons[1] + "')"
                    $('#preview-photo').css('background-image', imageRespons);
                    $('#photo-employee').css('background-image', imageRespons);
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

function citireTari(mode,id){
    if(preventServer == 0){
        var campSelect = document.getElementById('cetatenie-id')
        var campSecund = document.getElementById('country-id')
        disable_select()
        preventServer = 1

        fetch('../../../included/json/tari.json')
        .then(response => {
            if (!response.ok) {
            throw new Error('Nu s-a putut încărca fișierul JSON');
            }
            return response.json();
        })
        .then(data => {
            for (let index = 0; index < data['tari'].length; index++) {
                var selectedCheck = ''
                if(data['tari'][index] == 'România'){
                    selectedCheck = 'selected'
                }
                campSelect.innerHTML += '<option ' + selectedCheck + '>' + data['tari'][index] + '</option>'
                if(id == 'all'){
                    campSecund.innerHTML += '<option ' + selectedCheck + '>' + data['tari'][index] + '</option>'
                }
            }
            preventServer = 0
            enable_select()
            if(mode == 'waterfall'){
                citireJudete('waterfall')
            }
        })
        .catch(error => {
            campSelect.innerHTML = '<option>România</option>'
            campSecund.innerHTML = '<option>România</option>'
            preventServer = 0
            enable_select()
        });
    }
}

function citireJudete(mode){
    if(preventServer == 0){
        document.getElementById('judetul-id').innerHTML = ''
        preventServer = 1
        disable_select()

        fetch('../../../included/json/judete.json')
        .then(response => {
            if (!response.ok) {
            throw new Error('Nu s-a putut încărca fișierul JSON');
            }
            return response.json();
        })
        .then(data => {
            for (let index = 0; index < data.length; index++) {
                var selectedCheck = ''
                if(data[index] == 'Alba'){
                    selectedCheck = 'selected'
                }
                document.getElementById('judetul-id').innerHTML += '<option ' + selectedCheck + '>' + data[index] + '</option>'
            }
            preventServer = 0
            enable_select()
            if(mode == 'waterfall'){
                citireLocalitati('', 'Alba')
            }
        })
        .catch(error => {
            document.getElementById('judetul-id').innerHTML = '<option> România </option>'
            preventServer = 0
            enable_select()
        });
    }
}

function citireLocalitati(el, nume){
    if(preventServer == 0){
        document.getElementById('localitatea-id').innerHTML = ''
        preventServer = 1
        disable_select()
        var judet = ''

        if(nume == 'no'){
            judet = el.value
        }
        else{
            judet = nume
        }
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
                document.getElementById('localitatea-id').innerHTML += '<option>' + data[index] + '</option>'
            }
            preventServer = 0
            enable_select()
        })
        .catch(error => {
            preventServer = 0
            enable_select()
        });
    }
}

function apatridChecker(el){
    var valoare = el.value
    var cetatienie = document.getElementById('cetatenie-range')

    if(valoare == '1'){
        document.getElementById('aviz-apatrid').innerHTML = ''
        cetatienie.innerHTML = ''
        document.getElementById('act-id').innerHTML = tipCnpObject + tipPassObject
    }
    else if(valoare == '0'){
        cetatienie.innerHTML = ''
        document.getElementById('aviz-apatrid').innerHTML = avizApatrid
        document.getElementById('act-id').innerHTML = tipCnpObject + tipPassObject
    }
    else{
        document.getElementById('aviz-apatrid').innerHTML = ''
        document.getElementById('act-id').innerHTML = tipCnpObject
        cetatienie.innerHTML = cetatenieObject
        citireTari('','')
    }
    actDetails(document.getElementById('act-id'))
}

function taraChecker(el){
    var valoare = el.value
    var rangeJudetLocal = document.getElementById('judet-localitate-range')

    if(valoare !== 'România'){
        rangeJudetLocal.innerHTML = ''
    }
    else{
        rangeJudetLocal.innerHTML = judetLocalObject
        citireJudete('waterfall')
    }
}

function adresaChange(el){
    var valoare = el.value

    if(valoare == 'România'){
        document.getElementById('aviz-apatrid').innerHTML = ''
        document.getElementById('act-id').innerHTML = tipCnpObject
    }
    else{
        document.getElementById('aviz-apatrid').innerHTML = avizApatrid
        document.getElementById('act-id').innerHTML = tipPassObject
    }
    actDetails(document.getElementById('act-id'))
}

function actDetails(el){
    var tipAct = el.value
    var actRangeDetails = document.getElementById('detalii-act-range')
    if(tipAct == '1'){
        actRangeDetails.innerHTML = pasaportForm 
    }
    else if(tipAct == '0'){
        actRangeDetails.innerHTML = detailsCnp
    }
    else{
        actRangeDetails.innerHTML = simpleCnp
    }
}

function typeAviz(el){
    var avizEm = "Data emitere aviz<input type='date' name='emitere-aviz'>"
    var avizEx = "Data expirare aviz<input type='date' name='expirare-aviz'>"
    var avizVal = el.value
    var emitere = document.getElementById('emitere-aviz')
    var expirare = document.getElementById('expirare-aviz')
    if(avizVal == '1'){
        emitere.innerHTML = ''
        expirare.innerHTML = ''
    }
    else if(avizVal == '0'){
        emitere.innerHTML = avizEm
        expirare.innerHTML = ''
    }
    else{
        emitere.innerHTML = avizEm 
        expirare.innerHTML = avizEx
    }
}

function closeView(){
    document.getElementById('view-all-info').classList.add('dis-none')
}

function showAllInfo(){
    $('#info-employee').text('')
    var campuri = [
        "nume","prenume","genul","apatrid","cetatenie","emitere-aviz","expirare-aviz","tip-aviz",
        "tara","judetul","localitatea","adresa","telefon-1","telefon-2","email-1",
        "email-2","tip-act","pasaport","cnp","seria","numar-serie","data-emiterii",
        "data-expirarii","emis-de","casatorit","copii","persoane-in-intretinere","observatii"
    ];

    var dataContinut = ''
    var secventaCod = ''
    var titluContinut = ''
    var formular = document.getElementById('add-emplolyee')
    var continut = new FormData(formular)
    document.getElementById('view-all-info').classList.remove('dis-none')

    for (let index = 0; index < campuri.length; index++) {
        if(continut.has(campuri[index])){
            dataContinut = continut.get(campuri[index])
            titluContinut = campuri[index].replace(/-/g, ' ')
            if(dataContinut.trim().length <= 0){
                dataContinut = '-'
            }
            if(titluContinut == 'apatrid'){
                dataContinut = coduriApatrid[dataContinut]
            }
            if(titluContinut == 'tip act'){
                dataContinut = coduriCarte[dataContinut]
            }
            if(titluContinut == 'tip aviz'){
                dataContinut = coduriAviz[dataContinut]
            }
            secventaCod = '<h3 class="titlu-date">' + titluContinut + ':</h3><div class="paragraf-date">' + dataContinut + '</div>'
            $('#info-employee').append(secventaCod)
        }
    }
}

function addEmployee(e){
    if(preventServer == 0){
        preventServer = 1
        e.preventDefault();

        $('#loading-id').removeClass('dis-none')
        var formular = document.getElementById('add-emplolyee')
        var continut = new FormData(formular)
        continut.append('imagine', photoUser)

        $.ajax({
            url: '../../../included/function/exe/add-employee.php',
            type: 'POST',
            data: continut,
            processData: false,
            contentType: false,
            success: function(result) {
                result = result.trim()
                if(result == 'ok'){
                    $('#dialog').removeClass('dis-none')
                    $('#dialog-mess').text('Angajatul a fost adaugat cu succes!');
                    setTimeout(function() {
                        location.reload()
                    }, 1000);
                }
                else{
                    $('#dialog').removeClass('dis-none')
                    $('#dialog-mess').text(result);
                    preventServer = 0
                    setTimeout(function() {
                        $('#loading-id').addClass('dis-none');
                    }, 1000);
                }
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

$(document).ready(function () {
    $('#loading-id').removeClass('dis-none')
    citireTari('waterfall','all')
    $('#add-emplolyee').submit(addEmployee)
    $('#back-form').click(back)
    $('#next-form').click(next)
    $('#up-photo').on("change", uploadPhoto)
    setTimeout(function() {
        $('#loading-id').addClass('dis-none');
    }, 2000);
})
