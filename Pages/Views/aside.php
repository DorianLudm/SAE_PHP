<!doctype html>
<html>
<head>
    <link rel="stylesheet" href="./static/css/aside.css">
    <!-- <script src="./static/js/aside.js" defer></script> -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <aside>
        <header id="headerAside">
            <div class="hamburger-menu">
                <input id="menu__toggle" type="checkbox" />
                <label id="hamburger" class="menu__btn" for="menu__toggle">
                    <span></span>
                </label>
                <img id="logo" src="/static/img/grandLogo.png" alt="logo">
                <ul class="menu__box">
                    <li><a class="menu__item" href="/Pages/Views/accueil.php" title="Accueil" id="Accueil"><i class="material-icons">home</i><span class="menu__text">Accueil</span></a></li>
                    <li><a class="menu__item" href="/Pages/Views/profil.php" title="Profil" id ="Profil"><i class="material-icons">account_circle</i><span class="menu__text">Profil</span></a></li>
                    <!-- <li><a class="menu__item" href="index.php" title="Bibliothèque" id="bibliotheque"><i class="material-icons">library_books</i><span class="menu__text">Bibliothèque</span></a></li> -->
                </ul>
            </div>
        </header>
    </aside>
</body>
</html>