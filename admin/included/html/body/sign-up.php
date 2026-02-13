<div id='container'>
    <form id='sign-up'>
        <h2>BINE AI VENIT!</h2>
        <label>
            Nume utilizator
            <input name='username' minlength='5' maxlength='30' required>
        </label>
        <br>
        <label>
            Nume companie
            <input name='company' minlength='3' maxlength='60' required>
        </label>
        <br>
        <label class='parent-info'>
            Parola
            <details id='info-password-signup'>
                <summary><span class="material-symbols-outlined">info</span></summary>
                <p>Formatul parolei: cel putin o litera mica si una mare si cel putin o cifra,
                    iar parola trebuie sa aiba mai mult de 7 caractere.
                </p>
            </details>
            <input name='password' pattern='(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}' type='password' minlength='8' maxlength='40' required>
        </label>
        <br>
        <label>
            Confirma parola
            <input name='confirm-password' pattern='(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}' type='password' minlength='8' maxlength='40' required>
        </label>
        <br>
        <br>
        <div class="g-recaptcha" data-sitekey="6LevXBAgAAAAAKpnReqgefdcNQYccsRP-EonRjHW"></div>
        <label id='term-label' for='terms-condition'><input id='terms-condition' type='checkbox' required>Accept <a title='terms and conditions' aria-label='terms-of-service' href="terms-of-service/">termenii si conditiile</a></label>
        <br>
        <button class='tag-default'>INREGISTRARE</button>
        <a href='../login/' class='tag-default a-class'>CONECTARE</a>
    </form>
</div>