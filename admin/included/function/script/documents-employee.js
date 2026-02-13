var urlFile = '../../../included/function/exe/documents-employee.php'
var messageError = ['Folderul pe care incerci sa il accesezi nu exista in baza de date!','Acesasta adresa nu exista!']
var functie = [insideFolder, activeazaCreate, afterCreatedFolder, citireFoldere, afterRenameFolder, moveFolderSql, downloadPhp, pathTravelFun]
var preventServer = 0
var angajatObiect = ''
var path = 'R:'
var createFolderAcces = 0
var folderSelectat = 0
var permisiuneInputSearch = 1
var timeoutId = ''
var mutareFolder = 0
var modeMove = 0
var pathTravel = 0
var indexTravel = 0

// FUNCTII COMUNE
    function sendCommandPhp(objectForPhp, numarFunctie){
        clearTimeout(timeoutId)
        if(preventServer == 0){
            $('#angajat').prop('disabled', true)
            preventServer = 1
            $.ajax({
                url: urlFile,
                type: 'POST',
                data: objectForPhp,
                success: function(result) {
                    result = cleanSpaces(result)
                    preventServer = 0
                    $('#angajat').prop('disabled', false)
                    functie[numarFunctie](result)
                },
                error: function(result){
                    preventServer = 0
                    $('#angajat').prop('disabled', false)
                    functie[numarFunctie](result)
                }
            })
        }
    }

    function multipleEfect(array, fun, target) {
        var idsButon = array;
        for (let index = 0; index < idsButon.length; index++) {
            $(idsButon[index])[fun](target);
        }
    }
    function pathCorectat(path){
        var checkPath = ''
        if(path.includes("/")){
            var pathVal = path.split('/')
            for (var index = 0; index < pathVal.length - 1; index++) {
                checkPath += pathVal[index] + '/'
            }
        }
        else{
            checkPath = 'R:'
        }
        return checkPath;
    }
//

// FUNCTII DE REDARE ANGAJATI SI DOCUMENTE
    function listaAngajati(selectEl) {
        if(preventServer == 0){
            preventServer = 1
            $('#angajat').prop('disabled', true)
            $('#loading-id').removeClass('dis-none')
            $.ajax({
                url: '../../../included/function/exe/edit-employee.php',
                type: 'POST',
                data: {'lista-angajati':'read','selecteaza':selectEl},
                success: function(result) {
                    result = '<option value="" disabled selected hidden>Salariati</option>' + stergeSpatiiInceput(result)
                    $('#angajat').html(result)
                    preventServer = 0
                    $('#angajat').prop('disabled', false)

                    setTimeout(function() {
                        $('#loading-id').addClass('dis-none');
                    }, 1000);
                },
                error: function(result){
                    preventServer = 0
                    $('#angajat').prop('disabled', false)
                    setTimeout(function() {
                        $('#loading-id').addClass('dis-none');
                    }, 1000);
                }
            })
        }
    }

    function readEmployee(el, mode){
        if(preventServer == 0){
            preventServer = 1
            permisiuneInputSearch = 1
            el.disabled = true
            folderSelectat = 0
            createFolderAcces = 0
            angajatObiect = ''
            mutareFolder = 0
            modeMove = 0

            $('#foldere').html('')
            $('#input-cale').val('')
            $('#angajat').prop('disabled', true)
            multipleEfect(['#loading-id', '#create-folder'], 'removeClass', 'disabled-effect')
            multipleEfect(['#download-file', '#edit-folder', '#delete-folder', '#move-folder', '#cut-folder', '#paste-folder'], 'addClass', 'disabled-effect')
            path = 'R:'

            var angajat = el
            if(mode == 'start'){
                angajat = el.value
            }

            $.ajax({
                url: '../../../included/function/exe/edit-employee.php',
                type: 'POST',
                data: {'angajat':angajat},
                success: function(result) {
                    result = stergeSpatiiInceput(result)
                    result = result.split('||')
                    if(result[0] == 'ok'){                    
                        $('#cititor-angajati').removeClass('dis-none')
                        angajatObiect = JSON.parse(result[1])
                        var folderEmployee = '../../../included/gallery/employee/'
                        var imageRespons = "url('" + folderEmployee + angajatObiect['imagine'] + "')"
                        $('#imagine-salariat').css('background-image', imageRespons);
                        $('#mesaj-ajutor').addClass('dis-none')
                        preventServer = 0
                        $('#angajat').prop('disabled', false)
                        var arrayForPhpFoldere = {
                            'angajat': angajatObiect['id'],
                            'cale-folder': path,
                            'search-foldere': 'true'
                        }
                        sendCommandPhp(arrayForPhpFoldere, 3)
                    }
                    else{
                        $('#dialog').removeClass('dis-none')
                        $('#dialog-mess').text(result[1]);
                        preventServer = 0
                        $('#angajat').prop('disabled', false)
                    }
                    el.disabled = false
                    setTimeout(function() {
                        $('#loading-id').addClass('dis-none');
                    }, 1000);
                },
                error: function(result) {
                    preventServer = 0
                    $('#angajat').prop('disabled', false)
                    setTimeout(function() {
                        $('#loading-id').addClass('dis-none');
                    }, 1000);
                }
            });
        }
    }
