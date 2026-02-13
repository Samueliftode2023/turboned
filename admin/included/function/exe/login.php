<?php
    $root = '../../../'; 
    $type_session = 'unimportant';
    include_once $root.'included/function/php/common.php';
    include_once $root.'included/function/php/login.php';
    check_session($type_session, $root, $conectareDB);
    if(isset($_POST['username']) && isset($_POST['password'])){
        $secretKey  = '6LevXBAgAAAAAEuIYCKl8V_ED2hw6cu5u1KEmuCu';
        if(check_robot($secretKey)){
            $username = $_POST['username'];
            $password = $_POST['password'];

            if(check_data_access($username, $password)){
                if(connect_account('users', $username, $password, $conectareDB, $root)){
                    if(!isset($_POST['reminde'])){
                        setcookie('username', $username, time() - 30 * 24 * 60 * 60, '/');
                        setcookie('password', $password, time() - 30 * 24 * 60 * 60, '/');
                    }
                    else{
                        set_cookie($username, $password, $_POST['reminde']);
                    }
                    create_connect_session($username, $password, $root);
                }
            }
        }
    }
    else{
        header('Location:'.$root.'');
        exit;
    }
?>