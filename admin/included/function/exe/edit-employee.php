<?php
    $root = '../../../'; 
    $type_session = 'important';
    include_once $root.'included/function/php/common.php';
    include_once $root.'included/function/php/edit-employee.php';
    check_session($type_session, $root, $conectareDB);
    $array_format = ['png','jpeg','jpg'];
    if(isset($_POST['photo-access']) && isset($_POST['id'])){
        if(check_file('file', 2000000, $array_format)){
            upload_file($_FILES['file'], $root);
            unset($_POST['photo-access']);
            unset($_POST['file']);
            make_change($_POST, $conectareDB);
        }
    }
    else if(isset($_POST['delete']) && isset($_POST['id'])){
        deleteEmployee($_POST['id'], $conectareDB);
    }
    else if(isset($_POST['angajat'])){
        if(is_table('angajati', $conectareDB)){
            $id = $_POST['angajat'];
            get_data_employee($id, $conectareDB);
        }
    }
    else if(isset($_POST['id'])){
        if(check_change_and_make($_POST, $conectareDB)){
            make_change($_POST, $conectareDB);
        }
    }
    else if(isset($_POST['lista-angajati'])){
        get_employee($_POST['selecteaza'], $conectareDB);
    }
    else{
        header('Location:'.$root.'');
        exit;
    }
?>
