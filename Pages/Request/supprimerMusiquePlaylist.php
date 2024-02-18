<?php
    session_start();
    if ($_SESSION['user'] == null) {
        $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
        header('Location: /Pages/Views/login.php');
    }
    $id_playlist = $_GET['id_playlist'] ?? 1;
    $id_musique = $_GET['id_musique'] ?? 1;
    require_once dirname(__FILE__) . '/../../Classes/Data/DataBase.php';
    $data = new Data\DataBase();
    echo $id_playlist;
    echo $id_musique;
    $userStatement = $data->deleteMusiquePlaylist($id_musique, $id_playlist);
    header('Location: /Pages/Views/playlist.php?id='.$id_playlist);
?>