//

function moveCursorToEnd(element) {
    let range = document.createRange();
    range.selectNodeContents(element);
    range.collapse(false);
    let selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(range);
}

function createFolder(){
    if(path.charAt(path.length - 1) != '/' && path.charAt(path.length - 1) != ':' && createFolderAcces == 0){
        // CORECTAM CALEA INAINTE DE A CREEA FOLDERUL
            path = pathCorectat(path)
            var arrayForPhpFoldere = {
                'angajat': angajatObiect['id'],
                'cale-folder': path,
                'search-foldere': 'true'
            }
            sendCommandPhp(arrayForPhpFoldere, 3)
        //
    }
    else if(createFolderAcces == 0){
        // DEZACTIVAM ACCESUL TUTUROR FUNCTIILOR PANA LA FINALIZAREA FOLDERULUI
            createFolderAcces = 1
            document.getElementById('file-input').disabled = true
            $('#upload-file').addClass('disabled-effect')
        //
        // IN CAZUL IN CARE AVEM FOLDER SELECTAT IL DEZACTIVAM
            if(folderSelectat != 0){
                var folderVarSelectat = document.getElementById(folderSelectat).children
                folderVarSelectat[1].classList.replace('descriere-completa', 'descriere-incompleta');
                folderVarSelectat[0].style.color = '#e0b85c'
                folderSelectat = 0
                multipleEfect(['#download-file', '#edit-folder', '#delete-folder', '#move-folder', '#cut-folder'], 'addClass', 'disabled-effect')
            }
        //
        // FOLDER
            var divElementFolder = document.createElement('div');
            divElementFolder.setAttribute('ondblclick', 'insideFolder(this)');
            divElementFolder.setAttribute('onmouseover', 'openEfect(this, "folder")');
            divElementFolder.setAttribute('onmouseout', 'closeEfect(this, "folder")');
            divElementFolder.setAttribute('onclick', 'showAllDescriere(this)');
            divElementFolder.className = 'file-class';
        //
        // ICON FOLDER
            var spanElement = document.createElement('span')
            spanElement.className = 'material-symbols-outlined';
            spanElement.innerHTML = 'folder'
        //
        // FUNCTIE EXE DE CREAT FOLDER + NUME
            var divElementDescriere = document.createElement('div')
            divElementDescriere.setAttribute('id', 'new-folder') 
            divElementDescriere.classList.add('descriere', 'descriere-completa', 'descriere-completa-special');
            divElementDescriere.setAttribute('onpaste','pasteEv(this)')
            divElementDescriere.setAttribute('onkeydown','removeExtra(this)')
            divElementDescriere.setAttribute('onfocusout','exeCreateFolder(this)') 
            divElementDescriere.setAttribute('contenteditable', 'true')
            divElementDescriere.innerHTML = 'dosar'
        //
        // CREARE FOLDER INTERFATA
            divElementFolder.appendChild(spanElement)
            divElementFolder.appendChild(divElementDescriere)
            $('#foldere').append(divElementFolder)
        //
        // FOCALIZAM ELEMENTUL CARE DETINE FUNCTIA EXE
            document.getElementById('new-folder').focus()
            moveCursorToEnd(document.getElementById('new-folder'))
        //
    }
}

