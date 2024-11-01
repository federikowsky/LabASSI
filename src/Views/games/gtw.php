<div class="container" id="container">
	<div class="container timer">
		<div>
			<h1 class="textSide">Timer</h1>
			<p><span id="time">0</span> seconds</p>
		</div>
	</div>
	<div class = "game">
		<h1 class = "text-center textCenter">Guess the Word</h1>
		<div id="word"></div>
		<div id="guesses"></div>
		<input type="text" id="letter" placeholder="Enter a letter" maxlength="1">
		<div>
			<button class = "btn btn-primary unvisible" id="guess-btn">Guess</button>
			<button class = "btn btn-primary" id="reset-btn">Start</button>
		</div>
	</div>
	<div class="container classifica">
		<h1 class="textSide">Classifica</h1>
		<table>
			<thead>
				<tr>
					<th>Posizione</th>
					<th>Username</th>
					<th>Punteggio</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$position = 1;
				foreach ($stats as $stat) : ?>
					<tr>
						<td><?= $position ?></td>
						<td><?= htmlspecialchars($stat['username']) ?></td>
                        <td><?= htmlspecialchars($stat['score']) ?></td>
					</tr>
					<?php $position++; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
