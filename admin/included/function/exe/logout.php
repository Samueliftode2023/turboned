<?php
    session_start();
    if(isset($_POST['logout'])){
        session_destroy();
        echo 'ok';
    }
    else{
        header('Location:../');
        exit;
    }
?>