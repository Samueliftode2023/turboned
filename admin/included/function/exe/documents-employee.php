<?php
    $root = '../../../'; 
    $type_session = 'important';
    include_once $root.'included/function/php/common.php';
    include_once $root.'included/function/php/documents-employee.php';
    check_session($type_session, $root, $conectareDB);

    if(isset($_POST['create-folder'])){
        $array = ['angajat', 'cale-folder', 'nume-folder'];
        if(check_if_is_set($array)){
            if(is_table('documente', $conectareDB)){
                create_folder($conectareDB);
            }
            else if(create_table($conectareDB)){
                create_folder($conectareDB);
            }
        }
    }
    else if(isset($_POST['search-foldere'])){
        $array = ['angajat', 'cale-folder'];
        if(check_if_is_set($array)){
            if(is_table('documente', $conectareDB)){
                search_folder($conectareDB);
            }
        }
    }
    else if(isset($_POST['input-foldere'])){
        $array = ['angajat', 'cale-folder'];
        if(check_if_is_set($array)){
            if(is_table('documente', $conectareDB)){
                is_folder($conectareDB);
            }
            else{
                echo 'false';
            }
        }
    }
    else if(isset($_POST['rename-fisiere'])){
        $array = ['angajat', 'cale-folder', 'new-name','folder-name'];
        if(check_if_is_set($array)){
            if(is_table('documente', $conectareDB)){
                rewrite_files($conectareDB);
            }
            else{
                echo 'false';
            }
        }
    }
    else if(isset($_POST['move-copy-fisiere'])){
        $array = ['angajat', 'old-path', 'new-path','move-mode'];
        if(check_if_is_set($array)){
            paste_folder($conectareDB);
        }

    }
    else if(isset($_POST['delete-file'])){
        $array = ['angajat', 'path-delete'];
        if(check_if_is_set($array)){
            delete_folder($conectareDB);
        }
    }
    else if(isset($_POST['angajat']) && isset($_POST['cale-document']) && isset($_FILES['file'])){
        $id = mysqli_real_escape_string($conectareDB, InlocuireCharactere($_POST['angajat']));
        $path = mysqli_real_escape_string($conectareDB, InlocuireCharactere($_POST['cale-document']));
        if(!is_table('documente', $conectareDB)){
            create_table($conectareDB);
            check_path_file($id, $path, $_FILES['file'], $conectareDB);
        }
        else{
            check_path_file($id, $path, $_FILES['file'], $conectareDB);
        }
    }
    else if(isset($_POST['download-file'])){
        $array = ['angajat', 'fisier'];
        if(check_if_is_set($array)){
            download_file($conectareDB);
        }
    }
    else{
        header('Location:'.$root.'');
        exit;
    }
?>