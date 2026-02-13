<?php
    $root = '../../../'; 
    $type_session = 'important';
    include_once $root.'included/function/php/common.php';
    include_once $root.'included/function/php/import-agremeent-revisal.php';
    check_session($type_session, $root, $conectareDB);
    if(isset($_FILES['file'])){
        $type_file = ['db']; 
        if(check_file('file', 500000000, $type_file)){
            execute_import_revisal($_FILES['file']['tmp_name'], $conectareDB);
        }
    }
    else{
        header('Location:'.$root.'');
        exit;
    }
?>