function exeCreateFolder(el){
    var arrayForPhp = {
        'angajat': angajatObiect['id'],
        'cale-folder': path,
        'nume-folder': el.innerHTML,
        'create-folder': 'true'
    }
    sendCommandPhp(arrayForPhp, 2)
}

function afterCreatedFolder(response){
    if(response == 'true'){
        folderSelectat = 0
        createFolderAcces = 0
        document.getElementById('file-input').disabled = false
        $('#upload-file').removeClass('disabled-effect')

        var arrayForPhpFoldere = {
            'angajat': angajatObiect['id'],
            'cale-folder': path,
            'search-foldere': 'true'
        }
        sendCommandPhp(arrayForPhpFoldere, 3)
    }
    else{
        mesajEroareFolder('new-folder', response)
    }
}

function pasteEv(e){
    var clipboardData = event.clipboardData || window.clipboardData;
    var pastedText = clipboardData.getData('text');
    event.preventDefault();
    var numText = pastedText + e.innerHTML
    if(numText.length >= 217){
        if(document.getElementById('new-folder')){
            document.getElementById('new-folder').focus()
        }
        else if(document.getElementById('rename-folder')){
            document.getElementById('rename-folder').focus()
        }
        $('#dialog').removeClass('dis-none')
        $('#dialog-mess').text('Cea ce doresti sa adaugi depaseste spatiul permis!');
    }
    else{
        e.innerHTML += pastedText
    }
}    

function removeExtra(el){
    var descriere = el.innerText
    var lungime = descriere.length
    if(lungime >= 217 && event.keyCode != 8){
        event.preventDefault()
    }
    if(lungime <= 217 && event.keyCode == 13){
        el.blur()
        event.preventDefault()
    }
}

function openEfect(el,tipDoc){
    if(tipDoc == 'folder'){
        el.children[0].innerHTML = 'folder_open' 
    }
}

function closeEfect(el,tipDoc){
    if(tipDoc == 'folder'){
        el.children[0].innerHTML = 'folder'
   }
}

function citireFoldere(result){
    $('#input-cale').val(path.slice(2))
    $('#foldere').html(result)
}

function showAllDescriere(el){
    if(createFolderAcces == 0 || permisiuneInputSearch == 0){
        if(folderSelectat != 0 && el.id != folderSelectat){
            var folderVarSelectat = document.getElementById(folderSelectat).children
            folderVarSelectat[1].classList.replace('descriere-completa', 'descriere-incompleta');
            folderVarSelectat[0].style.color = '#e0b85c'
        }
        folderSelectat = el.id
        el.children[0].style.color = '#4a6da7'
        el.children[1].classList.replace('descriere-incompleta','descriere-completa');
        multipleEfect(['#download-file','#edit-folder', '#delete-folder', '#move-folder', '#cut-folder'], 'removeClass', 'disabled-effect')
    }
}

function insideFolder(el){
    if(createFolderAcces == 0){
        path = el.id
        folderSelectat = 0
        var caleRest = el.id.split(':')
    
        $('#input-cale').val(caleRest[1])
        multipleEfect(['#download-file','#edit-folder', '#delete-folder', '#move-folder', '#cut-folder'], 'addClass', 'disabled-effect')
        $('#create-folder').removeClass('disabled-effect')
        $('#input-cale').removeClass('bara-activare')
    
        var arrayForPhpFoldere = {
            'angajat': angajatObiect['id'],
            'cale-folder': path,
            'search-foldere': 'true'
        }

        pathTravel = path
        indexTravel = pathTravel.split('/').length
        $('#undo-id').removeClass('disabled-effect')
        $('#redo-id').addClass('disabled-effect')

        sendCommandPhp(arrayForPhpFoldere, 3)
        createFolderAcces = 0
        permisiuneInputSearch = 1
    }
}

