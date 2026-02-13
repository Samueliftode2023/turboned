<?php
    include_once $code['php'];
    echo '<!DOCTYPE html><html>';
        echo '<head>';
            include_once $root.'included/html/head/common.php';
            include_once $code['head'];
            echo '<style>';
                include_once $root.'included/function/css/common.css';
                include_once $code['css'];
            echo '</style>';
            echo '<script>';
                include_once $root.'included/function/script/common.js';
                include_once $code['script'];
            echo '</script>';
        echo '</head>';
        echo '<body>';
            include_once $nav;
            include_once $code['body'];
        echo '</body>';
    echo '</html>';
?>