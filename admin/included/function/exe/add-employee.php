<?php
    $root = '../../../'; 
    $type_session = 'important';
    include_once $root.'included/function/php/common.php';
    include_once $root.'included/function/php/add-employee.php';
    check_session($type_session, $root, $conectareDB);
    $array_format = ['png','jpeg','jpg'];
    if(isset($_POST['photo-access'])){
        if(check_file('file', 2000000, $array_format)){
            upload_file($_FILES['file'], $root);
        }
    }
    else if(isset($_POST['nume']) && isset($_POST['prenume'])){
        $_POST['nume'] = InlocuireCharactere($_POST['nume']).' '.InlocuireCharactere($_POST['prenume']);
        unset($_POST['prenume']);
        if(check_employee_data($_POST, $conectareDB)){
            if(is_table('angajati', $conectareDB)){
                insert_angajat($_POST, $conectareDB);
            }
            else{
                create_table($_POST, $conectareDB);
            }
        }
    }
    else{
        header('Location:'.$root.'');
        exit;
    }
?>
