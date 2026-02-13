<?php
    $root = '../../'; 
    $title = 'Inregistrare';
    $type_session = 'unimportant';
    include_once $root.'included/function/php/common.php';
    check_session($type_session, $root, $conectareDB);
    $scope = basename(__DIR__);
    echo create_page($scope, $title, $root, $conectareDB);
?>