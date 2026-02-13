<?php
    $root = '../../../'; 
    $title = 'Edit employees';
    $type_session = 'important';
    include_once $root.'included/function/php/common.php';
    check_session($type_session, $root, $conectareDB);
    $scope = basename(__DIR__);
    echo create_page($scope, $title, $root, $conectareDB);
?>