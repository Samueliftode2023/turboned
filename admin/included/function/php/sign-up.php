<?php
// VERIFICAM FORMATUL DATELOR
    function check_company_name($company){
        if(strlen($company) <= 4 || strlen($company) >= 61){
            $str_length = strlen($company);
            echo 'Numele introdus are: '.$str_length.' caractere. 
            Este necesar ca acesta sa aiba intre 5 - 60 de caractere';
            return false;
        }
        if(preg_match('/[\'"^£$%&*()}{#~!?><>,|=+¬]/', $company)){
            echo 'Numele nu trebuie sa contina simboluri.';
            return false;
        }
        return true;
    }
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
    function create_connect_session($username, $password){
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        echo 'ok';
    }
//
// CREATE NEW ACCOUNT
    function create_new_account($name_table, $username, $password, $company, $password_check, $conectareDB){
        $sql_search = 'SELECT * FROM '.$name_table.' WHERE username = "'.$username.'"';
        $execute_search = mysqli_query($conectareDB, $sql_search);
        $verify_user = mysqli_num_rows($execute_search);
        if($verify_user !== 0){
            echo 'Acest nume exista in baza de date, te ruga sa alegi alt nume.';
            return false;
        }
        else{
            if($password !== $password_check){
                echo 'Parola nu a fost confirmata!';
                return false;
            }
            else{
                $columns = "company_name, username, password, state, security, share_with, data_creation";
                $val_password = password_hash(mysqli_real_escape_string($conectareDB, $password), PASSWORD_DEFAULT);
                $val_username = mysqli_real_escape_string($conectareDB, $username);  
                $val_company = mysqli_real_escape_string($conectareDB, strtoupper($company)); 
                $val_state = 'admin-suprem';
                $val_security = 0;
                $val_share_with = 0;
                $date_creation = date("y-m-d");

                $value = "'".$val_company."','".$val_username."','".$val_password."','".
                $val_state."','".$val_security."','".$val_share_with."','".
                $date_creation."'";

                insert_data($conectareDB, $name_table, $columns, $value);
                return true;
            }
        }
    }
//
?>
