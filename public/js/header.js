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
    get_game('');
});


// al click del bottone chiudi, nasconde il search dropdown e fa riapparire l'header e il menu hamburger
$('.close-search').on('click', function () {
    $('.dropdown-search-container').removeClass('show');
    $("header").css('visibility', 'visible');
    $(".animated-togglebutton").css('transition', '0.5s');
    $(".animated-togglebutton span").css('transition', '0.25s');
    $('.video-container').css('margin-top', '80');
});

// all input nella barra di ricerca, chiama la funzione get_game() che ottiene i giochi dal database
$('.search-input').on('input', function () {
    get_game($(this).val());
});


function get_game(input_data) {
    $.ajax({
        type: 'POST',
        url: '/api/get_games',
        dataType: "json",
        success: function (data) {
            var html = data
                .filter(function (value) {
                    return input_data === '' || value.title.toLowerCase().startsWith(input_data.toLowerCase());
                })
                .map(function (value) {
                    return `
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
                            <div class="dropdown-separator-search"></div>
                        `;
                }).join("");
            $(".search-game-result").html(html);
        },
        error: function (xhr, status, error) {
            console.log("Errore: " + xhr.responseText);
        }
    });
}

