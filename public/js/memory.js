const images = [
	'../assets/memory/1.png',
	'../assets/memory/2.png',
	'../assets/memory/3.png',
	'../assets/memory/4.png',
	'../assets/memory/5.png',
	'../assets/memory/6.png',
	'../assets/memory/7.png',
	'../assets/memory/8.png',
];

let firstCard = null; 
let secondCard = null; 
let cardsMatched = 0;
let generatedNumbers = []; 
let time = 0.0; 
let countdown; 
let gameStarted = false;

const gridItems = $('.grid-item');

// Funzione per generare un numero casuale
function generateRandomNumber() {
	let randomNumber = 0;

	if (generatedNumbers.length === 16) {
		console.log("Errore: non ci sono più numeri da generare");
		return -1;
	}

	do {
		randomNumber = Math.floor(Math.random() * 8); 
	} while (generatedNumbers.filter(n => n === randomNumber).length >= 2); 

	generatedNumbers.push(randomNumber); 

	return randomNumber;
}

// Funzione per mescolare le carte
function shuffle(array) {
	let currentIndex = array.length;
	let temporaryValue, randomIndex;

	while (currentIndex !== 0) {
		randomIndex = Math.floor(Math.random() * currentIndex);
		currentIndex--;
		temporaryValue = array[currentIndex];
		array[currentIndex] = array[randomIndex];
		array[randomIndex] = temporaryValue;
	}

	return array;
}

// Funzione per impostare l'immagine di una carta
function setImage(card, index) {
	const img = $('<img>').attr('src', images[index]);
	card.append(img); 
}

// Funzione per girare una carta
function flipCard(card) { 
	card.removeClass('hidden').addClass('visible');
}

// Funzione per nascondere una carta
function hideCard(card) {
	card.removeClass('visible').addClass('hidden');
}

// Funzione per controllare se due carte sono uguali
function cardsMatch(card1, card2) {
	const img1 = card1.find('img');
	const img2 = card2.find('img');
	return img1.attr('src') === img2.attr('src');
}

// Funzione per gestire il click su una carta prende come parametro l'evento che ha scatenato la funzione
function handleCardClick(event) {
	const card = $(event.currentTarget);

	if (!gameStarted) {
		gameStarted = true;
		countdown = setInterval(function () {
			time += 0.1;
			time = parseFloat(time.toFixed(2));
			$('#time').text(time);
		}, 100);
	}

	// La carta è già stata girata o è la stessa carta cliccata in precedenza o è già stata trovata un'altra carta 
	if (!card.hasClass('hidden') || firstCard === card || secondCard) { 
		return;
	}

	flipCard(card);

	if (!firstCard) firstCard = card;
	else {
		secondCard = card;
		// Le due carte sono uguali
		if (cardsMatch(firstCard, secondCard)) { 
			cardsMatched += 2;
			firstCard = null;
			secondCard = null; 
			if (cardsMatched === 16) vittoria();
		}
		else {
			setTimeout(() => {
				hideCard(firstCard); 
				hideCard(secondCard); 
				firstCard = null;
				secondCard = null;
			}, 1000);
		}
	}
}

// Funzione per inizializzare il gioco
function initGame() {
	gridItems.each(function (i) {
		const card = $(this);
		const index = generateRandomNumber();

		hideCard(card);
		setImage(card, index);
		card.on('click', handleCardClick); 
	});
	shuffle(images);
}

// Funzione per resettare il gioco
function resetGame() {
	firstCard = null;
	secondCard = null;
	cardsMatched = 0;
	generatedNumbers = [];
	gameStarted = false;
	clearInterval(countdown);
	time = 0;
	$('#time').text(time); 
	gridItems.each(function () {
		const card = $(this);
		card.find('img').remove(); 
	});
	initGame();
}

// Funzione chiamata a fine partita per aggiornare la classifica
function vittoria() {
	gameStarted = false;
	clearInterval(countdown);
	setTimeout(function () {
		aggiornaClassifica();
	}, 1000);
}

$(function () {
	initGame();
	$('#reset-btn').on('click', resetGame);
	console.log(generatedNumbers);
});


/****************************** Gestione Classifica ******************************/

function renderClassifica(data) {
	const container = $('.classifica');
	container.empty();

	let html = `
	<h1 class="textSide">Classifica</h1>
	<table>
		<tr>
			<th>Posizione</th>
			<th>Username</th>
			<th>Tempo</th>
		</tr>
	`;
	data.forEach(function (value, index) {
		html += `
		<tr>
			<td>${index + 1}</td>
			<td>${value.username}</td>
			<td>${value.score}</td>
		</tr>`;
	});
	html += '</table>';
	container.html(html);
}

function get_classifica() {
	$.ajax({
		type: "GET",
		url: "/api/memory_stats",
		success: function (data) {
			renderClassifica(data);
		},
		error: function (xhr, status, error) {
			console.log("Errore: " + xhr.responseText);
		}
	});
}

// aggiorna la classifica nel database
function aggiornaClassifica() {
	$.ajax({
		type: "POST",
		url: "/api/memory_stats_update",
		data: {
			score: time
		},
		success: function (data) {
			get_classifica();
		},
		error: function (xhr, status, error) {
			console.log("Errore: " + xhr.responseText);
		}
	});
}