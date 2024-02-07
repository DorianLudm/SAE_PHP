import { loadFichier } from "./audioVisualizer.js";
import { playVisualize } from "./audioVisualizer.js";

var sound;
var playlist = [
    "https://audio.jukehost.co.uk/2BNwH5heGwPsQ3lOHhMfgBA9Pm5mAxow",
    "https://audio.jukehost.co.uk/RJZlOinQcXyxi48c9eKKmiZavmIdQhqi",
];

var searchBar = document.getElementById('search');
var progressBar = document.getElementById('progressBar');
var progress = document.getElementById('progress');

var header = document.getElementById('trueHeader');

var player = document.getElementById('customPlayer');
var playButton = document.getElementById('playButton');
var previousButton = document.getElementById('prevButton');
var nextButton = document.getElementById('nextButton');
var volumeButton = document.getElementById('volumeButton');
var repeatButton = document.getElementById('repeatButton');
var aleatoireButton = document.getElementById('shuffleButton');
var progressVolume = document.getElementById('progressVolume');
var sliderVolume = document.getElementById('sliderVolume');
var arrowUp = document.getElementById('arrowUp');

var currentTime = document.getElementById('currentTime');
var circle_progress = document.getElementById('circle_progress');
var buttonIconPlay = document.querySelector('#playButton i.material-icons');
var volumeButtonI = document.querySelector('#volumeButton i.material-icons');
var repeatButtonI = document.querySelector('#repeatButton i.material-icons');
var aleatoireButtonI = document.querySelector('#shuffleButton i.material-icons');
var arrowUpI = document.querySelector('#arrowUp i.material-icons');
let timeoutId;
var in_play = false;
var isMute = false;
var currentVolume;

var in_play = false;
var repeat = 0;

function playPlaylist() {
    var currentTrackIndex = 0;

    // Fonction récursive pour jouer la playlist
    function playNextTrack() {
        if (currentTrackIndex < playlist.length) {
            sound = new Howl({
                src: [playlist[currentTrackIndex]],
                format: ['mp3']
            });
            // Définir l'événement onend seulement si sound est défini
            sound.on('end', function () {
                currentTrackIndex++;
                playNextTrack(); // Appeler la fonction pour jouer la piste suivante
            });
            play();
            sound.on('play', function () {
                setInterval(updateProgressBar, 100);
            });
        }
    }

    // Commencer la lecture de la playlist
    playNextTrack();
}


// METTRE EN PAUSE AVEC SPACE BAR
var isUserTyping = false;

if (searchBar !== null) {
    searchBar.addEventListener('input', function () {
        isUserTyping = true;
    });

    searchBar.addEventListener('blur', function () {
        isUserTyping = false;
    });

    searchBar.addEventListener('mouseenter', function () {
        isUserTyping = true;
    });

    searchBar.addEventListener('mouseleave', function () {
        isUserTyping = false;
    });
}

document.addEventListener('keydown', function (event) {
    if (event.code === 'Space' && !isUserTyping) {
        event.preventDefault();
        play();
    }
});

// Fonction de mise à jour de la barre de progression
function updateProgressBar() {
    var percentage = (sound.seek() / sound.duration()) * 100;
    progress.style.width = percentage + '%';

    // Convertir le temps de lecture en format HH:MM:SS
    var currentTime = formatTime(sound.seek());
    var totalTime = formatTime(sound.duration());

    currentTimeDisplay.textContent = currentTime + ' / ' + totalTime;

    if (currentTime == totalTime) {
        if (!sound.loop()) {
            in_play = false;
            buttonIconPlay.textContent = 'play_arrow';
        }
    }
}

progressBar.addEventListener('mouseenter', function () {
    circle_progress.style.transition = 'opacity 0.3s ease';
    circle_progress.style.opacity = 1;
});

progressBar.addEventListener('mouseleave', function () {
    timeoutId = setTimeout(function () {
        circle_progress.style.transition = 'opacity 1s ease';
        circle_progress.style.opacity = 0;
    }, 1000);
});

progressBar.addEventListener('mouseenter', function () {
    clearTimeout(timeoutId);
});

// Fonction pour formater le temps en format HH:MM:SS
function formatTime(timeInSeconds) {
    var hours = Math.floor(timeInSeconds / 3600);
    var minutes = Math.floor((timeInSeconds % 3600) / 60);
    var seconds = Math.floor(timeInSeconds % 60);

    return pad(minutes) + ':' + pad(seconds);
}

// Fonction pour ajouter un zéro devant les nombres inférieurs à 10
function pad(number) {
    return (number < 10 ? '0' : '') + number;
}

function play() {
    if (!in_play) {
        sound.play();
        loadFichier(sound);
        playVisualize();
        in_play = true;
        buttonIconPlay.textContent = 'pause';
        buttonIconPlay.setAttribute('title', 'Pause');
    } else {
        sound.pause();
        in_play = false;
        buttonIconPlay.textContent = 'play_arrow';
        buttonIconPlay.setAttribute('title', 'Lire');
    }
}

// Événement pour lire la musique
playButton.addEventListener('click', function () {
    playPlaylist();
});

player.addEventListener('mouseleave', function () {
    progressVolume.style.opacity = 0;
});

repeatButton.addEventListener('click', function () {
    if (repeat == 0) {
        repeatButton.style.opacity = 1;
        repeatButtonI.textContent = 'repeat';
        repeatButton.setAttribute('title', 'Tout lire en boucle');
        repeat = 1;
    } else if (repeat == 1) {
        repeatButton.style.opacity = 1;
        repeatButtonI.textContent = 'repeat_one';
        repeatButton.setAttribute('title', 'Lire un titre en boucle');
        sound.loop(true);
        repeat = 2;
    } else {
        repeatButton.style.opacity = 0.5;
        repeatButtonI.textContent = 'repeat';
        repeatButton.setAttribute('title', 'Activer la répétition');
        sound.loop(false);
        repeat = 0;
    }
});

aleatoireButton.addEventListener('click', function () {
    aleatoireButtonI.classList.toggle('rotate'); // Ajoute ou supprime la classe 'rotate'
});

arrowUp.addEventListener('click', function () {
    if (arrowUpI.classList.contains('rotate_arrow')) {
        arrowUp.style.opacity = 0.5;
    } else {
        arrowUp.style.opacity = 1;
    }
    arrowUpI.classList.toggle('rotate_arrow');
});

// Événement pour déplacer la lecture au clic sur la barre de progression
progressBar.addEventListener('click', function (e) {
    var rect = this.getBoundingClientRect();
    var clickPositionInPixels = e.clientX - rect.left;
    var clickPositionInPercentage = clickPositionInPixels / rect.width;
    sound.seek(sound.duration() * clickPositionInPercentage);
    updateProgressBar(); // Mettre à jour la barre de progression après le déplacement
});