function activeazaCreate(result){
    if(result == 'true'){
        document.getElementById('file-input').disabled = false
        $('#create-folder').removeClass('disabled-effect')
        $('#upload-file').removeClass('disabled-effect')

        if(mutareFolder != 0){
            $('#paste-folder').removeClass('disabled-effect')
        }
        createFolderAcces = 0
        permisiuneInputSearch = 1
        var arrayForPhpFoldere = {
            'angajat': angajatObiect['id'],
            'cale-folder': path,
            'search-foldere': 'true'
        }

        if(path != 'R:' && path .charAt(path.length - 1) === '/'){
            pathTravel = path
            indexTravel = pathTravel.split('/').length
            $('#undo-id').removeClass('disabled-effect')
            $('#redo-id').addClass('disabled-effect')
        }

        sendCommandPhp(arrayForPhpFoldere, 3)
        document.getElementById('input-cale').focus()
    }
    else{
        mesajEroareFolder('', 'Acest folder nu exista!')
    }
}

function findFolderInput(el){
    if(preventServer == 0){
        var caractereSpeciale = /[<>:;'"|.?*]/g

        if(!caractereSpeciale.test(el.value) && cleanSpaces(el.value).length > 0){   
            el.classList.add('bara-activare') 
            var valoareInp = cleanSpaces(el.value)
            path = valoareInp.replace(/\s+/g, "-")
            var pathVal = path.split('/')
            var checkPath = ''
    
            if(pathVal.length > 0){
                checkPath = 'R:' 
                for (var index = 0; index < pathVal.length - 1; index++) {
                    checkPath += pathVal[index] + '/'
                }
            }
            else{
                pathVal = ''
                checkPath = 'R:' + pathVal
            }
            path = 'R:' + path
            var arrayForPhpFoldere = {
                'angajat': angajatObiect['id'],
                'cale-folder': checkPath,
                'input-foldere': 'true'
            }
            sendCommandPhp(arrayForPhpFoldere, 1)
        }
        else if(!caractereSpeciale.test(el.value) && cleanSpaces(el.value).length == 0){
            path = 'R:'
            activeazaCreate('true')
            el.classList.remove('bara-activare')
        }
        else{
            path = 'R:'
            $('#foldere').html('')
            el.classList.remove('bara-activare')
        }
    }
}

function startEventInput(el){
    folderSelectat = 0
    createFolderAcces = 1
    permisiuneInputSearch = 0
    pathTravel = 0
    $('#undo-id').addClass('disabled-effect')
    $('#redo-id').addClass('disabled-effect')

    document.getElementById('file-input').disabled = true
    multipleEfect(['#download-file','#upload-file', '#edit-folder', '#delete-folder', '#create-folder', '#move-folder', 
    '#paste-folder', '#cut-folder'], 'addClass', 'disabled-effect')
    $('#foldere').html('')

    clearTimeout(timeoutId)
    timeoutId = setTimeout(function() {
        findFolderInput(el)
    }, 1000)
}

function backgroundFunction(){
    if(event.target.id == 'foldere'){
        if(folderSelectat != 0 && createFolderAcces == 0){
            var folderVarSelectat = document.getElementById(folderSelectat).children
            folderVarSelectat[1].classList.replace('descriere-completa', 'descriere-incompleta');
            folderVarSelectat[0].style.color = '#e0b85c'
            multipleEfect(['#download-file', '#edit-folder', '#delete-folder','#move-folder','#cut-folder'], 'addClass', 'disabled-effect')
            folderSelectat = 0
        }
    }
}

function editFolder(){
    if(folderSelectat != 0){
        createFolderAcces = 1
        document.getElementById('file-input').disabled = true
        var folderEditat = document.getElementById(folderSelectat)
        folderEditat.children[1].setAttribute('contenteditable', 'true');
        folderEditat.children[1].setAttribute('id', 'rename-folder');
        folderEditat.children[1].focus()
        folderEditat.children[1].classList.add('descriere-completa-special')
        folderEditat.children[1].classList.add('descriere-completa')
        folderEditat.children[1].classList.remove('descriere-incompleta')
        folderEditat.children[1].setAttribute('onfocusout', 'editareFolderExe(this)')
        folderEditat.children[1].setAttribute('onpaste', 'pasteEv(this)')
        folderEditat.children[1].setAttribute('onkeydown', 'removeExtra(this)')
        moveCursorToEnd(document.getElementById('rename-folder'))
    }
}

function editareFolderExe(el){
    var lastCaracter = el.parentElement.id.charAt(el.parentElement.id.length - 1)
    var newPathRename = pathCorectat(path) + el.innerText.trim() + lastCaracter
    newPathRename = newPathRename.replace(/\s+/g, ' ')
    newPathRename = newPathRename.replace(/ /g, '-').toUpperCase()

    if(newPathRename != el.parentElement.id){
        var arrayForPhpFoldere = {
            'angajat': angajatObiect['id'],
            'cale-folder': pathCorectat(path),
            'folder-name': folderSelectat,
            'new-name':el.innerHTML,
            'rename-fisiere': 'true'
        }
        sendCommandPhp(arrayForPhpFoldere, 4)
    }
    else{
        var arrayForPhpFoldere = {
            'angajat': angajatObiect['id'],
            'cale-folder': path,
            'search-foldere': 'true'
        }
        sendCommandPhp(arrayForPhpFoldere, 3)
        createFolderAcces = 0
        document.getElementById('file-input').disabled = false
        folderSelectat = 0
        multipleEfect(['#download-file', '#edit-folder', '#delete-folder', '#cut-folder', '#move-folder'], 'addClass', 'disabled-effect')
    }
}

function afterRenameFolder(result){
    if(result == 'true'){
        var arrayForPhpFoldere = {
            'angajat': angajatObiect['id'],
            'cale-folder': path,
            'search-foldere': 'true'
        }
        sendCommandPhp(arrayForPhpFoldere, 3)
        createFolderAcces = 0
        folderSelectat = 0
        document.getElementById('file-input').disabled = false
        multipleEfect(['#download-file', '#edit-folder', '#delete-folder', '#cut-folder', '#move-folder'], 'addClass', 'disabled-effect')
    }
    else{
        mesajEroareFolder('rename-folder', result)
    }
}

function moveFolder(el){
    if(folderSelectat != 0 && createFolderAcces == 0){
        prepareMoveFolder('copy')
    }
}

function cutFolder(el){
    if(folderSelectat != 0 && createFolderAcces == 0){
        prepareMoveFolder('cut')
    }
}

function prepareMoveFolder(mode){
    modeMove = mode
    mutareFolder = folderSelectat
    $('#paste-folder').removeClass('disabled-effect')
}

function sendLocation() {
    if(mutareFolder != 0 && createFolderAcces == 0 && modeMove != 0){
        if(folderSelectat != 0){
            var arrayForPhpFoldere = {
                'angajat': angajatObiect['id'],
                'old-path': mutareFolder,
                'new-path': folderSelectat,
                'move-mode': modeMove,
                'move-copy-fisiere': 'true'
            }
            sendCommandPhp(arrayForPhpFoldere, 5)
        }
        else{
            var pathEditat = ''
            if(path.includes("/")){
                var pathVal = path.split('/')
                for (var index = 0; index < pathVal.length - 1; index++) {
                    pathEditat += pathVal[index] + '/'
                }
            }
            else{
                pathEditat = 'R:'
            }
            var arrayForPhpFoldere = {
                'angajat': angajatObiect['id'],
                'old-path': mutareFolder,
                'new-path': pathEditat,
                'move-mode': modeMove,
                'move-copy-fisiere': 'true'
            }
            sendCommandPhp(arrayForPhpFoldere, 5)
        }
    }
}

function moveFolderSql(result){
    if(result == 'true'){
        disabledMoveFolder()
        var arrayForPhpFoldere = {
            'angajat': angajatObiect['id'],
            'cale-folder': path,
            'search-foldere': 'true'
        }
        sendCommandPhp(arrayForPhpFoldere, 3)
    }
    else{
        mesajEroareFolder('', result)
    }
}

function disabledMoveFolder(){
    modeMove = 0
    mutareFolder = 0
    folderSelectat = 0
    multipleEfect(['#download-file', '#edit-folder', '#delete-folder', '#move-folder', 
        '#paste-folder', '#cut-folder'], 'addClass', 'disabled-effect')
}

function mesajEroareFolder(folderMode, mesaj){
    $('#dialog').removeClass('dis-none')
    $('#dialog-mess').text(mesaj)
    if(document.getElementById(folderMode)){
        document.getElementById(folderMode).focus()
        moveCursorToEnd(document.getElementById(folderMode))
    }
}

function deleteFolder(){
    if(folderSelectat != 0 && createFolderAcces == 0){
        if(preventServer == 0){
            createFolderAcces = 1
            preventServer = 1
            $('#angajat').prop('disabled', true)
            $.ajax({
                url: urlFile,
                type: 'POST',
                data: {
                    'angajat': angajatObiect['id'],
                    'path-delete': folderSelectat,
                    'delete-file':'true'
                },
                success: function(result) {
                    result = cleanSpaces(result)
                    preventServer = 0
                    createFolderAcces = 0
                    if(result == 'true'){
                        folderSelectat = 0
                        multipleEfect(['#download-file', '#edit-folder', '#delete-folder', 
                        '#move-folder', '#cut-folder'], 'addClass', 'disabled-effect')
                        var arrayForPhpFoldere = {
                            'angajat': angajatObiect['id'],
                            'cale-folder': path,
                            'search-foldere': 'true'
                        }
                        sendCommandPhp(arrayForPhpFoldere, 3)
                    }
                    else{
                        mesajEroareFolder('', result)
                    }
                    $('#angajat').prop('disabled', false)
                },
                error: function(result){
                    createFolderAcces = 0
                    mesajEroareFolder('', 'Ceva nu a functionat. Te rugam sa incerci din nou!')
                    preventServer = 0
                    $('#angajat').prop('disabled', false)
                }
            })
        }
    }
}

function uploadFile(){
    if(createFolderAcces == 0){
        var fileInput = document.getElementById('file-input');
        var file = fileInput.files[0]
        var formData = new FormData()
        formData.append('angajat', angajatObiect['id'])
        formData.append('cale-document', path)
        formData.append('file', file)

        if(preventServer == 0 && fileInput.files.length > 0){
            preventServer = 1
            $('#angajat').prop('disabled', true)
            $.ajax({
                url: urlFile,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(result) {
                    result = cleanSpaces(result)
                    preventServer = 0
                    if(result == 'true'){
                        folderSelectat = 0
                        $('#edit-folder').addClass('disabled-effect')
                        $('#delete-folder').addClass('disabled-effect')
                        $('#move-folder').addClass('disabled-effect')
                        $('#cut-folder').addClass('disabled-effect')
                        var arrayForPhpFoldere = {
                            'angajat': angajatObiect['id'],
                            'cale-folder': path,
                            'search-foldere': 'true'
                        }
                        sendCommandPhp(arrayForPhpFoldere, 3)
                    }
                    else{
                        mesajEroareFolder('', result)
                    }
                    $('#angajat').prop('disabled', false)
                    document.getElementById('file-input').value = ''
                },
                error: function(result){
                    mesajEroareFolder('', 'Ceva nu a functionat. Te rugam sa incerci din nou!')
                    preventServer = 0
                    $('#angajat').prop('disabled', false)
                }
            })
        }
    }
}

function downloadDoc(el, tipEvent){
    if(createFolderAcces == 0){
        var fisier = 0
        if(tipEvent == 'buton'){
            fisier = folderSelectat
        }
        else if(tipEvent == 'event'){
            fisier = el.id
        }
        if(fisier != 0){
            var arrayForPhpFoldere = {
                'angajat': angajatObiect['id'],
                'fisier': fisier,
                'download-file': 'true'
            }
            sendCommandPhp(arrayForPhpFoldere, 6)
        }
    }
}

function downloadPhp(response){
    var response = JSON.parse(response);
    var numeFisier = response['numeFisier'];
    var tipMime = response['tipMime'];
    var continutBase64 = response['continut'];
    var byteCharacters = atob(continutBase64);

    // Convertă byteCharacters într-un array de tip Uint8
    var byteNumbers = new Array(byteCharacters.length);
    for (var i = 0; i < byteCharacters.length; i++) {
        byteNumbers[i] = byteCharacters.charCodeAt(i);
    }
    var byteArray = new Uint8Array(byteNumbers);
    var blob = new Blob([byteArray], { type: tipMime });

    // Crează un link de descărcare și configurează proprietățile sale
    var link = document.createElement('a');
    link.href = window.URL.createObjectURL(blob);
    link.download = numeFisier; 
    document.body.appendChild(link);
    link.click();
    window.URL.revokeObjectURL(link.href);
}

function backPath(){
    if(pathTravel != 0 && pathTravel != 'R:' && createFolderAcces == 0 && indexTravel > 1  && preventServer == 0){
        folderSelectat = 0
        multipleEfect(['#download-file','#edit-folder', '#delete-folder', '#move-folder', '#cut-folder'], 'addClass', 'disabled-effect')
        $('#redo-id').removeClass('disabled-effect')
        var pathTravelLocal = ''
        var pathTravelSplit = pathTravel.split(':')
        pathTravelSplit = pathTravelSplit[1].split('/')

        indexTravel--
        for (let index = 0; index < indexTravel - 1; index++) {
            pathTravelLocal += pathTravelSplit[index] + '/'
        }

        if(indexTravel == 1){
            $('#undo-id').addClass('disabled-effect')
        }

        path = 'R:' +  pathTravelLocal
        var arrayForPhpFoldere = {
            'angajat': angajatObiect['id'],
            'cale-folder': path,
            'input-foldere': 'true'
        }
        sendCommandPhp(arrayForPhpFoldere, 7)
    }
}

function nextPath(){
    if(pathTravel != 0 && createFolderAcces == 0 && indexTravel < pathTravel.split('/').length && preventServer == 0){
        folderSelectat = 0
        multipleEfect(['#download-file','#edit-folder', '#delete-folder', '#move-folder', '#cut-folder'], 'addClass', 'disabled-effect')
        $('#undo-id').removeClass('disabled-effect')
        var pathTravelLocal = ''
        var pathTravelSplit = pathTravel.split(':')
        pathTravelSplit = pathTravelSplit[1].split('/')

        indexTravel++
        for (let index = 0; index < indexTravel - 1; index++) {
            pathTravelLocal += pathTravelSplit[index] + '/'
        }

        if(indexTravel == pathTravel.split('/').length){
            $('#redo-id').addClass('disabled-effect')
        }

        path = 'R:' +  pathTravelLocal
        var arrayForPhpFoldere = {
            'angajat': angajatObiect['id'],
            'cale-folder': path,
            'input-foldere': 'true'
        }
        sendCommandPhp(arrayForPhpFoldere, 7)
    }
}

function pathTravelFun(result){
    if(result == 'true'){
        var arrayForPhpFoldere = {
            'angajat': angajatObiect['id'],
            'cale-folder': path,
            'search-foldere': 'true'
        }
        sendCommandPhp(arrayForPhpFoldere, 3)
    }
    else{
        path = 'R:'
        pathTravel = path
        indexTravel = pathTravel.split('/').length
        $('#undo-id').addClass('disabled-effect')
        $('#redo-id').addClass('disabled-effect')
        mesajEroareFolder('', 'Acest folder nu exista!')
        var arrayForPhpFoldere = {
            'angajat': angajatObiect['id'],
            'cale-folder': path,
            'search-foldere': 'true'
        }
        sendCommandPhp(arrayForPhpFoldere, 3)
    }
}

$(document).ready(function () {
    listaAngajati('no')
})