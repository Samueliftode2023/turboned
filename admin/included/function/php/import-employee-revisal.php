<?php
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

    function execute_import_revisal($file, $conectareDB) {
        $judete = ["Alba","Arad","Arges","Bacau","Bihor","Bistrita-Nasaud","Botosani","Braila",
        "Brasov","Bucuresti","Buzau","Calarasi","Caras-Severin","Cluj","Constanta","Covasna","Dambovita","Dolj","Galati","Giurgiu","Gorj",
        "Harghita","Hunedoara","Ialomita","Iasi","Ilfov","Maramures","Mehedinti",
        "Mures","Neamt","Olt","Prahova","Salaj","Satu Mare","Sibiu","Suceava","Teleorman","Timis","Tulcea","Valcea","Vaslui","Vrancea"];
        $obiecte = [];

        try {
            $pdo = new PDO('sqlite:' . $file);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->query("SELECT * FROM Salariat");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($results as $row) {
                $object = [
                    'nume' => 'nevalid',
                    'genul' => 'Masculin',
                    'apatrid' => 'none',
                    'cetatenie' => 'nevalid',
                    'emitere_aviz' => 'nevalid',
                    'expirare_aviz' => 'nevalid',
                    'tip_aviz' => 'nevalid',
                    'tara' => 'nevalid',
                    'judetul' => 'nevalid',
                    'localitatea' => 'nevalid', 
                    'adresa' => 'nespecificat',
                    'telefon_1' => 'nespecificat',
                    'telefon_2' => 'nespecificat',
                    'email_1' => 'nespecificat',
                    'email_2' => 'nespecificat',
                    'tip_act' => 'nevalid',
                    'pasaport' => 'nevalid',
                    'cnp' => 'nevalid',
                    'seria' => 'nevalid',
                    'numar_serie' => 'nevalid',
                    'data_emiterii' => 'nespecificat',
                    'data_expirarii' => 'nespecificat',
                    'emis_de' => 'nespecificat',
                    'casatorit' => 'Nu',
                    'copii' => 0,
                    'persoane_in_intretinere' => 0,
                    'observatii' => 'nespecificat',
                    'imagine' => 'user.png',
                    'origin' => 'revisal',
                    'id_revisal' => 0,
                    'radiat' => 0
                ];

                $object['nume'] = $row['Nume'] . ' ' . $row['Prenume'];

                if($row['Apatrid'] !== null){
                    $object['apatrid'] = $row['Apatrid'];
                    $object['cetatenie'] = 'nevalid';
                    if($row['Apatrid'] == 0){
                        $object['tip_aviz'] = bin2hex($row['DetaliiSalariatStrainId']);
                    }
                }
                else{
                    $object['cetatenie'] = bin2hex($row['NationalitateId']);
                    $object['tip_aviz'] = bin2hex($row['DetaliiSalariatStrainId']);
                }

                $object['tara'] = bin2hex($row['TaraDomiciliuId']);
                $object['localitatea'] = bin2hex($row['LocalitateId']);
                $object['tip_act'] = $row['TipActIdentitate'];

                if($row['TipActIdentitate'] > 1){
                    $object['genul'] = verificaSexCNP($row['Cnp']);
                    $object['cnp'] = $row['Cnp'];
                }
                else if($row['TipActIdentitate'] == 1){
                    $object['pasaport'] = $row['Cnp'];
                }
                else{
                    $object['genul'] = verificaSexCNP($row['Cnp']);
                    $object['cnp'] = $row['Cnp'];
                    $object['numar'] = 'nespecificat';
                    $object['serie'] = 'nespecificat';
                }
                if($row['Mentiuni'] !== null){
                    $object['observatii'] = $row['Mentiuni'];
                }
                $object['id_revisal'] = bin2hex($row['Id']);
                $object['radiat'] = $row['Radiat'];
                array_push($obiecte, $object);
            }
    
            for ($i = 0; $i < count($obiecte); $i++) { 
                if($obiecte[$i]['cetatenie'] != 'nevalid'){
                    $stmto = $pdo->query("SELECT * FROM Nationalitate WHERE Id = x'".$obiecte[$i]['cetatenie']."'"); 
                    $results = $stmto->fetchAll(PDO::FETCH_ASSOC);

                    if (count($results) > 0) {
                        $obiecte[$i]['cetatenie'] = $results[0]['Nume'];
                    } 
                }
                $stmto = $pdo->query("SELECT * FROM Nationalitate WHERE Id = x'".$obiecte[$i]['tara']."'"); 
                $results = $stmto->fetchAll(PDO::FETCH_ASSOC);
                $obiecte[$i]['tara'] = $results[0]['Nume'];

                $stmto = $pdo->query("SELECT * FROM Localitate WHERE Id = x'".$obiecte[$i]['localitatea']."'"); 
                $results = $stmto->fetchAll(PDO::FETCH_ASSOC);

                if(count($results) > 0){
                    $obiecte[$i]['judetul'] = $judete[$results[0]['Judet']];
                    $obiecte[$i]['localitatea'] = $results[0]['Nume'];
                }
                else{
                    $obiecte[$i]['judetul'] = '';
                    $obiecte[$i]['localitatea'] = '';
                }

                if($obiecte[$i]['apatrid'] == 0 || $obiecte[$i]['cetatenie'] != 'România' && $obiecte[$i]['cetatenie'] != 'nevalid'){
                    $stmto = $pdo->query("SELECT * FROM DetaliiSalariatStrain WHERE Id = x'".$obiecte[$i]['tip_aviz']."'"); 
                    $results = $stmto->fetchAll(PDO::FETCH_ASSOC);

                    if(count($results) > 0){
                        if($results > 1){
                            $obiecte[$i]['emitere_aviz'] = 'nespecificat';
                            $obiecte[$i]['expirare_aviz'] = 'nespecificat';
                            if($results[0]['DataInceputAutorizatie'] !== null){
                                $obiecte[$i]['emitere_aviz'] = substr($results[0]['DataInceputAutorizatie'], 0, 10);

                            }
                            if( $results[0]['DataSfarsitAutorizatie'] !== null){
                                $obiecte[$i]['expirare_aviz'] = substr($results[0]['DataSfarsitAutorizatie'], 0, 10);
                            }

                            $obiecte[$i]['tip_aviz'] = $results[0]['TipAutorizatie'];
                        }
                        else if($results == 0){
                            $obiecte[$i]['emitere_aviz'] = 'nespecificat';
                            if($results[0]['DataInceputAutorizatie'] !== null){
                                $obiecte[$i]['emitere_aviz'] = substr($results[0]['DataInceputAutorizatie'], 0, 10);
                            }
                            $obiecte[$i]['tip_aviz'] = $results[0]['TipAutorizatie'];
                        }
                        else{
                            $obiecte[$i]['tip_aviz'] = $results[0]['TipAutorizatie'];
                        }
                    }
                }
                else{
                    $obiecte[$i]['tip_aviz'] = 'nevalid';
                }
            }
            $pdo = null;
            $campuri = [
                "nume","genul","apatrid","cetatenie","emitere_aviz","expirare_aviz","tip_aviz",
                "tara","judetul","localitatea","adresa","telefon_1","telefon_2","email_1",
                "email_2","tip_act","pasaport","cnp","seria","numar_serie","data_emiterii",
                "data_expirarii","emis_de","casatorit","copii","persoane_in_intretinere","observatii","imagine", "origin", "id_revisal", "radiat"
            ];

            for ($j=0; $j < count($obiecte); $j++) { 
                $columnValues = '';
                $name_table = $_SESSION['username'].'_'.'angajati';
                $columnHeader = '(';

                for ($i=0; $i < count($campuri); $i++) { 

                    $columnHeader .= $campuri[$i].', ';
                    if(isset($obiecte[$j][$campuri[$i]])){
                        $valuesFiltrat = mysqli_real_escape_string($conectareDB, InlocuireCharactere($obiecte[$j][$campuri[$i]]));
                        $columnValues .= '"'.$valuesFiltrat.'", ';
                    }
                }

                $columnHeader = substr(InlocuireCharactere($columnHeader), 0, -1);
                $columnHeader = $columnHeader.')';
                $columnValues = substr(InlocuireCharactere($columnValues), 0, -1);
        
                if($obiecte[$j]['cnp'] != 'nevalid'){
                    $colCnpTip = 'cnp';
                    $colCnpTipVal = $obiecte[$j]['cnp'];
                }
                else if($obiecte[$j]['pasaport'] != 'nevalid'){
                    $colCnpTip = 'pasaport';
                    $colCnpTipVal = $obiecte[$j]['pasaport'];
                }

                if(!is_table('angajati', $conectareDB)){
                    create_table($object, $conectareDB);
                }
                
                $sql = 'SELECT * FROM '.$name_table.' WHERE '.$colCnpTip.' = "'.$colCnpTipVal.'"';
                $result = mysqli_query($conectareDB, $sql);
                $row = mysqli_num_rows($result);

                if($row == 0){
                    $sql = "INSERT INTO ".$name_table." ".$columnHeader."
                    VALUES(".$columnValues.")";
                    if(mysqli_query($conectareDB, $sql)){
                        echo '<div style="color:green;">Ati adaugat: '.$obiecte[$j]['nume'].'</div><br><br>';
                    }
                }
                else{
                    echo '<div style="color:red;">Acest angajat exista deja in baza de date: '.$obiecte[$j]['nume'].'</div><br><br>';
                }
            }
        } catch (PDOException $e) {
            die('Eroare la execuția importului Revisal: ' . $e->getMessage());
        }
    }

    function verificaSexCNP($cnp) {
        if (strlen($cnp) != 13 || !ctype_digit($cnp)) {
            return 'Masculin';
        }
    
        // Prima cifră din CNP
        $primaCifra = $cnp[0];
    
        // Verificăm sexul pe baza primei cifre
        switch ($primaCifra) {
            case '1':
            case '3':
            case '5':
            case '7':
                return "Masculin";
            case '2':
            case '4':
            case '6':
            case '8':
                return "Feminin";
            default:
                return "Masculin";
        }
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
?>