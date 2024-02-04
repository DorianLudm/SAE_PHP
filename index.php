<?php
declare(strict_types=1);
session_start();

require_once 'Data/DataBase.php';
$data = new Data\DataBase();

// Autoload
require 'Classes/Autoloader.php';
Autoloader::register();
?>

<!doctype html>
<html>
<head>
    <title>PHP'O SONG</title>
    <link rel="stylesheet" href="./static/css/index.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="./static/js/index.js" defer></script>
    <script src="./static/js/accueil.js" defer></script>
</head>
<?php 
    // if ($_SESSION['user'] == null) {
    //     header('Location: login.php');
    // }
?>
<body>
    <?php include 'header.php'; ?>
    <main>
        <div id="titre">
            <?php
                if (!isset($_SESSION['user'])) {
                    echo '<h1> Bienvenue </h1>';
                } else {
                    echo '<h1> Bienvenue, '.$_SESSION['user']['prenom_utilisateur'].'</h1>';
                }
            ?>
        </div>
        <div id="playlist" class="sections_accueil">
            <h2>Playlists</h2>
            <?php 
            $playlists = $data->getPlaylistsTrieesParNote();
            foreach ($playlists as $playlist) {
                echo '<a href= "playlist.php?id='.$playlist['id_playlist'].'">';
                $image = $data->getMusiquesAlbumsByPlaylist($playlist['id_playlist'])['image_album'] ?? 'default.jpg';
                echo '<img src="./ressources/images/'.$image.'">';
                echo '<h3>'.$playlist['nom_playlist'].'</h3>';
                echo '<p class="infos_supp">'.$playlist['description_playlist'].'</p>';
                echo '</a>';
            }
            ?>
        </div>
        <div id="albums" class="sections_accueil">
            <h2>Albums</h2>
            <?php 
            $albums = $data->getAlbums();
            foreach ($albums as $album) {
                echo '<a href= "">';
                echo '<img src="./ressources/images/'.$album['image_album'].'">';
                echo '<h3>'.$album['titre'].'</h3>';
                echo '<p class="infos_supp">'.$data->getNomGroupe($album['id_groupe'])['nom_groupe'].'</p>';
                echo '</a>';

            }
            ?>
        </div>
        <div id="genres" class="sections_accueil">
            <h2>Genres</h2>
            <?php 
            $genres = $data->getGenres();
            foreach ($genres as $genre) {
                echo '<a href= "">';
                echo '<h3>'.$genre['nom_genre'].'</h3>';
                echo '</a>';
            }
            ?>
        </div>
        <div id="groupes" class="sections_accueil">
            <h2>Groupes</h2>
            <?php 
            $groupes = $data->getGroupes();
            foreach ($groupes as $groupe) {
                echo '<a href= "">';
                echo '<h3>'.$groupe['nom_groupe'].'</h3>';
                echo '<p class="infos_supp">'.$groupe['description_groupe'].'</p>';
                echo '</a>';
            }
            ?>
        </div>
        <!-- <div id="player">
            <h2>Titre de la chanson</h2>
            <audio controls="controls">
                <source src="musics/ARK_MainTheme.mp3">
            </audio>
            <div id="video">
                <h2>Titre de la vidéo</h2>
                <iframe  width="560" height="315" src="https://drive.google.com/uc?export=download&id=1a4URD6ChccVe9BPDy8xnOUXtsC4kZGea" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <iframe style="border-radius:12px" src="https://open.spotify.com/embed/track/1f2V8U1BiWaC9aJWmpOARe?utm_source=generator" width="100%" height="352" frameBorder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
        </div> -->
        <div id="circle"></div> <!-- Cercle qui suit le pointeur de la souris -->
    </main>
</body>
</html>