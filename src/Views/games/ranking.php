<div class="top-bg">
    <div id="container">
        <h1 class="textCenter">Classifiche</h1>
        <div class="grid">

            <?php foreach ($stats as $gameName => $gameStats): ?>
                <div class="box-classifica">
                    <div>
                        <h1 class="textCenter"><?php echo htmlspecialchars($gameName); ?></h1>
                    </div>
                    <div class="container classifica d-flex justify-content-center" id="classifica<?php echo strtolower($gameName); ?>">
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
                                <?php foreach ($gameStats as $index => $stat): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo htmlspecialchars($stat['username']); ?></td>
                                        <td><?php echo htmlspecialchars($stat['score']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
</div>