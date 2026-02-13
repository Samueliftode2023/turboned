<?php
    session_start();
    include_once $root.'included/data-base/index.php';
    function InlocuireCharactere($StringDeCorectat){
        $StringDeCorectat = str_Replace("  "," ",$StringDeCorectat);
         if (strpos($StringDeCorectat, "  ") !== false){
             $StringDeCorectat=InlocuireCharactere($StringDeCorectat);
         };
         //Verificam daca stringul are un spatiu la INCEPUT daca exista il eliminam
         if (substr($StringDeCorectat, 0, 1)===' '){
             $StringDeCorectat=substr($StringDeCorectat, 1, strlen($StringDeCorectat));
             //echo $StringDeCorectat;	
         };
         //Verificam daca stringul are un spatiu la SFARSIT daca exista il eliminam
        if (substr($StringDeCorectat, -1)===' '){
             $StringDeCorectat=substr($StringDeCorectat, 0, -1);
         };
         return $StringDeCorectat;
    };
    // VERIFICAM DACA FISIRIELE EXISTA
        function verify_file($path){
            if(!file_exists($path)){
                file_put_contents($path, '');
            }
        }
    //
    function create_page($scope, $title, $root, $conectareDB){
        $navigation = array(
            'navigation' => $root.'included/html/body/navigation.php',
            'access-navigation' => $root.'included/html/body/access-navigation.php'
        );
        $code = array(
            'css' => $root.'included/function/css/'.$scope.'.css',
            'script' => $root.'included/function/script/'.$scope.'.js',
            'php' =>  $root.'included/function/php/'.$scope.'.php',
            'head' => $root.'included/html/head/'.$scope.'.php',
            'body' => $root.'included/html/body/'.$scope.'.php',
            'exe' => $root.'included/function/exe/'.$scope.'.php'
        );

        foreach ($code as $cheie => $valoare) {
            verify_file($valoare);
        }

        $name_folder = dirname($_SERVER['PHP_SELF']);
        $check_root = explode('/',$name_folder);
        if(in_array('access', $check_root) || in_array('key', $check_root)){
            $nav = $navigation['access-navigation'];
        }
        else{
            $nav = $navigation['navigation'];
        }
        include_once $root.'included/html/composer.php';
    }
    //  VERIFICAM DACA DATELE USERULUI SUNT IN DB
        function check_user($username, $password, $name_base, $data_base){
            $sql = 'SELECT * FROM '.$name_base.' WHERE username = "'.$username.'"';
            $query =  mysqli_query($data_base, $sql);
            $exist_user = mysqli_num_rows($query);
            if($exist_user == 0){
                echo 'Acest nume nu exista in baza de date.';
                session_destroy();
                return false;
            }
            else{
                $user_row =  mysqli_fetch_assoc($query);
                if(!password_verify($password,$user_row['password'])){
                    echo 'Parola nu se potriveste.';
                    session_destroy();
                    return false;
                }
            }
            return true;
        }
    // 
    // VERIFICAM DACA SESIUNEA ESTE CORECTA
        function check_session($type_session, $root, $conectareDB){
            if($type_session === 'important'){
                if(!isset($_SESSION['username']) && !isset($_SESSION['password'])){
                    header('Location:'.$root.'');
                    exit;
                }
                else{
                    check_user($_SESSION['username'], $_SESSION['password'], 'users', $conectareDB);
                }
            }
            else if($type_session === 'unimportant'){
                if(isset($_SESSION['username']) && isset($_SESSION['password'])){
                    header('Location:'.$root.'main/');
                    exit;
                }
            }
        }
    //
    // VERIFICAM ROBOTII
        function check_robot($secretKey){
            if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']); 
                $responseData = json_decode($verifyResponse);         
                if($responseData->success){ 
                    return true;
                }
                else{
                    echo 'Completarea campului NU SUNT ROBOT - este necesara!';
                    return false;
                }
            }
            else{
                echo 'Completarea campului NU SUNT ROBOT - este necesara!';
                return false;
            }
        }
    //
    // INSERT DATA IN TABLE
        function insert_data($data_base, $name_table, $columns, $value){
            $sql = "INSERT INTO ".$name_table." (".$columns.")
            VALUES (".$value.")";
            mysqli_query($data_base, $sql);
        }    
    //
    // SORTARE AFLABETIAC ARRAY CU OBIECTE
    function comparare_alfabetica($a, $b) {
        $a_lower = mb_strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $a->nume));
        $b_lower = mb_strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $b->nume));
        return strcasecmp($a_lower, $b_lower);
    }
    //
    // FUNCTII DE VERIFICARE A CAMPURILOR
        function check_input_simboluri($string){
            if(preg_match('/[\'"^£$%&*()}{#~!?><>,|=+¬]/', $string)){
                return true;
            }
            return false;
        }
        function check_input_length($string, $min, $max){
            $string = str_replace(' ', '', $string);
            if(strlen($string) >= $max || strlen($string) <= $min){
                return true;
            }
            return false;
        }
        function check_cnp($cnp){
            if(strlen($cnp) !== 13) {
                return false;
            }
            if(!ctype_digit($cnp)) {
                return false;
            }
            $cifraControl = intval(substr($cnp, -1));
            $coeficienti = array(2, 7, 9, 1, 4, 6, 3, 5, 8, 2, 7, 9);
            $suma = 0;
            for($i = 0; $i < 12; $i++) {
                $suma += $cnp[$i] * $coeficienti[$i];
            }
            $rest = $suma % 11;
            if(($rest === 10 && $cifraControl === 1) || $rest === $cifraControl) {
                return true; 
            } else {
                return false; 
            }
        }
        function check_dubluri_act($tipAct, $numar, $conectareDB){
            if(is_table('angajati', $conectareDB)){
                $name_table = $_SESSION['username'].'_'.'angajati';
                $sql = 'SELECT * FROM '.$name_table.' WHERE '.$tipAct.' = "'.$numar.'"';
                $execute = mysqli_query($conectareDB, $sql);
                $verify_user = mysqli_num_rows($execute);
                if($verify_user > 0){
                    return true;
                }
                return false;
            }
        }
    //
?>    