import { init } from "./spa.js";

var sound;
var playlist = [
];

var playlistDetails = ["TheFatRat - Unity", "TheFatRat - Monody", "TheFatRat - Fly Away"];

var titlePage = document.querySelector('title');

var searchBar = document.getElementById('search');
var progressBar = document.getElementById('progressBar');
var progress = document.getElementById('progress');
var header = document.getElementById('trueHeader');

var nbrMusiquesListeLecture = document.getElementById('nbrMusiquesListeLecture');
var player = document.getElementById('customPlayer');
var detailsSection = document.getElementById('detailsSection');
var playButton = document.getElementById('playButton');
var previousButton = document.getElementById('prevButton');
var nextButton = document.getElementById('nextButton');
var volumeButton = document.getElementById('volumeButton');
var repeatButton = document.getElementById('repeatButton');
var aleatoireButton = document.getElementById('shuffleButton');
var progressVolume = document.getElementById('progressVolume');
var sliderVolume = document.getElementById('volumeSlider');
var arrowUp = document.getElementById('arrowUp');
// var visualizerButton = document.getElementById("audioVisualizer");
// var plusDetails = document.getElementById('moreMusic');
var optionsMusic = document.getElementById('optionsMusic');
var addMusiquePlaylist = document.getElementById('playlistButton');
var dialogPlaylist = document.getElementById('dialogPlaylist');
var buttonAddMusicToPlaylist = document.getElementById('addToPlaylist');
var musiquesASuivre = document.getElementById('musiquesASuivre');


var currentTime = document.getElementById('currentTime');
var circle_progress = document.getElementById('circle_progress');
var buttonIconPlay = document.querySelector('#playButton i.material-icons');
var volumeButtonI = document.querySelector('#volumeButton i.material-icons');
var repeatButtonI = document.querySelector('#repeatButton i.material-icons');
var aleatoireButtonI = document.querySelector('#shuffleButton i.material-icons');
var arrowUpI = document.querySelector('#arrowUp i.material-icons');
var visualizerButtonI = document.querySelector('#visualizerButton i.material-icons');

var inLecture = null;

var title = document.getElementById('title');
var cover = document.getElementById('cover');
var artiste = document.getElementById('nomArtiste');
var album = document.getElementById('nomAlbum');

//BIG PLAYER

var bigCover = document.getElementById('bigCover');

//

let timeoutId;
var in_play = false;
var isMute = false;
var currentVolume;
var currentTrackIndex = 0;
var repeat = 0;
var pause = false;
var currentTime;

export function lireUneMusique(id_musique, nom, cover, nomGroupe, nomAlbum, url) {
    playlist = [];
    playlist.push(url);
    playlistDetails = [];
    playlistDetails.push([nom, cover, nomGroupe, nomAlbum, id_musique]);
    addToListeLecture(id_musique, nom, cover, nomGroupe, nomAlbum, url);
    playPlaylist();
};

export function addToPlaylist(id_musique, nom, cover, nomGroupe, nomAlbum, url) {
    playlist.push(url);
    playlistDetails.push([nom, cover, nomGroupe, nomAlbum, id_musique]);
    addToListeLecture(id_musique, nom, cover, nomGroupe, nomAlbum, url);
};

export function addToListeLecture(id_musique, nom, cover, nomGroupe, nomAlbum, url, index) {
    if(inLecture == id_musique) {
        musiquesASuivre.innerHTML += "<li  class='musicEnLecture' id='oneMusicListeLecture'>"+
                                    "<a href='/Pages/Request/jouerIndex.php?id="+id_musique+"&index="+index+"' id=changeTrack>"+
                                        "<div class='flexContainerListeLecture'>" +
                                            "<div id='coverBigPlayer'>" +
                                                "<img class='imgListeLecture' src='/static/img/"+cover+"' alt='cover'>" +
                                            "</div>" +
                                            "<div id='infoListeLecture'>" +
                                                "<h4 id='titleListe'>"+nom+"</h4>" +
                                                "<p id='artisteListe'>"+nomGroupe+" • "+nomAlbum+"</p>" +
                                            "</div>" +
                                            "<img src='/static/img/sound.gif' alt='wave' id='wave'>" +
                                        "</div>" +
                                    "</a></li>";
    } else {
        musiquesASuivre.innerHTML += "<li id='oneMusicListeLecture'>"+
                                    "<a href='/Pages/Request/jouerIndex.php?id="+id_musique+"&index="+index+"' id=changeTrack>"+
                                        "<div class='flexContainerListeLecture'>" +
                                            "<div id='coverBigPlayer'>" +
                                                "<img class='imgListeLecture' src='/static/img/"+cover+"' alt='cover'>" +
                                            "</div>" +
                                            "<div id='infoListeLecture'>" +
                                                "<h4 id='titleListe'>"+nom+"</h4>" +
                                                "<p id='artisteListe'>"+nomGroupe+" • "+nomAlbum+"</p>" +
                                            "</div>" +
                                        "</div>" +
                                    "</a></li>";
    }
    init();
}


