<div class="container">
    <div class="row">
        <!-- Gioco al centro -->
        <div class="col-md-8 offset-md-1 mb-sm-5 d-flex justify-content-center">
            <div class="game text-center">
                <div class="row">
                    <div class="col-12">
                        <h1 class="textCenter">DOT</h1>
                    </div>
                </div>
                <div id="dot"></div>
                <p>Time: <span id="time">10</span> seconds</p>
                <p>Score: <span id="score">0</span></p>
                <button class="btn btn-primary" id="reset-btn">Reset</button>
            </div>
        </div>

        <!-- Classifica a destra confinata nella penultima colonna -->
        <div class="col-md-3 d-flex align-items-center">
            <div class="container classifica text-center">
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
    </div>
</div>
