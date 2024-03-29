<?php 
    namespace Data;

    require_once __DIR__ . '/../Autoloader.php';
    \Autoloader::register();
    
    use Data\Provider;

    class DataBase{
        private $file_db;
        public function __construct(){
            $dbPath = __DIR__ . '/PHPOSONG.sqlite';
            $isNewDb = !file_exists($dbPath);
        
            $this->file_db = new \PDO('sqlite:'.$dbPath);
            $this->file_db->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_WARNING);
        
            if ($isNewDb) {
                $this->createTable();
                $this->executeSqlFile(__DIR__ . '/insert_php.sql');
                $Provider = new Provider('./ressources/extrait.yml');
                $data = $Provider->getData();
                $this->insertDataProvider($data);
            }
        }
        private function createTable(){
            $this->file_db->exec("CREATE TABLE IF NOT EXISTS GROUPE ( 
                id_groupe INTEGER PRIMARY KEY AUTOINCREMENT,
                nom_groupe TEXT,
                image_groupe TEXT,
                description_groupe TEXT)");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS ALBUM ( 
                id_album INTEGER PRIMARY KEY AUTOINCREMENT,
                titre TEXT,
                image_album TEXT,
                id_groupe INTEGER,
                dateSortie DATE,
                FOREIGN KEY (id_groupe) REFERENCES GROUPE(id_groupe))");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS ARTISTE ( 
                id_artiste INTEGER PRIMARY KEY AUTOINCREMENT,
                pseudo_artiste TEXT,
                image_artiste TEXT)");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS GROUPE_ARTISTE (
                id_groupe INTEGER,
                id_artiste INTEGER,
                PRIMARY KEY (id_groupe, id_artiste),
                FOREIGN KEY (id_groupe) REFERENCES GROUPE(id_groupe),
                FOREIGN KEY (id_artiste) REFERENCES ARTISTE(id_artiste))");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS GENRE (
                id_genre INTEGER PRIMARY KEY AUTOINCREMENT,
                nom_genre TEXT UNIQUE,
                image_genre TEXT)");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS ROLE (
                id_role INTEGER PRIMARY KEY AUTOINCREMENT,
                nom_role TEXT UNIQUE)");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS UTILISATEUR ( 
                id_utilisateur INTEGER PRIMARY KEY AUTOINCREMENT,
                login_utilisateur TEXT,
                password_utilisateur TEXT,
                nom_utilisateur TEXT,
                prenom_utilisateur TEXT,
                ddn_utilisateur DATE,
                email_utilisateur TEXT,
                image_utilisateur TEXT,
                id_role INTEGER,
                FOREIGN KEY (id_role) REFERENCES ROLE(id_role))");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS ALBUM_NOTE (
                id_album INTEGER,
                id_utilisateur INTEGER,
                note INTEGER,
                PRIMARY KEY (id_album, id_utilisateur),
                FOREIGN KEY (id_album) REFERENCES ALBUM(id_album),
                FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur))");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS GENRE_SIMILAIRE (
                id_genre INTEGER,
                id_genre_similaire INTEGER,
                PRIMARY KEY (id_genre, id_genre_similaire),
                FOREIGN KEY (id_genre) REFERENCES GENRE(id_genre),
                FOREIGN KEY (id_genre_similaire) REFERENCES GENRE(id_genre))");
            
            $this->file_db->exec("CREATE TABLE IF NOT EXISTS PLAYLIST (
                id_playlist INTEGER PRIMARY KEY AUTOINCREMENT,
                nom_playlist TEXT,
                description_playlist TEXT,
                public BOOLEAN,
                id_auteur INTEGER,
                FOREIGN KEY (id_auteur) REFERENCES UTILISATEUR(id_utilisateur)
                )");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS MUSIQUE (
                id_musique INTEGER PRIMARY KEY AUTOINCREMENT,
                nom_musique TEXT,
                duree TEXT,
                id_groupe INTEGER,
                id_album INTEGER,
                id_genre INTEGER,
                url_musique TEXT,
                FOREIGN KEY (id_groupe) REFERENCES GROUPE(id_groupe),
                FOREIGN KEY (id_genre) REFERENCES GENRE(id_genre),
                FOREIGN KEY (id_album) REFERENCES ALBUM(id_album)
                )");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS PLAYLIST_MUSIQUE (
                id_playlist INTEGER,
                id_musique INTEGER,
                PRIMARY KEY (id_playlist, id_musique),
                FOREIGN KEY (id_playlist) REFERENCES PLAYLIST(id_playlist),
                FOREIGN KEY (id_musique) REFERENCES MUSIQUE(id_musique))");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS PLAYLIST_NOTE (
                id_playlist INTEGER,
                id_utilisateur INTEGER,
                note INTEGER,
                PRIMARY KEY (id_playlist, id_utilisateur),
                FOREIGN KEY (id_playlist) REFERENCES PLAYLIST(id_playlist),
                FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur))");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS PLAYLIST_FAVORIS (
                id_playlist INTEGER,
                id_utilisateur INTEGER,
                PRIMARY KEY (id_playlist, id_utilisateur),
                FOREIGN KEY (id_playlist) REFERENCES PLAYLIST(id_playlist),
                FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur))");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS ALBUM_FAVORIS (
                id_album INTEGER,
                id_utilisateur INTEGER,
                PRIMARY KEY (id_album, id_utilisateur),
                FOREIGN KEY (id_album) REFERENCES ALBUM(id_album),
                FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur))");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS MUSIQUE_FAVORIS (
                id_musique INTEGER,
                id_utilisateur INTEGER,
                PRIMARY KEY (id_musique, id_utilisateur),
                FOREIGN KEY (id_musique) REFERENCES MUSIQUE(id_musique),
                FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur))");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS MUSIQUE_NOTE (
                id_musique INTEGER,
                id_utilisateur INTEGER,
                note INTEGER,
                PRIMARY KEY (id_musique, id_utilisateur),
                FOREIGN KEY (id_musique) REFERENCES MUSIQUE(id_musique),
                FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur))");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS GROUPE_FAVORIS (
                id_groupe INTEGER,
                id_utilisateur INTEGER,
                PRIMARY KEY (id_groupe, id_utilisateur),
                FOREIGN KEY (id_groupe) REFERENCES GROUPE(id_groupe),
                FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur))");

            $this->file_db->exec("CREATE TABLE IF NOT EXISTS MUSIQUE_HISTORIQUE (
                id_musique INTEGER,
                id_utilisateur INTEGER,
                date_lecture DATETIME,
                PRIMARY KEY (id_musique, id_utilisateur, date_lecture),
                FOREIGN KEY (id_musique) REFERENCES MUSIQUE(id_musique),
                FOREIGN KEY (id_utilisateur) REFERENCES UTILISATEUR(id_utilisateur))");
        }
        private function executeSqlFile($filePath) {
            try {
                // Read SQL file
                $sql = file_get_contents($filePath);
        
                // Execute SQL
                $this->file_db->exec($sql) or die(print_r($this->file_db->errorInfo(), true));
        
                echo "SQL file executed successfully";
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
        }
        public function getAlbums(){
            $albums = $this->file_db->query('SELECT * from ALBUM natural join GROUPE');
            return $albums->fetchAll();
        }
        public function getAlbum($id){
            $album = $this->file_db->query('SELECT * from ALBUM where id_album='.$id);
            return $album->fetch();
        }
        public function getIdGroupe($nom){
            $groupe = $this->file_db->query('SELECT id_groupe from GROUPE where nom_groupe="'.$nom.'"');
            return $groupe->fetch();
        }
        public function getIdAlbum($nom){
            $album = $this->file_db->query('SELECT id_album from ALBUM where titre="'.$nom.'"');
            if ($album){
                return $album->fetch();
            } else {
                return null;
            }
        }
        public function getGenres(){
            $genres = $this->file_db->query('SELECT * from GENRE');
            return $genres->fetchAll();
        }
        public function getArtistes(){
            return $this->file_db->query('SELECT * from ARTISTE');
        }
        public function getAlbumsArtistes(){
            return $this->file_db->query('SELECT * from ALBUM_ARTISTE');
        }
        public function getAlbumsGenres(){
            return $this->file_db->query('SELECT * from ALBUM_GENRE');
        }
        public function getAlbumsArtistesGenres(){
            return $this->file_db->query('SELECT * from ALBUM_ARTISTE natural join ALBUM_GENRE');
        }
        public function getAlbumsById($id){
            return $this->file_db->query('SELECT * from ALBUM where id_album='.$id);
        }
        public function getArtistesById($id){
            return $this->file_db->query('SELECT * from ARTISTE where id_artiste='.$id);
        }
        public function getGenresById($id){
            $genres = $this->file_db->query('SELECT * from GENRE where id_genre='.$id);
            return $genres->fetch();
        }
        public function getAlbumsArtistesByIdAlbum($id){
            $groupe = $this->file_db->query('SELECT nom_groupe from GROUPE natural join ALBUM where id_album='.$id);
            return $groupe->fetch();
        }
        public function getAlbumsArtistesByIdArtiste($id){
            return $this->file_db->query('SELECT * from ALBUM_ARTISTE where id_artiste='.$id);
        } 
        public function getMusiquesByGroupe($id){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join ALBUM natural join GROUPE natural left join MUSIQUE_NOTE where id_groupe='.$id.' GROUP BY id_musique order by note desc');
            return $musiques->fetchAll();
        }
        public function getMusiquesAleatoireByGroupe($id){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join ALBUM natural join GROUPE natural left join MUSIQUE_NOTE where id_groupe='.$id.' ORDER BY note desc LIMIT 12');
            return $musiques->fetchAll();
        }
        public function getAlbumsByGroupe($id){
            $albums = $this->file_db->query('SELECT * from ALBUM natural join GROUPE where id_groupe='.$id);
            return $albums->fetchAll();
        }
        public function getAlbumsAleatoireByGroupe($id){
            $albums = $this->file_db->query('SELECT * from ALBUM natural join GROUPE natural left join ALBUM_NOTE where id_groupe='.$id.' ORDER BY note desc LIMIT 12');
            return $albums->fetchAll();
        }
        public function getArtistesByGroupe($id){
            $artistes = $this->file_db->query('SELECT * from ARTISTE natural join GROUPE_ARTISTE where id_groupe='.$id);
            return $artistes->fetchAll();
        }
        public function getPlaylists(){
            $playlist = $this->file_db->query('SELECT * from PLAYLIST');
            return $playlist->fetchAll();
        }
        public function getMusiqueRecente(){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join ALBUM natural join GROUPE natural left join MUSIQUE_NOTE GROUP BY id_musique order by AVG(note) desc LIMIT 15');
            return $musiques->fetchAll();
        }
        public function getMusiqueRecemmentEcoutee(){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE  natural join ALBUM natural join GROUPE natural join MUSIQUE_HISTORIQUE order by date_lecture desc LIMIT 15');
            return $musiques->fetchAll();
        }
        public function getGroupes(){
            $groupes = $this->file_db->query('SELECT * from GROUPE');
            return $groupes->fetchAll();
        }
        public function getGroupeById($id){
            $groupe = $this->file_db->query('SELECT * from GROUPE where id_groupe='.$id);
            return $groupe->fetch();
        }
        public function getUser($login,$mdp){
            return $this->file_db->query('SELECT * from UTILISATEUR where login_utilisateur="'.$login.'" and password_utilisateur="'.$mdp.'"');
        }

        public function getAlbumsByIdGroupe($id){
            $albums = $this->file_db->query('SELECT * from ALBUM natural join GROUPE  where id_groupe='.$id.' LIMIT 2');
            if ($albums){
                return $albums->fetchAll();
            } else {
                return null;
            }
        }
        public function getGenresByName($nom){
            $genres = $this->file_db->query('SELECT * from GENRE where nom_genre LIKE "%'.$nom.'%"');
            return $genres->fetchAll();
        }
        public function getAlbumsByName($nom){
            $albums = $this->file_db->query('SELECT * from ALBUM natural join GROUPE where titre LIKE "%'.$nom.'%"');
            return $albums->fetchAll();
        }
        public function getPlaylistsByName($nom){
            $playlists = $this->file_db->query('SELECT * from PLAYLIST where nom_playlist LIKE "%'.$nom.'%"');
            
            return $playlists->fetchAll();
        }
        public function getPlaylist($id){
            $playlist = $this->file_db->query('SELECT * from PLAYLIST where id_playlist='.$id);
            return $playlist->fetch();
        }
        public function getMusiquesPlaylist($id){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join PLAYLIST_MUSIQUE where id_playlist='.$id);
            return $musiques->fetchAll();
        }
        public function getMusiquesPlaylistAleatoire($id){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join PLAYLIST_MUSIQUE natural left join MUSIQUE_NOTE where id_playlist='.$id.' ORDER BY note LIMIT 12');
            return $musiques->fetchAll();
        }
        public function getMusiques(){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE');
            return $musiques->fetchAll();
        }
        public function getGroupesByName($nom){
            $groupes = $this->file_db->query('SELECT * from GROUPE where nom_groupe LIKE "%'.$nom.'%"');
            return $groupes->fetchAll();
        }
        public function getGroupe($id){
            $groupe = $this->file_db->query('SELECT * from GROUPE where id_groupe='.$id);
            return $groupe->fetch();
        }
        public function getNomGroupe($id){
            $groupe = $this->file_db->query('SELECT nom_groupe from GROUPE where id_groupe='.$id);
            return $groupe->fetch();
        }
        public function getUtilisateur($id){
            $utilisateur = $this->file_db->query('SELECT * from UTILISATEUR where id_utilisateur='.$id);
            if ($utilisateur){
                return $utilisateur->fetch();
            } else {
                return null;
            }
        }
        public function getMusiquesNonAlbum(){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE where id_album is null');
            return $musiques->fetchAll();
        }
        public function getMusiquesAlbum($id){
            $musique = $this->file_db->query('SELECT * from MUSIQUE where id_album='.$id);
            return $musique->fetchAll();
        }
        public function getMusiqueByName($nom){
            $musique = $this->file_db->query('SELECT * from MUSIQUE where nom_musique = "'.$nom.'"');
            return $musique->fetch();
        }
        public function getMusiquesAlbumsByPlaylist($id){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join ALBUM natural join PLAYLIST_MUSIQUE where id_playlist='.$id);
            if ($musiques){
                return $musiques->fetch(); 
            } else {
                return null;
            }
        }
        public function getPlaylistsTrieesParNote(){
            $playlists = $this->file_db->query('SELECT * from PLAYLIST natural left join PLAYLIST_MUSIQUE natural left join MUSIQUE natural left join ALBUM natural left join PLAYLIST_NOTE where public=1 group by id_playlist order by avg(note) desc');
            return $playlists->fetchAll();
        }
        public function getAlbumsTrieesParNote(){
            $album = $this->file_db->query('SELECT * from ALBUM natural join GROUPE natural left join ALBUM_NOTE group by id_album order by avg(note) desc');
            return $album->fetchAll();
        }
        public function getPlaylistsByUser($id){
            $playlists = $this->file_db->query('SELECT *, AVG(note) moyenne_note from PLAYLIST  natural join PLAYLIST_MUSIQUE natural join MUSIQUE natural join ALBUM natural left join PLAYLIST_NOTE where id_auteur='.$id .' group by id_playlist');
            if ($playlists){
                return $playlists->fetchAll();
            } else {
                return null;
            }
        }
        public function getPlaylistsFavorisByUser($id){
            $playlists = $this->file_db->query('SELECT * from PLAYLIST natural join PLAYLIST_FAVORIS natural join PLAYLIST_MUSIQUE natural join MUSIQUE natural join ALBUM  where id_utilisateur="'.$id.'"group by id_playlist');
            return $playlists->fetchAll();
        }
        public function getMusiquesFavorisByUser($id){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join GROUPE natural join ALBUM natural join MUSIQUE_FAVORIS where id_utilisateur='.$id);
            return $musiques->fetchAll();
        }
        public function getGroupesFavorisByUser($id){
            $groupes = $this->file_db->query('SELECT * from GROUPE natural join GROUPE_FAVORIS where id_utilisateur='.$id);
            return $groupes->fetchAll();
        }
        public function getAlbumsFavorisByUser($id){
            $albums = $this->file_db->query('SELECT * from ALBUM natural join GROUPE natural join ALBUM_FAVORIS where id_utilisateur='.$id);
            return $albums->fetchAll();
        }
        public function getAlbumByMusique($id){
            $album = $this->file_db->query('SELECT * from ALBUM natural join MUSIQUE where id_musique='.$id);
            return $album->fetch();
        }
        public function getMusiquesByName($nom){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join ALBUM natural join GROUPE where nom_musique LIKE "%'.$nom.'%"');
            return $musiques->fetchAll();
        }
        public function getMusique($id){
            $musique = $this->file_db->query('SELECT * from MUSIQUE where id_musique='.$id);
            return $musique->fetch();
        }
        public function getMusiquesByGenre($id){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join ALBUM natural join GROUPE natural join GENRE where id_genre='.$id);
            return $musiques->fetchAll();
        }
        public function getGenresSimilaire($id){
            $genres = $this->file_db->query('SELECT * from GENRE_SIMILAIRE natural join GENRE where id_genre_similaire='.$id);
            return $genres->fetchAll();
        }
        public function getNoteMusique( $id_musique, $id_utilisateur){
            $note = $this->file_db->query('SELECT * from MUSIQUE_NOTE where id_musique='.$id_musique.' and id_utilisateur='.$id_utilisateur);
            return $note->fetch();
        }
        public function getMusiquesByIdGroupe($id){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join ALBUM natural join GROUPE natural left join MUSIQUE_NOTE where id_groupe='.$id.' GROUP BY id_musique order by note desc LIMIT 2');
            return $musiques->fetchAll();
        }
        public function getMusiquesByIdAlbum($id){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join ALBUM natural join GROUPE natural left join MUSIQUE_NOTE where id_album='.$id.' GROUP BY id_musique order by note desc LIMIT 2');
            return $musiques->fetchAll();
        }
        public function getMusiquesByIdPlaylist($id){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join PLAYLIST_MUSIQUE natural left join ALBUM natural join GROUPE natural join MUSiQUE_NOTE where id_playlist='.$id.' GROUP BY id_musique order by note desc LIMIT 2');
            return $musiques->fetchAll();
        }
        public function getMusiquesByIdGenre($id){
            $musiques = $this->file_db->query('SELECT * from MUSIQUE natural join ALBUM natural join GROUPE natural left join MUSIQUE_NOTE where id_genre='.$id.' GROUP BY id_musique order by note desc LIMIT 2');
            return $musiques->fetchAll();
        }
        public function getNoteAlbum( $id_album, $id_utilisateur){
            $note = $this->file_db->query('SELECT * from ALBUM_NOTE where id_album='.$id_album.' and id_utilisateur='.$id_utilisateur);
            return $note->fetch();
        }
        public function getNotePlaylist( $id_playlist, $id_utilisateur){
            $note = $this->file_db->query('SELECT * from PLAYLIST_NOTE where id_playlist='.$id_playlist.' and id_utilisateur='.$id_utilisateur);
            return $note->fetch();
        }
        public function getNoteMoyenneMusique($id_musique){
            $note = $this->file_db->query('SELECT AVG(note) moyenne_note from MUSIQUE_NOTE where id_musique='.$id_musique.' group by id_musique');
            return $note->fetch();
        }
        public function insertUser($pseudo, $password, $nom, $prenom, $email, $ddn){
            $insert="INSERT INTO UTILISATEUR (login_utilisateur, password_utilisateur, nom_utilisateur, prenom_utilisateur, email_utilisateur, ddn_utilisateur, id_role) VALUES (:pseudo, :pswd, :nom, :prenom, :email, :ddn, 1)";
            $stmt=$this->file_db->prepare($insert);
            $stmt->bindParam(':pseudo',$pseudo);
            $stmt->bindParam(':pswd',$password);
            $stmt->bindParam(':nom',$nom);
            $stmt->bindParam(':prenom',$prenom);
            $stmt->bindParam(':email',$email);
            $stmt->bindParam(':ddn',$ddn);
            $stmt->execute();
        }
        public function insertFavorisPlaylist($id_playlist,$id_utilisateur){
            $insert="INSERT INTO PLAYLIST_FAVORIS (id_playlist, id_utilisateur) VALUES (:id_playlist, :id_utilisateur)";
            $stmt=$this->file_db->prepare($insert);
            $stmt->bindParam(':id_playlist',$id_playlist);
            $stmt->bindParam(':id_utilisateur',$id_utilisateur);
            $stmt->execute();
        }
        public function insertFavorisAlbum($id_album,$id_utilisateur){
            $insert="INSERT INTO ALBUM_FAVORIS (id_album, id_utilisateur) VALUES (:id_album, :id_utilisateur)";
            $stmt=$this->file_db->prepare($insert);
            $stmt->bindParam(':id_album',$id_album);
            $stmt->bindParam(':id_utilisateur',$id_utilisateur);
            $stmt->execute();
        }
        public function insertFavoriteGroupe($id_groupe,$id_utilisateur){
            $insert="INSERT INTO GROUPE_FAVORIS (id_groupe, id_utilisateur) VALUES (:id_groupe, :id_utilisateur)";
            $stmt=$this->file_db->prepare($insert);
            $stmt->bindParam(':id_groupe',$id_groupe);
            $stmt->bindParam(':id_utilisateur',$id_utilisateur);
            $stmt->execute();
        }
        public function insertFavorisMusique($id_musique,$id_utilisateur){
            $insert="INSERT INTO MUSIQUE_FAVORIS (id_musique, id_utilisateur) VALUES (:id_musique, :id_utilisateur)";
            $stmt=$this->file_db->prepare($insert);
            $stmt->bindParam(':id_musique',$id_musique);
            $stmt->bindParam(':id_utilisateur',$id_utilisateur);
            $stmt->execute();
        }
        public function insertMusiquePlaylist($id_musique,$id_playlist){
            $insert="INSERT INTO PLAYLIST_MUSIQUE (id_playlist, id_musique) VALUES (:id_playlist, :id_musique)";
            $stmt=$this->file_db->prepare($insert);
            $stmt->bindParam(':id_playlist',$id_playlist);
            $stmt->bindParam(':id_musique',$id_musique);
            $stmt->execute();
        }
        public function insertNotePlaylist($id_playlist,$id_utilisateur,$note){
            if ($note > 5){
                $note = 5;
            }
            if ($note < 1){
                $note = 1;
            }
            if ($this->file_db->query('SELECT * from PLAYLIST_NOTE where id_playlist='.$id_playlist.' and id_utilisateur='.$id_utilisateur)->fetch()){
                $update="UPDATE PLAYLIST_NOTE SET note=:note where id_playlist=:id_playlist and id_utilisateur=:id_utilisateur";
                $stmt=$this->file_db->prepare($update);
                $stmt->bindParam(':id_playlist',$id_playlist);
                $stmt->bindParam(':id_utilisateur',$id_utilisateur);
                $stmt->bindParam(':note',$note);
                $stmt->execute();
            } else {
                $insert="INSERT INTO PLAYLIST_NOTE (id_playlist, id_utilisateur, note) VALUES (:id_playlist, :id_utilisateur, :note)";
                $stmt=$this->file_db->prepare($insert);
                $stmt->bindParam(':id_playlist',$id_playlist);
                $stmt->bindParam(':id_utilisateur',$id_utilisateur);
                $stmt->bindParam(':note',$note);
                $stmt->execute();
            }
        }
        public function insertNoteAlbum($id_album,$id_utilisateur,$note){
            if ($note > 5){
                $note = 5;
            }
            if ($note < 1){
                $note = 1;
            }
            if ($this->file_db->query('SELECT * from ALBUM_NOTE where id_album='.$id_album.' and id_utilisateur='.$id_utilisateur)->fetch()){
                $update="UPDATE ALBUM_NOTE SET note=:note where id_album=:id_album and id_utilisateur=:id_utilisateur";
                $stmt=$this->file_db->prepare($update);
                $stmt->bindParam(':id_album',$id_album);
                $stmt->bindParam(':id_utilisateur',$id_utilisateur);
                $stmt->bindParam(':note',$note);
                $stmt->execute();
            } else {
                $insert="INSERT INTO ALBUM_NOTE (id_album, id_utilisateur, note) VALUES (:id_album, :id_utilisateur, :note)";
                $stmt=$this->file_db->prepare($insert);
                $stmt->bindParam(':id_album',$id_album);
                $stmt->bindParam(':id_utilisateur',$id_utilisateur);
                $stmt->bindParam(':note',$note);
                $stmt->execute();
            }
        }
        public function insertEcoute($id_musique,$id_utilisateur){
            $total = $this->file_db->query('SELECT * from MUSIQUE_HISTORIQUE where id_utilisateur='.$id_utilisateur);
            if ($total ) {
                $total = $total->rowCount();
            } else {
                return;
            }
            $estPresent = $this->file_db->query('SELECT * from MUSIQUE_HISTORIQUE where id_musique='.$id_musique.' and id_utilisateur='.$id_utilisateur);
            if ($estPresent->fetch()){
                $update = "UPDATE MUSIQUE_HISTORIQUE SET date_lecture = DATETIME() where id_musique=:id_musique and id_utilisateur=:id_utilisateur";
                $stmt=$this->file_db->prepare($update);
                $stmt->bindParam(':id_musique',$id_musique);
                $stmt->bindParam(':id_utilisateur',$id_utilisateur);
                $stmt->execute();
            } else if ($total > 9){
                $update = "UPDATE MUSIQUE_HISTORIQUE SET date_lecture = DATETIME(), id_musique=:id_musique where id_utilisateur=:id_utilisateur and date_lecture = (SELECT date_lecture from MUSIQUE_HISTORIQUE where id_utilisateur=:id_utilisateur order by date_lecture asc LIMIT 1)";
                $stmt=$this->file_db->prepare($update);
                $stmt->bindParam(':id_utilisateur',$id_utilisateur);
                $stmt->bindParam(':id_musique',$id_musique);
                $stmt->execute();
            } else {
                $insert="INSERT INTO MUSIQUE_HISTORIQUE (id_musique, id_utilisateur, date_lecture) VALUES (:id_musique, :id_utilisateur, DATETIME())";
                $stmt=$this->file_db->prepare($insert);
                $stmt->bindParam(':id_musique',$id_musique);
                $stmt->bindParam(':id_utilisateur',$id_utilisateur);
                $stmt->execute();
            }
        }
        public function insertNoteMusique($id_musique,$id_utilisateur,$note){
            if ($note > 5){
                $note = 5;
            }
            if ($note < 1){
                $note = 1;
            }
            if ($this->file_db->query('SELECT * from MUSIQUE_NOTE where id_musique='.$id_musique.' and id_utilisateur='.$id_utilisateur)->fetch()){
                $update="UPDATE MUSIQUE_NOTE SET note=:note where id_musique=:id_musique and id_utilisateur=:id_utilisateur";
                $stmt=$this->file_db->prepare($update);
                $stmt->bindParam(':id_musique',$id_musique);
                $stmt->bindParam(':id_utilisateur',$id_utilisateur);
                $stmt->bindParam(':note',$note);
                $stmt->execute();
            } else {
                $insert="INSERT INTO MUSIQUE_NOTE (id_musique, id_utilisateur, note) VALUES (:id_musique, :id_utilisateur, :note)";
                $stmt=$this->file_db->prepare($insert);
                $stmt->bindParam(':id_musique',$id_musique);
                $stmt->bindParam(':id_utilisateur',$id_utilisateur);
                $stmt->bindParam(':note',$note);
                $stmt->execute();
            }
        }
        public function insertMusiqueAlbum($id_musique,$id_album){
            $insert="INSERT INTO MUSIQUE (id_musique, id_album) VALUES (:id_musique, :id_album)";
            $stmt=$this->file_db->prepare($insert);
            $stmt->bindParam(':id_musique',$id_musique);
            $stmt->bindParam(':id_album',$id_album);
            $stmt->execute();
        }
        public function updateAlbum($id, $titre, $date_sortie, $id_groupe, $image){
            $update="UPDATE ALBUM SET titre=:titre, dateSortie=:date_sortie, id_groupe=:id_groupe, image_album=:image where id_album=:id_album";
            $stmt=$this->file_db->prepare($update);
            $stmt->bindParam(':titre',$titre);
            $stmt->bindParam(':date_sortie',$date_sortie);
            $stmt->bindParam(':id_groupe',$id_groupe);
            $stmt->bindParam(':image',$image);
            $stmt->bindParam(':id_album',$id);
            $stmt->execute();
        }
        public function updateGroupe($id, $nom, $description, $image){
            $update="UPDATE GROUPE SET nom_groupe=:nom, description_groupe=:description, image_groupe=:image where id_groupe=:id_groupe";
            $stmt=$this->file_db->prepare($update);
            $stmt->bindParam(':nom',$nom);
            $stmt->bindParam(':description',$description);
            $stmt->bindParam(':image',$image);
            $stmt->bindParam(':id_groupe',$id);
            $stmt->execute();
        }
        public function deleteMusiquePlaylist($id_musique,$id_playlist){
            $delete="DELETE FROM PLAYLIST_MUSIQUE WHERE id_playlist=:id_playlist and id_musique=:id_musique";
            $stmt=$this->file_db->prepare($delete);
            $stmt->bindParam(':id_playlist',$id_playlist);
            $stmt->bindParam(':id_musique',$id_musique);
            $stmt->execute();
        }
        public function deleteFavorisPlaylist($id_playlist,$id_utilisateur){
            $delete="DELETE FROM PLAYLIST_FAVORIS WHERE id_playlist=:id_playlist and id_utilisateur=:id_utilisateur";
            $stmt=$this->file_db->prepare($delete);
            $stmt->bindParam(':id_playlist',$id_playlist);
            $stmt->bindParam(':id_utilisateur',$id_utilisateur);
            $stmt->execute();
        }
        public function deleteFavorisAlbum($id_album,$id_utilisateur){
            $delete="DELETE FROM ALBUM_FAVORIS WHERE id_album=:id_album and id_utilisateur=:id_utilisateur";
            $stmt=$this->file_db->prepare($delete);
            $stmt->bindParam(':id_album',$id_album);
            $stmt->bindParam(':id_utilisateur',$id_utilisateur);
            $stmt->execute();
        }
        public function isFavorisPlaylist($id_playlist,$id_utilisateur){
            $favoris = $this->file_db->query('SELECT * from PLAYLIST_FAVORIS where id_playlist='.$id_playlist.' and id_utilisateur='.$id_utilisateur);
            if ($favoris->fetch()){
                return true;
            } else {
                return false;
            }
        }
        public function deleteFavorisMusique($id_musique,$id_utilisateur){
            $delete="DELETE FROM MUSIQUE_FAVORIS WHERE id_musique=:id_musique and id_utilisateur=:id_utilisateur";
            $stmt=$this->file_db->prepare($delete);
            $stmt->bindParam(':id_musique',$id_musique);
            $stmt->bindParam(':id_utilisateur',$id_utilisateur);
            $stmt->execute();
        }
        public function deleteAlbum($id, $id_utilisateur){
            if ($this->file_db->query('SELECT * from UTILISATEUR where id_utilisateur='.$id_utilisateur . ' and id_role=2')->fetch()){
                $delete="DELETE FROM ALBUM where id_album=:id_album";
                $stmt=$this->file_db->prepare($delete);
                $stmt->bindParam(':id_album',$id);
                $stmt->execute();
            }
        }
        public function deleteGroupe($id, $id_utilisateur){
            if ($this->file_db->query('SELECT * from UTILISATEUR where id_utilisateur='.$id_utilisateur . ' and id_role=2')->fetch()){
                $delete="DELETE FROM GROUPE where id_groupe=:id_groupe";
                $stmt=$this->file_db->prepare($delete);
                $stmt->bindParam(':id_groupe',$id);
                $stmt->execute();
            }
        }
        public function isFavorisAlbum($id_album,$id_utilisateur){
            $favoris = $this->file_db->query('SELECT * from ALBUM_FAVORIS where id_album='.$id_album.' and id_utilisateur='.$id_utilisateur);
            if ($favoris->fetch()){
                return true;
            } else {
                return false;
            }
        }
        public function isFavorisMusique($id_musique,$id_utilisateur){
            $favoris = $this->file_db->query('SELECT * from MUSIQUE_FAVORIS where id_musique='.$id_musique.' and id_utilisateur='.$id_utilisateur);
            if ($favoris->fetch()){
                return true;
            } else {
                return false;
            }
        }
        public function isMusiqueNotee($id_musique,$id_utilisateur){
            $favoris = $this->file_db->query('SELECT * from MUSIQUE_NOTE where id_musique='.$id_musique.' and id_utilisateur='.$id_utilisateur);
            if ($favoris->fetch()){
                return true;
            } else {
                return false;
            }
        }
        public function isAlbumNotee($id_album, $id_utilisateur){
            $favoris = $this->file_db->query('SELECT * from ALBUM_NOTE where id_album='.$id_album.' and id_utilisateur='.$id_utilisateur);
            if ($favoris->fetch()){
                return true;
            } else {
                return false;
            }
        }
        public function isPlaylistNotee($id_playlist, $id_utilisateur){
            $favoris = $this->file_db->query('SELECT * from PLAYLIST_NOTE where id_playlist='.$id_playlist.' and id_utilisateur='.$id_utilisateur);
            if ($favoris->fetch()){
                return true;
            } else {
                return false;
            }
        }
        public function creerAlbum($titre, $date_sortie, $id_groupe, $image){
            $insert="INSERT INTO ALBUM (titre, dateSortie, id_groupe, image_album) VALUES (:titre, :date_sortie, :id_groupe, :image)";
            $stmt=$this->file_db->prepare($insert);
            $stmt->bindParam(':titre',$titre);
            $stmt->bindParam(':date_sortie',$date_sortie);
            $stmt->bindParam(':id_groupe',$id_groupe);
            $stmt->bindParam(':image',$image);
            $stmt->execute();
        }
        public function creerGroupe($nom, $description, $image){
            $insert="INSERT INTO GROUPE (nom_groupe, description_groupe, image_groupe) VALUES (:nom, :description, :image)";
            $stmt=$this->file_db->prepare($insert);
            $stmt->bindParam(':nom',$nom);
            $stmt->bindParam(':description',$description);
            $stmt->bindParam(':image',$image);
            $stmt->execute();
        }
        public function creerPlaylist($nom, $description, $public, $id_auteur){
            $insert="INSERT INTO PLAYLIST (nom_playlist, description_playlist, public, id_auteur) VALUES (:nom, :description, :public, :id_auteur)";
            $stmt=$this->file_db->prepare($insert);
            $stmt->bindParam(':nom',$nom);
            $stmt->bindParam(':description',$description);
            $stmt->bindParam(':public',$public);
            $stmt->bindParam(':id_auteur',$id_auteur);
            $stmt->execute();
        }
        public function creerMusique($nom, $duree, $id_groupe, $id_album, $id_genre, $url){
            $insert="INSERT INTO MUSIQUE (nom_musique, duree, id_groupe, id_album, id_genre, url_musique) VALUES (:nom, :duree, :id_groupe, :id_album, :id_genre, :url)";
            $stmt=$this->file_db->prepare($insert);
            $stmt->bindParam(':nom',$nom);
            $stmt->bindParam(':duree',$duree);
            $stmt->bindParam(':id_groupe',$id_groupe);
            $stmt->bindParam(':id_album',$id_album);
            $stmt->bindParam(':id_genre',$id_genre);
            $stmt->bindParam(':url',$url);
            $stmt->execute();
        }
        
        public function insertDataProvider($data){
            foreach ($data as $album) {
                $index_musique = 0;
        
                // Check if image exists, if not use default
                $imagePath = $album['img'] ?? "";
                if (!file_exists("./ressources/images/".$imagePath)) {
                    $imagePath = "default.jpg"; // replace with your default image name
                }
        
                // Insert into GROUPE table
                $stmt = $this->file_db->prepare('INSERT INTO GROUPE (nom_groupe, image_groupe, description_groupe) VALUES (?, ?, ?)');
                $stmt->execute([$album['by'], $imagePath, $album['description']??""]);
        
                // Get the id of the group we just inserted
                $id_groupe = $this->file_db->lastInsertId();
        
                // Insert into ARTISTE table
                $stmt = $this->file_db->prepare('INSERT INTO ARTISTE (pseudo_artiste, image_artiste) VALUES (?, ?)');
                $stmt->execute([$album['by'], $imagePath]);
        
                // Get the id of the artist we just inserted
                $id_artiste = $this->file_db->lastInsertId();
        
                // Insert into GROUPE_ARTISTE table
                $stmt = $this->file_db->prepare('INSERT INTO GROUPE_ARTISTE (id_groupe, id_artiste) VALUES (?, ?)');
                $stmt->execute([$id_groupe, $id_artiste]);
                
                // Insert into ALBUM table
                $this->creerAlbum($album['title'], $album['releaseYear'], $id_groupe, $imagePath);
        
                // Get the id of the album we just inserted
                $id_album = $this->file_db->lastInsertId();
        
                // Insert into GENRE table
                foreach ($album['genre'] as $genre) {
                    if($genre != null){
                        $genre[0] = strtoupper($genre[0]);
                        $index_musique += 1;
        
                        $stmt = $this->file_db->prepare('INSERT OR IGNORE INTO GENRE (nom_genre) VALUES (?)');
                        $stmt->execute([$genre]);
            
                        // Get the id of the genre we just inserted
                        $id_genre = $this->file_db->lastInsertId();
            
                        // Insert into MUSIQUE table
                        $stmt = $this->file_db->prepare('INSERT INTO MUSIQUE (nom_musique, id_groupe, id_album, id_genre, url_musique) VALUES (?, ?, ?, ?, ?)');
                        $stmt->execute(['Musique de '.$album['by']." ".$index_musique, $id_groupe, $id_album, $id_genre, ""]);
                    }
                }
            }
        }
    }

    $db = new DataBase();
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['function_name']) && $_POST['function_name'] == 'insertMusiquePlaylist') {
            $id_playlist = $_POST['id_playlist'];
            $id_musique = $_POST['id_musique'];
            $db->insertMusiquePlaylist($id_musique, $id_playlist);
        }
    }
?>