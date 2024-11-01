<div class="container" id="container">

    <div class="container timer">
        <div>
            <h1 class="textSide">Timer</h1>
            <p><span id="time">0</span> seconds</p>
        </div>
    </div>

    <div class="game">
        <div class="row-12">
            <h1 class="text-center textCenter">Memory</h1>
        </div>

        <div class="row row-cols-4" id="griglia">
            <div class="row">
                <div class="col-auto-0 grid-item" id="0"></div>
                <div class="col-auto-1 grid-item" id="1"></div>
                <div class="col-auto-2 grid-item" id="2"></div>
                <div class="col-auto-3 grid-item" id="3"></div>
            </div>
            <div class="row">
                <div class="col-auto-0 grid-item" id="4"></div>
                <div class="col-auto-1 grid-item" id="5"></div>
                <div class="col-auto-2 grid-item" id="6"></div>
                <div class="col-auto-3 grid-item" id="7"></div>
            </div>
            <div class="row">
                <div class="col-auto-0 grid-item" id="8"></div>
                <div class="col-auto-1 grid-item" id="9"></div>
                <div class="col-auto-2 grid-item" id="10"></div>
                <div class="col-auto-3 grid-item" id="11"></div>
            </div>
            <div class="row">
                <div class="col-auto-0 grid-item" id="12"></div>
                <div class="col-auto-1 grid-item" id="13"></div>
                <div class="col-auto-2 grid-item" id="14"></div>
                <div class="col-auto-3 grid-item" id="15"></div>
            </div>
        </div>
        <button class="btn btn-primary" id="reset-btn">Reset</button>
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