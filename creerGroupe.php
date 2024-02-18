<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user'])) {
        $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $nom_groupe = $_POST['nom_groupe'];
        $description = $_POST['description'];
        $image = $_POST['image'];
        $user = $_SESSION['user'];
        require_once 'Classes/Data/DataBase.php'; 
        $db = new Data\Database();
        $db->creerGroupe($nom_groupe, $description, $image);
        $id_groupe = $db->getGroupesByName($nom_groupe)[0]['id_groupe'];
        if ($id_groupe){
            header('Location: groupe.php?id='.$id_groupe);
        } else {
            echo "<strong>Le groupe n'a pas été créé</strong>";
        }
    } 
?>
<header>
    <link rel="stylesheet" href="./static/css/creations.css">
</header>
<form id="boxCreation" action="creerGroupe.php" method="post">
    <label for="nom_groupe">Nom de Groupe</label>
    <input type="text" id="nom_groupe" name="nom_groupe" required>
    <label for="description">Description</label>
    <input type="text" id="description" name="description">
    <label for="image">Image:</label><br>
    <input type="text" id="image" name="image"><br>
    <input type="submit" value="Créer">
</form>