function refreshListeLecture() {
    musiquesASuivre.innerHTML = "";
    for(let i = 0; i < playlistDetails.length; i++) {
        addToListeLecture(playlistDetails[i][4], playlistDetails[i][0], playlistDetails[i][1], playlistDetails[i][2], playlistDetails[i][3], playlist[i], i);
    }
}

export function clearPlaylist() {
    musiquesASuivre.innerHTML = "";
    playlist = [];
    playlistDetails = [];
};

export function setFirstTrack(index) {
    currentTrackIndex = index;
};


export function playPlaylist() {
    if(player.style.display === 'none' || player.style.display === '') {
        player.style.display = 'flex';
    }
    // Fonction récursive pour jouer la playlist
    function playNextTrack() {
        // Libérer les ressources de la piste audio précédente
        if (sound) {
            sound.unload()
        }
        if (currentTrackIndex < playlist.length) {
            sound = new Howl({
                src: [playlist[currentTrackIndex]],
                format: ['mp3']
            });
            if(isMute){
                sound.volume(0);
            } else {
                sound.volume(sliderVolume.value);
            }
            // Définir l'événement onend seulement si sound est défini
            sound.on('end', function () {
                if(repeat != 2) {
                    currentTrackIndex++;
                    playNextTrack(); // Appeler la fonction pour jouer la piste suivante
                }
            });
            titlePage.textContent = playlistDetails[currentTrackIndex][0] + " - " + playlistDetails[currentTrackIndex][2];
            title.textContent = playlistDetails[currentTrackIndex][0];
            let taillePlaylist = playlistDetails.length;
            if(taillePlaylist > 1) {
                nbrMusiquesListeLecture.textContent = taillePlaylist + " titres";
            } else {
                nbrMusiquesListeLecture.textContent = "";
            }
            // Sélectionnez l'élément en cours de lecture
            inLecture = playlistDetails[currentTrackIndex][4];
            cover.src = "/static/img/"+playlistDetails[currentTrackIndex][1];
            bigCover.src = "/static/img/"+playlistDetails[currentTrackIndex][1];
            artiste.textContent = playlistDetails[currentTrackIndex][2];
            artiste.setAttribute('href', 'artiste.php?id='+playlistDetails[currentTrackIndex][2]);
            album.textContent = playlistDetails[currentTrackIndex][3];
            album.setAttribute('href', 'album.php?id='+playlistDetails[currentTrackIndex][3]);
            refreshListeLecture();
            let elementEnLecture = document.querySelector('.musicEnLecture');
            if(elementEnLecture !== null) {
                // Obtenir la position de l'élément en cours de lecture par rapport à la fenêtre
                let rect = elementEnLecture.getBoundingClientRect();
                if(rect.bottom >= window.innerHeight / 2 || rect.top <= window.innerHeight / 2) {
                    elementEnLecture.scrollIntoView({ behavior: 'smooth', block: 'center'});
                }
            }
            play(true);
            sound.on('play', function () {
                setInterval(updateProgressBar, 100);
            });
        } else {
            if(repeat == 1) {
                currentTrackIndex = 0;
                playNextTrack();
            } else {
                currentTrackIndex = 0;
                play();
                in_play = false;
                pause = false;
                titlePage.textContent = "Sonorify";
            }
        }
    }
    

    // Commencer la lecture de la playlist
    playNextTrack();
}


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


// METTRE EN PAUSE AVEC SPACE BAR
document.addEventListener('keydown', function (event) {
    if (event.code === 'Space' && !isUserTyping) {
        event.preventDefault();
        if(in_play || pause) {
            play();
        } else {
            playPlaylist();
        }
    }
});

