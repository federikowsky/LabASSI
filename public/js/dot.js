$(() => {
	var score = 0;
	var time = 10;
	var countdown;
	var gameStarted = false;

	// fa partire il gioco, gestisce il punteggio. Usando la fuzione setInterval e setTimeout viene gestito 
	// il conto alla rovescia e il tempo di attesa prima di aggiornare la classifica
	function startGame() {
		gameStarted = true;
		score++;
		$('#score').text(score);

		$('#dot').click(function () {
			score++;
			$('#score').text(score);
		});

		countdown = setInterval(function () {
			time--;
			$('#time').text(time);

			if (time == 0) {
				clearInterval(countdown);
				$('#dot').unbind();
				setTimeout(function () {
					aggiornaClassifica();
				}, 100);
				gameStarted = false;
			}
		}, 1000);
	}

	// resetta il gioco e le variabili di gioco
	$('#reset-btn').click(function () {
		score = 0;
		time = 10;
		$('#score').text(score);
		$('#time').text(time);
		clearInterval(countdown);
		$('#dot').unbind();
		gameStarted = false;
		$('#dot').click(function () {
			if (!gameStarted) {
				startGame();
			}
		});
	});

	// fa partire il gioco
	$('#dot').click(function () {
		if (!gameStarted) {
			startGame();
		}
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

	// ottiene la classifica dal database, crea una tabella html e l'aggiunge all'oggetto con id specificato
	function get_classifica() {
		$.ajax({
			type: "GET",
			url: "/api/dot_stats",
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
			url: "/api/dot_stats_update",
			data: {
				score: score
			},
			success: function (data) {
				get_classifica();
			},
			error: function (xhr, status, error) {
				console.log("Errore: " + xhr.responseText);
			}
		});
	}


});
