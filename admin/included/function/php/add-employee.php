<?php
    function upload_file($files, $root){
        $file_name = $files['name'];
        $file_tmp_name = $files['tmp_name'];

        $file_format = explode('.', $file_name);
        $format = strtolower(end($file_format));
        $new_name = "user-".uniqid('', true).'.'.$format;

        $new_location = $root.'included/gallery/employee/'.$new_name;
        move_uploaded_file($file_tmp_name, $new_location);
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
    function check_employee_data($object, $conectareDB){
        if(!isset($object['nume'])){
            echo 'Campurile "nume" sau "prenume" nu au fost completate!';
            return false;
        }
        if(check_input_simboluri($object['nume'])){
            echo 'Campurile "nume" sau "prenume" contin simboluri nepermise!';
            return false;
        }
        if(check_input_length($object['nume'], 1, 61)){
            echo 'Campurile "nume" sau "prenume" sunt prea scurte sau depasesc lungimea permisa!';
            return false;
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
        else if(isset($object['pasaport'])){
            if(check_input_length($object['pasaport'], 0, 70)){
               echo 'Seria pasaportului este prea mica!';
               return false; 
            }
            if(check_dubluri_act('pasaport', $object['pasaport'], $conectareDB)){
                echo 'Seria pasaportului exista deja la unul dintre angajati!';
                return false; 
            }
        }
        else{
            echo 'CNP-ul sau seria pasaportului sunt obligatorii!';
            return false;
        }
        return true;
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

    function insert_angajat($object, $conectareDB){
        $columnValues = '';
        $name_table = $_SESSION['username'].'_'.'angajati';
        $columnHeader = '(';
        $campuri = [
            "nume","genul","apatrid","cetatenie","emitere_aviz","expirare_aviz","tip_aviz",
            "tara","judetul","localitatea","adresa","telefon_1","telefon_2","email_1",
            "email_2","tip_act","pasaport","cnp","seria","numar_serie","data_emiterii",
            "data_expirarii","emis_de","casatorit","copii","persoane_in_intretinere","observatii","imagine", "origin", "id_revisal", "radiat"
        ];
        $campuriName = [
            "nume","genul","apatrid","cetatenie","emitere-aviz","expirare-aviz","tip-aviz",
            "tara","judetul","localitatea","adresa","telefon-1","telefon-2","email-1",
            "email-2","tip-act","pasaport","cnp","seria","numar-serie","data-emiterii",
            "data-expirarii","emis-de","casatorit","copii","persoane-in-intretinere","observatii","imagine", "origin" , "id_revisal", "radiat"
        ];
        for ($i=0; $i < count($campuri); $i++) { 
            $columnHeader .= $campuri[$i].', ';
            if(isset($object[$campuriName[$i]])){
                $valuesFiltrat = mysqli_real_escape_string($conectareDB, InlocuireCharactere($object[$campuriName[$i]]));
                if(strlen($valuesFiltrat) <= 0){
                    $columnValues .= '"'.'nespecificat'.'", ';
                }
                else{
                    $columnValues .= '"'.$valuesFiltrat.'", ';
                }
            }
            else{
                if($campuriName[$i] == 'origin'){
                    $columnValues .= '"app", ';
                }
                else if($campuriName[$i] == 'id_revisal'){
                    $columnValues .= '"0", ';
                }
                else if($campuriName[$i] == 'radiat'){
                    $columnValues .= '"0", ';
                }
                else{
                    $columnValues .= '"nevalid", ';
                }
            }
        }
        $columnHeader = substr(InlocuireCharactere($columnHeader), 0, -1);
        $columnHeader = $columnHeader.')';
        $columnValues = substr(InlocuireCharactere($columnValues), 0, -1);

        $sql = "INSERT INTO ".$name_table." ".$columnHeader."
                VALUES(".$columnValues.")";
        mysqli_query($conectareDB, $sql);
        echo 'ok';
    }

    function create_table($object, $conectareDB){
        $tabel = $_SESSION['username'].'_'.'angajati';
        $sql = "CREATE TABLE ".$tabel." (
            id INT(6) AUTO_INCREMENT PRIMARY KEY,
            nume VARCHAR(255),
            genul VARCHAR(255),
            apatrid VARCHAR(255),
            cetatenie VARCHAR(255),
            emitere_aviz VARCHAR(255),
            expirare_aviz VARCHAR(255),
            tip_aviz VARCHAR(255),
            tara VARCHAR(255),
            judetul VARCHAR(255),
            localitatea VARCHAR(255),
            adresa TEXT,
            telefon_1 VARCHAR(255),
            telefon_2 VARCHAR(255),
            email_1 VARCHAR(255),
            email_2 VARCHAR(255),
            tip_act VARCHAR(255),
            pasaport VARCHAR(255),
            cnp VARCHAR(255),
            seria VARCHAR(255),
            numar_serie VARCHAR(255),
            data_emiterii VARCHAR(255),
            data_expirarii VARCHAR(255),
            emis_de TEXT,
            casatorit VARCHAR(255),
            copii INT,
            persoane_in_intretinere INT,
            observatii TEXT,
            imagine VARCHAR(255),
            origin VARCHAR(255),
            id_revisal TEXT,
            radiat VARCHAR(255)
        )";
        mysqli_query($conectareDB, $sql);
        insert_angajat($object, $conectareDB);
    }
?>