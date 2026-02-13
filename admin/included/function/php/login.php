<?php
// VERIFICAM FORMATUL DATELOR
    function check_data_access($username, $password){
        if(strlen($username) <= 4 || strlen($username) >= 41){
            $str_length = strlen($username);
            echo 'Numele introdus are: '.$str_length.' caractere. 
            Este necesar ca acesta sa aiba intre 5 - 40 de caractere';
            return false;
        }
        if(strpos($username, " ") !== false){
            echo 'Numele introdus continue spatii.';
            return false;
        }
        if(preg_match('/[\'"^£$%&*()}{#~!?><>,|=+¬]/', $username)){
            echo 'Numele nu trebuie sa contina simboluri.';
            return false;
        }
        if(strlen($password) <= 7 || strlen($password) >= 41){
            $str_length = strlen($password);
            echo 'Parola introdusa are: '.$str_length.' caractere. Este necesar ca acesta sa aiba intre 8 - 40 de caractere.';
            return false;
        }
        if(!preg_match('`[A-Z]`',$password) || !preg_match('`[a-z]`',$password) || !preg_match('`[0-9]`',$password)){
            echo 'Parola nu respecta formatul.';
            return false;
        }
        if(strpos($password, " ") !== false){
            echo 'Parola nu trebuie sa contina spatii!';
            return false;
        }
        return true;
    }
// 
// CREATE NEW SESSION
    function create_connect_session($username, $password, $root){
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        echo 'ok';
    }
//
// CREATE NEW ACCOUNT
    function connect_account($name_table, $username, $password, $conectareDB){
        $sql_search = 'SELECT * FROM '.$name_table.' WHERE username = "'.$username.'"';
        $execute_search = mysqli_query($conectareDB, $sql_search);
        $verify_user = mysqli_num_rows($execute_search);
        if($verify_user > 0){
            $row_user =  mysqli_fetch_assoc($execute_search);
            if(password_verify($password, $row_user['password'])){
                return true;
            }
            else{
                echo 'Aceasta parola nu se potriveste!';
                return false;
            }
        }
        else{
            echo 'Acest nume nu eixsta in baza de date!';
            return false;
        }
    }
//
// STABILIM COOKIE
    function set_cookie($username, $password, $check){
        if(isset($check) && !isset($_COOKIE['username']) && !isset($_COOKIE['password'])){
            setcookie('username', $username, time() + 30 * 24 * 60 * 60, '/');
            setcookie('password', $password, time() + 30 * 24 * 60 * 60, '/');
        }    
    }
//
// COOKIE CONNECT
    $rem_user = '';
    $rem_pass = '';
    $checked = '';

    if(isset($_COOKIE['username']) && isset($_COOKIE['password'])){
        $rem_user = $_COOKIE['username'];
        $rem_pass = $_COOKIE['password'];
        $checked = 'checked';
    }
//
?>