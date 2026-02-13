<div id='container'>
    <form id='search-angajati'>
        <label>
            Nume
            <select onchange='readEmployee(this,"start")' name="angajat" id="angajat"></select>
        </label>
        <div id='imagine-salariat'></div>
    </form>
    <div id='cititor-angajati' class='dis-none'>
        <div id='bara-de-gestionare'>
            <div onclick='backPath()' id='undo-id' class='disabled-effect'>
                <span class="material-symbols-outlined">
                    arrow_back
                </span>
            </div>
            <div onclick='nextPath()' id='redo-id' class='disabled-effect'>
                <span class="material-symbols-outlined">
                    arrow_forward
                </span>
            </div>
            <label class='bara-path'><input onkeyup='startEventInput(this)' id='input-cale' type='text' placeholder='R:'></label>
            <div></div>
            <div id='cut-folder' onclick='cutFolder(this)' class='disabled-effect'>
                <span class="material-symbols-outlined">
                    content_cut
                </span>
            </div>
            <div id='move-folder' onclick='moveFolder(this)' class='disabled-effect'>
                <span class="material-symbols-outlined">
                    content_copy
                </span>
            </div>
            <div id='paste-folder' onclick='sendLocation()' class='disabled-effect'>
                <span class="material-symbols-outlined">
                    content_paste_go
                </span>
            </div>
            <div id='delete-folder' onclick='deleteFolder()' class='disabled-effect'>
                <span class="material-symbols-outlined">
                    delete
                </span>
            </div>
            <div id='edit-folder' onclick='editFolder()' class='disabled-effect'>
                <span class="material-symbols-outlined">
                    edit_document
                </span>
            </div>
            <div id='download-file' onclick='downloadDoc(folderSelectat,"buton")' class='disabled-effect'>
                <span class="material-symbols-outlined">
                    file_save
                </span>
            </div>
            <input type="file" id="file-input" accept='.jpg, .png, .jpeg, .pdf, .txt, .docx'  onchange='uploadFile()' name="file" class='dis-none'>
            <label for='file-input' id='upload-file'>
                <span class="material-symbols-outlined">
                    upload_file
                </span>
            </label>
            <div onclick='createFolder()' id='create-folder'>
                <span class="material-symbols-outlined">
                    create_new_folder
                </span>
            </div>
        </div>
        <div id='foldere' onclick='backgroundFunction()'>

        </div>
    </div>
    <div id='mesaj-ajutor'>
        <?php
            message_table($conectareDB);
        ?>
    </div>
</div>