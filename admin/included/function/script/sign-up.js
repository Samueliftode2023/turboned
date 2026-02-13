var preventSign = 0;

$(document).ready(function () {
    $('#sign-up').submit(function (event) {
        if(preventSign == 0){
            $('#loading-id').removeClass('dis-none')
            preventSign = 1
            event.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: "../../included/function/exe/sign-up.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function (result) {
                    result = stergeSpatiiInceput(result)
                    if(result === 'ok'){
                        window.location.href = "../../main/";
                    }
                    else{
                        grecaptcha.reset();
                        $('#display-message').text(result);
                        preventSign = 0
                        setTimeout(function() {
                            $('#loading-id').addClass('dis-none');
                        }, 1000);
                    }
                },
                error: function() {
                    $('#display-message').text("A apărut o eroare. Vă rugăm să încercați din nou mai târziu.");
                    preventSign = 0;
                    setTimeout(function() {
                        $('#loading-id').addClass('dis-none');
                    }, 1000);
                }
            })
        }
    })
})