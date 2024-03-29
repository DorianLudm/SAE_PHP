<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if ($_SESSION['user'] == null) {
        $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
        header('Location: /Pages/Views/login.php');
    } else {
        $id_album = $_GET['id'] ?? 1;
        require_once dirname(__FILE__) . '/../../Classes/Data/DataBase.php';
        $data = new Data\DataBase();
        $nom_musique = $_POST['nom_musique'] ?? 'AA';
        $musique = $data->getMusiqueByName($nom_musique);
        $id_musique = $musique['id_musique'];
        $userStatement = $data->insertMusiqueAlbum($id_musique, $id_album);
        header('Location: /Pages/Views/album.php?id='.$id_album);
    }
?>