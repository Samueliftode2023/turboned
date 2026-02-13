var urlFile = '../../../included/function/exe/import-employee-revisal.php'
var preventServer = 0

function uploadFile(){
    var fileInput = document.getElementById('file-revisal');
    var file = fileInput.files[0]
    var formData = new FormData()
    formData.append('file', file)

    if(preventServer == 0 && fileInput.files.length > 0){
        preventServer = 1
        $.ajax({
            url: urlFile,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(result) {
                preventServer = 0
                $('#show-import').html(result)
                $('#show-import').removeClass('dis-none')
                document.getElementById('file-revisal').value = ''
            },
            error: function(result){
                mesajEroareFolder('Ceva nu a functionat. Te rugam sa incerci din nou!')
                preventServer = 0
                $('#angajat').prop('disabled', false)
            }
        })
    }
}


function mesajEroareFolder(mesaj){
    $('#dialog').removeClass('dis-none')
    $('#dialog-mess').text(mesaj)
}