// Fonction de mise à jour de la barre de progression
function updateProgressBar() {
    var percentage = (sound.seek() / sound.duration()) * 100;
    progress.style.width = percentage + '%';

    circle_progress.style.left = (percentage-0.2) + '%';

    // Convertir le temps de lecture en format HH:MM:SS
    currentTime = formatTime(sound.seek());
    var totalTime = formatTime(sound.duration());

    currentTimeDisplay.textContent = currentTime + ' / ' + totalTime;
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

function play(suite_playlist = false) {
    if(!suite_playlist) {
        if (!in_play && !pause) {
            sound.play();
            in_play = true;
            buttonIconPlay.textContent = 'pause';
            buttonIconPlay.setAttribute('title', 'Pause');
        } else if(pause){
            pause = false;
            sound.play();
            in_play = true;
            buttonIconPlay.textContent = 'pause';
            buttonIconPlay.setAttribute('title', 'Pause');
        } else{
            pause = true;
            sound.pause();
            in_play = false;
            buttonIconPlay.textContent = 'play_arrow';
            buttonIconPlay.setAttribute('title', 'Lire');
        }
    } else {
        sound.play();
        in_play = true;
        buttonIconPlay.textContent = 'pause';
        buttonIconPlay.setAttribute('title', 'Pause');
    }
}

// Événement pour lire la musique
playButton.addEventListener('click', function () {
    if(in_play || pause) {
        play();
    } else {
        playPlaylist();
    }
});

player.addEventListener('mouseleave', function () {
    progressVolume.style.opacity = 0;
    progressVolume.style.pointerEvents = 'none';
});

repeatButton.addEventListener('click', function () {
    if (repeat == 0) {
        repeatButton.style.opacity = 1;
        repeatButtonI.textContent = 'repeat';
        repeatButton.setAttribute('title', 'Tout lire en boucle');
        repeat = 1;
    } else if (repeat == 1) {
        try{
            sound.loop(true);
            repeatButton.style.opacity = 1;
            repeatButtonI.textContent = 'repeat_one';
            repeatButton.setAttribute('title', 'Lire un titre en boucle');
            repeat = 2;
        } catch (e) {
            repeatButton.style.opacity = 0.5;
            repeatButtonI.textContent = 'repeat';
            repeatButton.setAttribute('title', 'Activer la répétition');
            repeat = 0;
        }
    } else {
        repeatButton.style.opacity = 0.5;
        repeatButtonI.textContent = 'repeat';
        repeatButton.setAttribute('title', 'Activer la répétition');
        sound.loop(false);
        repeat = 0;
    }
});

export function aleatoire() {
    // Sauvegardez la musique en cours de lecture et ses détails
    let currentTrack = playlist[currentTrackIndex];
    let currentTrackDetails = playlistDetails[currentTrackIndex];

    // Retirez la musique en cours de lecture de la playlist et des détails de la playlist
    playlist.splice(currentTrackIndex, 1);
    playlistDetails.splice(currentTrackIndex, 1);

    // Mélangez le reste de la playlist et des détails de la playlist
    for (let i = playlist.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [playlist[i], playlist[j]] = [playlist[j], playlist[i]];
        [playlistDetails[i], playlistDetails[j]] = [playlistDetails[j], playlistDetails[i]];
    }

    // Remettez la musique en cours de lecture à son index original dans la playlist et les détails de la playlist
    playlist.splice(currentTrackIndex, 0, currentTrack);
    playlistDetails.splice(currentTrackIndex, 0, currentTrackDetails);

    refreshListeLecture();
}

aleatoireButton.addEventListener('click', function () {
    aleatoireButtonI.classList.toggle('rotate'); // Ajoute ou supprime la classe 'rotate'
    aleatoire();
});

arrowUp.addEventListener('click', function () {
    if (arrowUpI.classList.contains('rotate_arrow')) {
        arrowUp.style.opacity = 0.5;
        arrowUp.setAttribute('title', 'Afficher les détails');
    } else {
        arrowUp.style.opacity = 1;
        arrowUp.setAttribute('title', 'Masquer les détails');
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


var volumeButtonI = document.querySelector('#volumeButton i.material-icons');
var volumeButton = document.getElementById('volumeButton');

function toggleSection() {
    if (detailsSection.style.display === 'none' || detailsSection.style.display === '') {
        showDetailsSection();
    } else {
        hideDetailsSection();
    }
}

// Fonction pour afficher la section
function showDetailsSection() {
    detailsSection.style.transition = 'transform 0.3s ease';
    detailsSection.style.display = 'flex'; // Afficher la section
    header.style.borderBottom = '1px solid rgba(61, 61, 61, 0.8)';
    setTimeout(function () {
        detailsSection.style.transform = 'translateY(-100%)'; // Faire monter la section
    }, 10); // Ajouter un petit délai pour assurer que la transition est appliquée correctement
    let elementEnLecture = document.querySelector('.musicEnLecture');
    if(elementEnLecture !== null) {
        // Obtenez la position de l'élément en cours de lecture par rapport à la fenêtre
        let rect = elementEnLecture.getBoundingClientRect();
        if(rect.bottom >= window.innerHeight / 2) {
            elementEnLecture.scrollIntoView({ behavior: 'smooth'});
        }
    }
}

// Fonction pour masquer la section
function hideDetailsSection() {
    detailsSection.style.transition = 'transform 0.3s ease';
    detailsSection.style.transform = 'translateY(0)'; // Faire descendre la section
    setTimeout(function () {
        detailsSection.style.display = 'none'; // Masquer la section après la transition
    }, 300); // Attendre la fin de la transition avant de masquer la section
}

function changeVolume(volume){
    sound.volume(volume);
    if(volume == 0) {
        volumeButtonI.textContent = 'volume_mute';
    } else if (volume > 0 && volume < 0.5) {
        volumeButtonI.textContent = 'volume_down';
    } else {
        volumeButtonI.textContent = 'volume_up';
    }
}

volumeButton.addEventListener('mouseenter', function() {
    progressVolume.style.transition = 'opacity 0.4s ease';
    progressVolume.style.opacity = 1;
    progressVolume.style.pointerEvents = 'all';
});

volumeButton.addEventListener('click', function() {
    if(!isMute) {
        isMute = true;
        currentVolume = sound.volume();
        sound.volume(0);
        volumeButtonI.textContent = 'volume_off';
        volumeButton.setAttribute('title', 'Activer le son');
    } else{
        isMute = false;
        changeVolume(currentVolume);
    }
});

sliderVolume.addEventListener('input', function () {
    changeVolume(sliderVolume.value);
});

sliderVolume.addEventListener('change', function () {
    changeVolume(sliderVolume.value);
});

arrowUp.addEventListener('click', function () {
    toggleSection();
});

previousButton.addEventListener('click', function () {
    if(currentTime <= "00:05"){
        pause = false;
        in_play = false;
        if(currentTrackIndex == 0) {
            currentTrackIndex = playlist.length - 1;
        } else {
            currentTrackIndex--;
        }
        playPlaylist();
    } else {
        sound.seek(0);
    }
});

// visualizerButton.addEventListener('click', function () {
//     loadFichier(sound);
//     playVisualize();
// });

nextButton.addEventListener('click', function () {
    pause = false;
    in_play = false;
    if(currentTrackIndex == playlist.length - 1) {
        currentTrackIndex = 0;
    } else {
        currentTrackIndex++;
    }
    playPlaylist();
});

// moreMusic.addEventListener('click', function (event) {
//     optionsMusic.style.display = 'flex';
//     event.stopPropagation(); // Prevent this click from triggering the document click event below
// });

// document.addEventListener('click', function () {
//     optionsMusic.style.display = 'none';
// });

// addMusiquePlaylist.addEventListener('click', function () {
//     dialogPlaylist.style.display = 'block';
// });

// Créez une nouvelle balise de style
var style = document.createElement('style');

// Ajoutez les règles CSS à la balise de style
style.innerHTML = `
#progressBar {
    background-size: 200% 200%;
    background-image: linear-gradient(45deg, red, orange, yellow, green, blue, indigo, violet);
    animation: gradient 5s ease infinite;
}

#title, .infos_supplementaires{
    background: linear-gradient(90deg, red, orange, yellow, green, blue, indigo, violet);
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradient 5s linear infinite;
}

#progress {
    height: 100%;
    width: 0;
    transition: width 0.2s ease;
    background: linear-gradient(90deg, red, orange, yellow, green, blue, indigo, violet) repeat;
    background-size: 50% 100%;
    animation: gradient 5s linear infinite;
}

@keyframes gradient {
    0% {
        background-position: 100% 0%;
    }
    50% {
        background-position: 0% 100%;
    }
    100% {
        background-position: 100% 0%;
    }
}`;



let typedWord = '';
const targetWord = 'awesome';
let animation = false;

window.addEventListener('keydown', function(event) {
    // Ignore non-alphabetic keys
    if (event.key.length !== 1 || !event.key.match(/[a-z]/i)) {
        return;
    }

    typedWord += event.key;

    // Si le mot tapé est plus long que le mot cible, enlevez le premier caractère
    if (typedWord.length > targetWord.length) {
        typedWord = typedWord.substring(1);
    }

    // Vérifiez si le mot tapé correspond au mot cible
    if (typedWord === targetWord) {
        if (animation) {
            document.head.removeChild(style);
            animation = false;
        } else {
            document.head.appendChild(style);
            animation = true;
        }
    }
});

// var idPlaylist;

// $('#addToPlaylist').click(function() {
//     idPlaylist = $(this).data('id-playlist');
//     // Now you can use idPlaylist in your AJAX request
// });

// buttonAddMusicToPlaylist.addEventListener('click', function () {
//     dialogPlaylist.style.display = 'none';
//     $.ajax({
//         url: '../../Classes/Data/DataBase.php', // the location of your PHP file
//         type: 'post', // the HTTP method you want to use
//         data: {
//             'function_name': 'insertMusiquePlaylist', // the name of the PHP function you want to call
//             'id_playlist': idPlaylist,
//             'id_musique': playlistDetails[currentTrackIndex][4], // any data you want to pass to the PHP function
//         },
//         success: function(response) {
//             // this function will be called when the AJAX request is successful
//             // 'response' will contain whatever the PHP function outputs
//             console.log(response);
//         },
//         error: function(jqXHR, textStatus, errorThrown) {
//             // this function will be called if the AJAX request fails
//             console.log(textStatus, errorThrown);
//         }
//     });
// });


