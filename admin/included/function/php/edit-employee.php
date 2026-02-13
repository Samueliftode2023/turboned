<?php
    function upload_file($files, $root){
        $file_name = $files['name'];
        $file_tmp_name = $files['tmp_name'];

        $file_format = explode('.', $file_name);
        $format = strtolower(end($file_format));
        $new_name = "user-".uniqid('', true).'.'.$format;

        $new_location = $root.'included/gallery/employee/'.$new_name;
        move_uploaded_file($file_tmp_name, $new_location);
        $_POST['imagine'] = $new_name;
        echo 'SUCCESS:'.$new_name;
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
                    echo 'ERROR:'.'Fisierul este prea mare! Limita este de 2mb.';
                    return false;
                }
            //
            return true;
        }
        else{
            return false;
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
                echo "Foloseste campul de selectare pentru a cauta un salariat!<br>";
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
    function get_employee($select_element, $conectareDB){
        if(is_table('angajati', $conectareDB)){
            if(has_row('angajati', $conectareDB)){
                $select = '';
                $tabel = $_SESSION['username'].'_'.'angajati';
                $sql = "SELECT * from ".$tabel."";
                $result = mysqli_query($conectareDB, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    if($row['id'] == $select_element){
                        $select = 'selected';
                    }
                    else{
                        $select = '';
                    }
                    echo '<option value="'.$row['id'].'" '.$select.'>'.$row['nume'].'</option>';
                }
            }
        }
    }
    function get_data_employee($id, $conectareDB){
        $tabel = $_SESSION['username'].'_'.'angajati';
        $sql = 'SELECT * FROM '.$tabel.' WHERE id="'.$id.'"';
        $result = mysqli_query($conectareDB, $sql);
        $number_row = mysqli_num_rows($result); 

        if($number_row == 1){
            $row = mysqli_fetch_assoc($result);
            unset($row['origin']);
            unset($row['id_revisal']);
            unset($row['radiat']);
            echo 'ok||'.json_encode($row);
        }
        else{
            echo 'ERROR||Acest angajat nu exista in baza de date!';
        }
    }
    function make_change($object, $conectareDB){
        $nevalidare = '';
        $tabel = $_SESSION['username'].'_'.'angajati';
        $keys = array_keys($object);
        $id = $object['id'];
        $coloana = '';
        $valoare = '';
        $sql = 'SELECT * FROM '.$tabel.' WHERE id="'.$id.'"';
        $result = mysqli_query($conectareDB, $sql);
        $number_row = mysqli_num_rows($result); 

        if($number_row == 1){
            if(isset($object['apatrid'])){
                if($object['apatrid'] != 'none'){
                    $nevalidare .= ", cetatenie = 'nevalid'";
                }
            }
            if(isset($object['tara'])){
                if($object['tara'] != 'România'){
                    $nevalidare .= ", judetul = 'nevalid'";
                    $nevalidare .= ", localitatea = 'nevalid'";
                }
            }
            if(isset($object['apatrid']) && isset($object['cetatenie'])){
                if($object['apatrid'] == 'none' && $object['cetatenie'] == 'România'){
                    $nevalidare .= ", tip_aviz = 'nevalid'";
                    $nevalidare .= ", expirare_aviz = 'nevalid'";
                    $nevalidare .= ", emitere_aviz = 'nevalid'";
                }
            }
            if(isset($object['tip_aviz'])){
                if($object['tip_aviz'] == '1'){
                    $nevalidare .= ", expirare_aviz = 'nevalid'";
                    $nevalidare .= ", emitere_aviz = 'nevalid'";
                }
                else if($object['tip_aviz'] == '0'){
                    $nevalidare .= ", expirare_aviz = 'nevalid'";
                }
            }
            if(isset($object['cnp'])){
                $nevalidare .= ", pasaport = 'nevalid'";
            }
            else if(isset($object['pasaport'])){
                $nevalidare .= ", cnp = 'nevalid'";
            }
            if(isset($object['tip_act'])){
                if($object['tip_act'] != '0'){
                    $nevalidare .= ", seria = 'nevalid'";
                    $nevalidare .= ", numar_serie = 'nevalid'";
                }
                if($object['tip_act'] == '0' && !isset($object['seria']) && !isset($object['numar_serie'])){
                    $nevalidare .= ", seria = 'nespecificat'";
                    $nevalidare .= ", numar_serie = 'nespecificat'";
                }
            }
            foreach ($keys as $key) {
                if($key != 'id'){
                    $coloana = $key;
                    $valoare = mysqli_real_escape_string($conectareDB, InlocuireCharactere($object[$key]));

                    if($valoare == ''){
                        $valoare = 'nespecificat';
                    }

                    $sql = "UPDATE ".$tabel."
                    SET ".$coloana ." = '".$valoare."'".$nevalidare."
                    WHERE id = '".$id."'";
                    mysqli_query($conectareDB, $sql);
                }
            }
            echo $id;
        }
    }

    function check_change_and_make($object, $conectareDB){
        if(count($object) <= 1){
            echo 'Cerere invalida!';
            return false;
        }
        if(isset($object['nume'])){
            if(check_input_simboluri($object['nume'])){
                echo 'Campurile "nume" sau "prenume" contin simboluri nepermise!';
                return false;
            }
            if(check_input_length($object['nume'], 1, 61)){
                echo 'Campurile "nume" sau "prenume" sunt prea scurte sau depasesc lungimea permisa!';
                return false;
            }
        }
        if(isset($object['cnp'])){
            if(!check_cnp($object['cnp'])){
                echo 'CNP-ul introdus este invalid!';
                return false;
            }
            if(check_dubluri_act('cnp', $object['cnp'], $conectareDB)){
                echo 'CNP-ul exista deja la unul dintre angajati!';
                return false; 
            }
        }
        if(isset($object['pasaport'])){
            if(check_input_length($object['pasaport'], 0, 70)){
               echo 'Seria pasaportului este prea mica!';
               return false; 
            }
            if(check_dubluri_act('pasaport', $object['pasaport'], $conectareDB)){
                echo 'Seria pasaportului exista deja la unul dintre angajati!';
                return false; 
            }
        }
        return true;
    }
    function deleteEmployee($id, $conectareDB){
        $tabel = $_SESSION['username'].'_'.'angajati';
        $sql = 'SELECT * FROM '.$tabel.' WHERE id="'.$id.'"';
        $result = mysqli_query($conectareDB, $sql);
        $number_row = mysqli_num_rows($result);

        if($number_row == 1){
            $sql = "DELETE FROM ".$tabel." WHERE id='".$id."'";
            mysqli_query($conectareDB, $sql);
            echo 'SUCCES:ok';
        }
        else{
            echo 'ERROR:Ceva nu a functionat, te rugam sa incerci din nou sau mai tarziu!';
        }
    }
?>