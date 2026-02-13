<?php
    function check_if_is_set($array){
        for ($i=0; $i < count($array); $i++) { 
            if(!isset($_POST[$array[$i]])){
                echo 'A aparut o eroare! Te rugam sa incerci din nou!';
                return false;
            }
        }
        return true;
    }

    function create_folder($conectareDB){
        $specialCharacters = '/[<>;:\'\"\/\\\\|.,?*!]/';
        $specialCharactersPath = '/[<>;\'"|.,?*!]/';
        $tabel = $_SESSION['username'].'_'.'documente';
        $angajat = InlocuireCharactere($_POST['angajat']);
        $cale_folder = InlocuireCharactere(strtoupper($_POST['cale-folder']));
        $nume_folder = str_replace('&nbsp;', ' ', $_POST['nume-folder']);
        $nume_folder = InlocuireCharactere($nume_folder);
        $new_folder = $cale_folder.strtoupper(str_replace(' ', '-', $nume_folder)).'/';
        
        if(strlen($nume_folder) <= 0){
            echo 'Folderul nu are un nume!';
        }
        else if(strlen($nume_folder) >= 217){
            echo 'Numele este prea mare!';
        }
        else if(preg_match($specialCharacters, $nume_folder)){
            echo "Numele nu trebuie sa continta < > ; : '' ' / \ | . , ? * !.";
        }
        else if(preg_match($specialCharactersPath, $cale_folder)){
            echo "Eroare de stocare!";
        }
        else if(bad_path($tabel, $angajat, $cale_folder, 'folder', $conectareDB) && $cale_folder != 'R:'){
            echo "Locul unde doresti sa creezi folderul nu exista!";
        }
        else if(!bad_path($tabel, $angajat, $new_folder, 'folder', $conectareDB)){
            echo "Aceasta nume exista!";
        }
        else{
            insert_folder($angajat, $new_folder, 'folder', $nume_folder, $conectareDB);
        }
    }

    function is_table($name_table, $conectareDB){
        $tabel = $_SESSION['username'].'_'.$name_table;
        $sql = "SHOW TABLES LIKE '".$tabel."'";
        $result = mysqli_query($conectareDB, $sql);

        if(mysqli_num_rows($result) == 0){
            return false;
        }
        return true;
    }

    function has_row($name_table, $conectareDB){
        $tabel = $_SESSION['username'].'_'.$name_table;
        $sql = "SELECT * from ".$tabel."";
        $result = mysqli_query($conectareDB, $sql);
        $number_row = mysqli_num_rows($result); 

        if($number_row > 0){
            return true;
        }
        return false;
    }

    function message_table($conectareDB){
        if(is_table('angajati', $conectareDB)){
            if(has_row('angajati', $conectareDB)){
                echo "Foloseste campul de selectare pentru a cauta documentele salariatului!<br>";
            }
            else{
                echo "Momentan in baza de date nu exista niciun salariat adaugat!
                <br><a href='../add-employee/'>Adauga un salariat</a>";
            }
        }
        else{
            echo "Momentan in baza de date nu exista niciun salariat adaugat!
            <br><a href='../add-employee/'>Adauga un salariat</a>";
        }
    }

    function create_table($conectareDB){
        $tabel = $_SESSION['username'].'_'.'documente';
        $sql = "CREATE TABLE ".$tabel." (
            id INT(6) AUTO_INCREMENT PRIMARY KEY,
            angajat VARCHAR(255),
            tip VARCHAR(255),
            nume VARCHAR(255),
            cale LONGTEXT NOT NULL,
            document LONGBLOB NOT NULL,
            mime VARCHAR(255)
        )";
        if(!mysqli_query($conectareDB, $sql)){
            echo 'Ceva nu a functionat. Te rugam sa incerci din nou!';
            return false;
        }
        return true;
    }
    function insert_folder($idAngajat, $cale, $tip, $nume, $conectareDB){
        $name_table = $_SESSION['username'].'_'.'documente';
        $columnHeader = '(angajat, tip, nume, cale)';
        $idAngajat = mysqli_real_escape_string($conectareDB, $idAngajat);
        $tip = mysqli_real_escape_string($conectareDB, $tip);
        $nume = mysqli_real_escape_string($conectareDB, $nume);
        $cale = mysqli_real_escape_string($conectareDB, $cale);
        $columnValues = "'".$idAngajat."', '".$tip."', '".$nume."', '".$cale."'";

        $sql = "INSERT INTO ".$name_table." ".$columnHeader."
        VALUES(".$columnValues.")";

        if(mysqli_query($conectareDB, $sql)){
            echo 'true';
        }
        else{
            echo 'Ceva nu a functionat, incearca din nou!';
        }
    }
    
    function search_folder($conectareDB){
        $id = mysqli_real_escape_string($conectareDB, $_POST['angajat']);
        $cale = $_POST['cale-folder'];
        $name_table = $_SESSION['username'].'_'.'documente';
        $numar_foldere = explode(':',$cale);
        $numar_foldere = count($numar_foldere) - 1;

        $check_slash = explode('/', $cale);
        $numar_slash = count($check_slash);

        if($numar_slash > 0){
            $numar_foldere = $numar_slash;
        }
        $cale = mysqli_real_escape_string($conectareDB, $_POST['cale-folder']);
        $sql = "SELECT * FROM ".$name_table." WHERE angajat=".$id." AND LENGTH(cale) - LENGTH(REGEXP_REPLACE(cale, '[/.]', '')) = ".$numar_foldere. " AND cale LIKE '" .$cale. "%'";
        $result = $conectareDB->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if($row['tip'] == 'folder'){
                    $span = "<span class='material-symbols-outlined'>folder</span>";
                    $folder = "<div id='".$row['cale']."'ondblclick='insideFolder(this)' onmouseover='openEfect(this,\"folder\")' onmouseout='closeEfect(this,\"folder\")' onclick='showAllDescriere(this)' class='file-class'>".$span;
                    $folder = $folder."<div class='descriere descriere-incompleta'>".$row['nume']."</div></div>";
                    echo $folder;
                }
                else if($row['tip'] == 'document'){
                    $span = "<span class='material-symbols-outlined'>draft</span>";
                    $folder = "<div id='".$row['cale']."'ondblclick='downloadDoc(this,\"event\")' onmouseover='openEfect(this,\"document\")' onmouseout='closeEfect(this,\"document\")' onclick='showAllDescriere(this)' class='file-class'>".$span;
                    $folder = $folder."<div class='descriere descriere-incompleta'>".$row['nume']."</div></div>";
                    echo $folder;
                }
            }
        }
        else{
            echo '<div id="error-show">Folder gol</div>';
        }
    }

    function is_folder($conectareDB){
        $id = mysqli_real_escape_string($conectareDB, InlocuireCharactere($_POST['angajat']));
        $cale = mysqli_real_escape_string($conectareDB, InlocuireCharactere($_POST['cale-folder']));
        $name_table = $_SESSION['username'].'_'.'documente';
        $sql = "SELECT * FROM ".$name_table." WHERE tip = 'folder' AND angajat=".$id." AND cale LIKE '" . $cale . "%'";
        $result = $conectareDB->query($sql);

        if($result->num_rows > 0){
            echo 'true';
        }
        else{
            echo 'false';
        }
    }
    function rewrite_files($conectareDB){
        $name_table = $_SESSION['username'].'_'.'documente';
        $specialCharacters = '/[<>;:\'\"\/\\\\|.,?*!]/';
        $specialCharactersPath = '/[<>;\'"|,?*!]/';
        $id = mysqli_real_escape_string($conectareDB, InlocuireCharactere($_POST['angajat']));
        $cale = $_POST['cale-folder'];
        $new_name = $_POST['new-name'];
        $new_name = str_replace('&nbsp;', ' ', $new_name);
        $new_name = InlocuireCharactere($new_name);
        $descriere = strtoupper(str_replace(' ', '-', $new_name));
        $folderDeEditat = $_POST['folder-name'];
        $last_car = substr($folderDeEditat, -1);

        if(strlen($new_name) <= 0){
            echo 'Folderul nu are un nume!';
        }
        else if(strlen($new_name) >= 217){
            echo 'Numele este prea mare!';
        }
        else if(preg_match($specialCharacters, $new_name)){
            echo "Numele nu trebuie sa continta < > ; : '' ' / \ | . , ? * !.";
        }
        else if(preg_match($specialCharactersPath, $cale)){
            echo "Eroare de stocare!";
        }
        else if($last_car == '.' || $last_car == '/'){
            if($last_car == '.'){
                $new_file = $cale.$descriere.$last_car;
                $type_file_doc = 'document';
            }
            else if($last_car == '/'){
                $new_file = $cale.$descriere.$last_car;
                $type_file_doc = 'folder';
            }
            if(bad_path($name_table, $id, $new_file, $type_file_doc, $conectareDB)){
                $folderDeEditat = mysqli_real_escape_string($conectareDB, $folderDeEditat);
                $new_file = mysqli_real_escape_string($conectareDB, $new_file);

                $sql = "UPDATE ".$name_table."
                SET nume = CASE
                WHEN angajat = ".$id." AND cale = '".$folderDeEditat."' THEN '".$new_name."'
                ELSE nume
                END,
                cale = REPLACE(cale, '".$folderDeEditat."', '".$new_file."')
                WHERE angajat='".$id."' AND cale LIKE '".$folderDeEditat."%'"; 
    
                if(mysqli_query($conectareDB, $sql)){
                    echo 'true';
                }
                else{
                    echo 'false';
                }
            }
            else{
                echo 'Acest nume exista!';
            }
        }
    }

    function bad_path($name_table, $id, $path, $tip_path, $conectareDB){
        $id = mysqli_real_escape_string($conectareDB, $id);
        $path = mysqli_real_escape_string($conectareDB, $path);
        $tip_path = mysqli_real_escape_string($conectareDB, $tip_path);
        $sql = "SELECT * FROM ".$name_table." WHERE angajat=".$id." AND cale = '".$path."' AND tip = '".$tip_path."'";
        $result = mysqli_query($conectareDB, $sql);

        if(mysqli_query($conectareDB, $sql)){
            if($result->num_rows == 0){
                return true;
             }
             return false;
        }
        else{
            return true;
        }
    }

    function is_in_path($fragment, $content){
        $numar_foldere_vechi = count(explode('/', $fragment));

        $content = explode('/', $content);
        $numar_foldere_nou = count($content);

        if($numar_foldere_nou >= $numar_foldere_vechi){
            $check_if_are_same = '';
            for ($i=0; $i < $numar_foldere_vechi - 1; $i++) { 
                $check_if_are_same .= $content[$i].'/';
            }
            if($fragment == $check_if_are_same){
                return true;
            }
        }
        return false;
    }
    function composer_n_path($old_path, $new_path){
        $old_path_editor = explode(':',$old_path);

        if(strpos($old_path, '.') !== false){
            $old_path_editor = explode('.', $old_path_editor[1]);
            $old_path_editor = explode('/', $old_path_editor[0]);
            $old_path_editor = $old_path_editor[count($old_path_editor) - 1];
            $car = '.';
        }
        else{
            $old_path_editor = explode('/', $old_path_editor[1]); 
            $old_path_editor = $old_path_editor[count($old_path_editor) - 2];
            $car = '/';
        }

        $composer_new_path = $new_path.''.$old_path_editor.$car;
        return $composer_new_path;
    }
    function paste_folder($conectareDB){
        $name_table = $_SESSION['username'].'_'.'documente';
        $id = InlocuireCharactere($_POST['angajat']);
        $old_path = InlocuireCharactere($_POST['old-path']);
        $new_path = InlocuireCharactere($_POST['new-path']);
        $tip_path = 'folder';
        $mode = $_POST['move-mode'];
        $composer_new_path = composer_n_path($old_path, $new_path);
        
        if(strpos($old_path, '.') !== false){
            $tip_path = 'document';
        }

        if(strpos($new_path, '.') !== false){
            echo 'Nu poti muta sau copia un fisier in alt fisier, incearca intr-un folder!';
            return false;
        }
        else if(bad_path($name_table, $id, $old_path, $tip_path, $conectareDB)){
            echo 'A aparut o eroare! Folderul pe care l-ai copiat a fost sters sau redenumit, te rugam sa incerci din nou!';
            return false;
        }
        else if($new_path != 'R:' && bad_path($name_table, $id, $new_path, 'folder', $conectareDB)){
            echo 'A aparut o eroare! Locul unde doresti sa muti sau sa lipesti folderul nu exista.';
            return false;
        }
        else if(is_in_path($old_path, $new_path)){
            echo 'Nu poti muta sau copia un folder in acelasi folder!';
            return false;
        }
        else if(!bad_path($name_table, $id, $composer_new_path,  $tip_path, $conectareDB)){
            echo 'In locul unde doresti sa muti sau sa lipesti folderul, exista deja un folder cu aceeasi denumire!';
            return false;
        }
        else{
            $new_path = $composer_new_path;

            if($mode == 'cut'){
                $id = mysqli_real_escape_string($conectareDB, $id);
                $old_path = mysqli_real_escape_string($conectareDB, $old_path);
                $new_path = mysqli_real_escape_string($conectareDB, $new_path);

                $sql = "UPDATE ".$name_table."
                SET cale = REPLACE(cale, '".$old_path."', '".$new_path."')
                WHERE angajat = ".$id.";";

                if(mysqli_query($conectareDB, $sql)){
                    echo 'true';
                }
                else{
                    echo 'false';
                }
            }
            else if($mode == 'copy'){
                $id = mysqli_real_escape_string($conectareDB, $id);
                $old_path = mysqli_real_escape_string($conectareDB, $old_path);
                $new_path = mysqli_real_escape_string($conectareDB, $new_path);

                $sql = "SELECT * FROM ".$name_table." WHERE angajat = '".$id."' AND cale LIKE '".$old_path."%'";
                $result = mysqli_query($conectareDB, $sql);
                $interogari_inserare = [];
    
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $file_doc = mysqli_real_escape_string($conectareDB, $row['document']);
                        $new_row = explode($old_path, $row["cale"]);
                        $new_row = $new_path.$new_row[1];
    
                        $interogari_inserare[] = "INSERT INTO ".$name_table." (angajat, tip, nume, cale, document, mime) 
                        VALUES ('".$row['angajat']."', '".$row['tip']."', '".$row['nume']."', '".$new_row."', '".$file_doc."','".$row["mime"]."')";
                    }
                    foreach ($interogari_inserare as $interogare) {
                        mysqli_query($conectareDB, $interogare);
                    }
                    echo 'true';
                }
            }
        }
    }
    function delete_folder($conectareDB){
        $id = mysqli_real_escape_string($conectareDB, InlocuireCharactere($_POST['angajat']));
        $delete_path = mysqli_real_escape_string($conectareDB, InlocuireCharactere($_POST['path-delete']));

        if($delete_path != 'R:'){
            $name_table = $_SESSION['username'].'_'.'documente';
            $sql = "DELETE FROM ".$name_table."  WHERE angajat = '".$id."' AND cale LIKE '".$delete_path."%'";
            if(mysqli_query($conectareDB, $sql)){
                echo 'true';
            }
            else{
                echo 'false';
            }
        }
        else{
            echo 'A aparut o eroare, te rugam sa incerci din nou!';
        }
    }

    function check_file($name, $size, $type_file){
        if(isset($_FILES[$name]['name'])){
            $file_name = $_FILES[$name]['name'];
            $file_size = $_FILES[$name]['size'];
            $file_error = $_FILES[$name]['error'];
            $file_type = $_FILES[$name]['type'];
                
            // VERIFICAM FORMATUL FISIERULUI
                $file_format = explode('.',$file_name);
                $format = strtolower(end($file_format));
                if(!in_array($format, $type_file)){
                    echo 'ERROR:'.'Fisierul are un format nepermis!';
                    return false;
                }
            //
            // VERIFICAM DACA FISIERUL CONTINE ERORI
                if($file_error === 1){
                    echo 'ERROR:'.'Eroare la incarcare!';
                    return false;
                }
            //
            // VERIFICAM MARIMEA FISIERULUI 
                if($file_size > $size){
                    echo 'ERROR:'.'Fisierul este prea mare! Limita este de 5mb.';
                    return false;
                }
            //
            return true;
        }
        else{
            return false;
        }
    }

    function check_path_file($id, $path, $file, $conectareDB){
        $array_format = ['png','jpeg','jpg','pdf','docx','txt'];
        $name_table = $_SESSION['username'].'_'.'documente';
        $path_corectat = '';
        $path = explode(':', $path);
        $path = explode('/', $path[1]);
        $file_name = explode('.',$file['name']);
        $complet_name = '';

        for ($i=0; $i < count($file_name) - 1; $i++) { 
            $complet_name .= $file_name[$i];
        }
        for ($i=0; $i < count($path) - 1; $i++) { 
            $path_corectat .= $path[$i].'/';
        }

        $descriere = $complet_name;
        $path = 'R:'.$path_corectat;
        $complet_name = $path.str_replace(' ', '-', $complet_name).'.';
        $complet_name = strtoupper($complet_name);

        if($path != 'R:' && bad_path($name_table, $id, $path, 'folder', $conectareDB)){
            echo 'Locul in care doresti sa incarci documentul nu exista!';
            return false;
        }
        else if(!bad_path($name_table, $id, $complet_name, 'document', $conectareDB)){
            echo 'In acest folder exista un fisier cu acelasi nume!';
            return false;
        }
        else if(!check_file('file', 5000000, $array_format)){
            return false;
        }
        else{
            upload_data_base($name_table, $id, $complet_name, $file, $descriere, 'document', $file_name[count($file_name) - 1], $conectareDB);
        }
    }
    function upload_data_base($name_table, $id, $path, $file, $descriere, $tip_document, $mime, $conectareDB){
        $fileContent = file_get_contents($file['tmp_name']);
        $fileContent = mysqli_real_escape_string($conectareDB, $fileContent);
        $sql = "INSERT INTO ".$name_table." (angajat, tip, nume, cale, document, mime) 
            VALUES ('".$id."', '".$tip_document."', '".$descriere."', '".$path."', '".$fileContent."', '".$mime."')";
        if(mysqli_query($conectareDB, $sql)){
            echo 'true';
        }
        else{
            echo 'false';
        }
    }
    function download_file($conectareDB){
        $id = mysqli_real_escape_string($conectareDB, $_POST['angajat']);
        $cale = mysqli_real_escape_string($conectareDB, $_POST['fisier']);
        $name_table = $_SESSION['username'].'_'.'documente';

        $sql = "SELECT * FROM ".$name_table." WHERE angajat = ".$id." AND  cale = '".$cale."' AND tip = 'document'";
        $result = $conectareDB->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $numeFisier = $row['nume'];  
            if($row['mime'] == 'pdf'){
                $tipMime = 'application/pdf';
            }
            else if($row['mime'] == 'docx'){
                $tipMime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            }
            else if($row['mime'] == 'png'){
                $tipMime = 'image/'.$row['mime'];
            }
            else if($row['mime'] == 'txt'){
                $tipMime = 'text/plain';
            }
            else{
                $tipMime = 'image/jpeg';
            }
            $continut = $row['document'];  
            $continutBase64 = base64_encode($continut);

            $response = [
                'numeFisier' => $numeFisier,
                'tipMime' => $tipMime,
                'continut' => $continutBase64
            ];
        
            //header('Content-Type: application/pdf');
            echo json_encode($response);
        }
    }
?>
