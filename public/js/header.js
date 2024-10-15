let gamesData = []; // Variabile per memorizzare i dati dei giochi
let debounceTimeout;

// applica stile css al menu hamburger e al menu dropdown quando si clicca sull'icona del menu
// setta la variabile isLoggedIn a true se l'utente è loggato, altrimenti a false
// aggiorna il testo e l'href del link al profilo se l'utente è loggato
$(() => {
    $('#hamburger').on('click', function () {
        $('.animated-togglebutton').toggleClass('open');
        $('.dropdown-nav-container').toggleClass('show');
        $('#search-btn').toggleClass('show');
    });
});

// al ridimensionamento della pagina, se la larghezza è maggiore di 991px, rimuove le classi open e show
// per far scomparire il menu hamburger e il menu dropdown
$(window).on('resize', function () {
    if ($(window).innerWidth() > 991) {
        $('.fa-search').removeClass('open');
        $('.dropdown-nav-container').removeClass('show');
        $('.animated-togglebutton').removeClass('open');
    }
});

// scrivi funzione che se la route è / allora toglie height: 100px; a .header-spacer
// altrimenti la mette
$(() => {
    if (window.location.pathname === '/') {
        $('.header-spacer').css('height', '0');
    } else {
        $('.header-spacer').css('height', '100px');
    }
});

// fa apparire il search dropdown quando si clicca sull'icona della ricerca e nasconde l'header e il menu hamburger 
$('#search-btn2, #search-btn').on('click', function () {
    $('.dropdown-search-container').toggleClass('show');
    $("header").css('visibility', 'hidden');
    $(".animated-togglebutton, .animated-togglebutton span").css('transition', '0s');
    $('.video-container').css('margin-top', '0');
    get_games();
});


// al click del bottone chiudi, nasconde il search dropdown e fa riapparire l'header e il menu hamburger
$('.close-search').on('click', function () {
    $('.dropdown-search-container').removeClass('show');
    $("header").css('visibility', 'visible');
    $(".animated-togglebutton").css('transition', '0.5s');
    $(".animated-togglebutton span").css('transition', '0.25s');
    $('.video-container').css('margin-top', '80');
});


// Funzione per ottenere i giochi
function get_games() {
    $.ajax({
        type: 'POST',
        url: '/api/games',
        dataType: "json",
        success: function (data) {
            gamesData = data; // Memorizza i dati dei giochi
            renderGames(gamesData); // Mostra tutti i giochi inizialmente
        },
        error: function (xhr, status, error) {
            console.log("Errore: " + xhr.responseText);
        }
    });
}

// Funzione per rendere i giochi
function renderGames(games) {
    const container = $(".search-game-result");
    container.empty(); // Pulisci il contenitore

    // Genera i nuovi risultati
    let html = '';
    games.forEach(function (value, index) {
        html += `
            <div class="dropdown-game-container">
                <a class="d-flex search-game-link" href="${value.link}">
                    <div class="search-game-img">
                        <img src="${value.image_path}" alt="${value.image_path}">
                    </div>
                    <div class="search-game-text">
                        <h4 class="search-game-title">${value.title}</h4>
                        <div class="search-game-description">${value.description}</div>
                    </div>
                </a>
            </div>
        `;

        // Aggiungi il separatore solo se non è l'ultimo elemento
        if (index < games.length - 1) {
            html += '<div class="dropdown-separator-search"></div>';
        }
    });

    container.html(html); // Aggiungi i nuovi risultati al contenitore
}

// Filtra i giochi in base all'input
$('.search-input').on('input', function (event) {
    clearTimeout(debounceTimeout); // Pulisci il timeout precedente

    // Imposta un nuovo timeout per il debouncing
    debounceTimeout = setTimeout(() => {
        const input_data = event.target.value; // Ottieni il valore dell'input
        const filteredGames = gamesData.filter(function (value) {
            return input_data === '' || value.title.toLowerCase().startsWith(input_data.toLowerCase());
        });
        renderGames(filteredGames); // Rendi i giochi filtrati
    }, 300); // Debounce di 300 ms
});