<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user'])) {
        $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
    }
    $id_musique = $_GET['id'] ?? 1;
    $note = $_GET['note'] ?? 1;
    $id_utilisateur = $_SESSION['user']['id_utilisateur'] ?? 1;
    require_once 'Classes/Data/DataBase.php'; 
    $data = new Data\DataBase();
    $userStatement = $data->insertNoteMusique($id_musique, $id_utilisateur, $note);
    header('Location: ' . $_SESSION['page']);
?>