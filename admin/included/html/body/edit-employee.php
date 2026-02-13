<div id='container'>
    <form id='search-angajati'>
        <label>
            Nume
            <select onchange='readEmployee(this,"start")' name="angajat" id="angajat"></select>
        </label>
        <div id='imagine-salariat'>
            <label for='up-photo' id='new-photo' class='centrare-poza-det dis-none'>Schimba poza</label>
            <input onchange='uploadPhoto(this)' id='up-photo' accept='.jpg, .png, .jpeg' type='file' class='dis-none' name='file'> 
        </div>
    </form>
    <div id='cititor-angajati' class='dis-none'></div>
    <div id='mesaj-ajutor'>
        <?php
            message_table($conectareDB);
        ?>
    </div>
    <div id='campuri-form' class='back-editare-campuri dis-none'>
        <div class='blur-efect'></div>
        <form id='editare-campuri'>
            <div onclick='closeEditor()' id='close-editor'>
                <span class="material-symbols-outlined">close</span>
            </div>
            <div id='campuri-container'></div>
            <br>
            <button class='pozitionare-dreapta'>Salveaza</button>
        </form>
    </div>
    <div id='delete-angajat' class='delete-buton dis-none'>
        <div onclick='openDelete()' class='vertical-center'>Sterge salariatul</div>
    </div>
    <div id='panou-stergere' class='dis-none'>
        Esti sigur ca doresti sa elimini angajatul?
        <br><br><br>
        <button class='red-yes' onclick='stergeAngajat()'>Da</button>
        <button class='green-no' onclick='closeDelete()'>Nu</button>
    </div>
</div>