<div class="container" id="container">

	<div class="container simon">
		<div class="row-12">
			<h1 class="text-center textCenter">Simon</h1>
			<p>Livello: <span id="level">0</span></p>
		</div>
		<div id="griglia">
			<div class="color red"></div>
			<div class="color blue"></div>
			<div class="color orange"></div>
			<div class="color yellow"></div>
			<div class="color purple"></div>
			<div class="color green"></div>
			<div class="color pink"></div>
			<div class="color brown"></div>
			<div class="color white"></div> 
		</div>
		<button class="btn btn-primary" id="start-btn">Start</button>
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