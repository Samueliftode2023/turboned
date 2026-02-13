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
        $tabel = $_SESSION['username'].'_'.'contracte';
        $tabel_angajati = $_SESSION['username'].'_'.'angajati';

        if(!is_table('contracte', $conectareDB)){
            create_table($conectareDB);
        }
        try {
            $pdo = new PDO('sqlite:' . $file);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->query("SELECT * FROM Contract");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $row) {
                $contract = array(
                    'Id' => bin2hex($row['Id']),
                    'TipActualizare' => $row['TipActualizare'],
                    'DataConsemnare' => $row['DataConsemnare'],
                    'TipDurata' => $row['TipDurata'],
                    'TipNorma' => $row['TipNorma'],
                    'TipContract' => $row['TipContract'],
                    'NumarContract' => $row['NumarContract'],
                    'NumereContractVechi' => $row['NumereContractVechi'],
                    'DataContract' => $row['DataContract'],
                    'DateContractVechi' => $row['DateContractVechi'],
                    'DataInceputContract' => $row['DataInceputContract'],
                    'DataSfarsitContract' => $row['DataSfarsitContract'],
                    'Salariu' => $row['Salariu'],
                    'ExceptieDataSfarsit' => $row['ExceptieDataSfarsit'],
                    'Detalii' => $row['Detalii'],
                    'CorId' => bin2hex($row['CorId']),
                    'StareCurentaId' => bin2hex($row['StareCurentaId']),
                    'SalariatId' => bin2hex($row['SalariatId']),
                    'TimpMuncaNorma' => $row['TimpMuncaNorma'],
                    'TimpMuncaRepartizare' => $row['TimpMuncaRepartizare'],
                    'TimpMuncaIntervalTimp' => $row['TimpMuncaIntervalTimp'],
                    'TimpMuncaDurata' => $row['TimpMuncaDurata'],
                    'DetaliiMutareId' => $row['DetaliiMutareId'],
                    'Radiat' => $row['Radiat']
                );
                $sql = 'SELECT * FROM '.$tabel.' WHERE Id = "'.$contract['Id'].'"';
                $interogare = mysqli_query($conectareDB, $sql);
                $numar_rand = mysqli_num_rows($interogare); 
                
                $sqlAngajat = 'SELECT * FROM '.$tabel_angajati.' WHERE id_revisal = "'.$contract['SalariatId'].'"';
                $interogare_angajat = mysqli_query($conectareDB, $sqlAngajat);
                $angajat_exitent = mysqli_num_rows($interogare_angajat); 

                if($numar_rand == 0 && $angajat_exitent >= 1){
                    $columnValues = '';
                    $columnHeader = '(';
                    foreach ($contract as $key => $value) {
                        $columnHeader .= $key.', ';
                        $valuesFiltrat = mysqli_real_escape_string($conectareDB, $value);
                        $columnValues .= '"'.$valuesFiltrat.'", ';
                    }
                    $columnHeader = substr(InlocuireCharactere($columnHeader), 0, -1);
                    $columnHeader = $columnHeader.')';
                    $columnValues = substr(InlocuireCharactere($columnValues), 0, -1);

                    $sql = "INSERT INTO ".$tabel." ".$columnHeader."
                    VALUES(".$columnValues.")";
                    if(!mysqli_query($conectareDB, $sql)){
                        echo 'Ceva nu a functionat! Incearca din nou!';
                    }
                    else{
                        echo 'Incarcare completa!';
                    }
                }
            }

        } catch (PDOException $e) {
            die('Eroare la execuția importului Revisal: ' . $e->getMessage());
        }
    }

    function create_table($conectareDB){
        $tabel = $_SESSION['username'].'_'.'contracte';
        $sql = "
            CREATE TABLE ".$tabel." (
            Id CHAR(36) NOT NULL, 
            TipActualizare INT, 
            DataConsemnare DATETIME NOT NULL, 
            TipDurata INT NOT NULL, 
            TipNorma INT NOT NULL, 
            TipContract INT NOT NULL, 
            NumarContract NVARCHAR(255) NOT NULL, 
            NumereContractVechi NVARCHAR(255), 
            DataContract DATETIME NOT NULL, 
            DateContractVechi NVARCHAR(255), 
            DataInceputContract DATETIME NOT NULL, 
            DataSfarsitContract DATETIME, 
            Salariu INT NOT NULL, 
            ExceptieDataSfarsit INT, 
            Detalii NVARCHAR(255), 
            CorId CHAR(36) NOT NULL, 
            StareCurentaId CHAR(36) NOT NULL, 
            SalariatId CHAR(36) NOT NULL, 
            TimpMuncaNorma INT, 
            TimpMuncaRepartizare INT, 
            TimpMuncaIntervalTimp INT, 
            TimpMuncaDurata NUMERIC, 
            DetaliiMutareId CHAR(36), 
            Radiat INT NOT NULL DEFAULT 0, 
            CONSTRAINT NumarData UNIQUE (NumarContract, DataContract), 
            CONSTRAINT PK_Contract PRIMARY KEY (Id)
            );
        ";
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