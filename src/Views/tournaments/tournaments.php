<div class="tournament-section">
    <div class="content-tournament">
        <h1 class="h1-colored ">Tournaments</h1>
        <div class="tournament-position">
            <div class="tournament-list">
                <?php foreach ($tournaments as $index => $tournament) : ?>
                    <?php 
                        $X = rand(0, 30);
                        $Y = rand(0, 30);
                        $imgNumber = ($index % 8) + 1;
                    ?>
                    <div class="card" style="background-position: <?= $X ?>% <?= $Y ?>%;">
                        <img src="/assets/cardFoto.png" class="jumping-foto">
                        <div class="logo-container">
                            <img src="/assets/memory/<?= $imgNumber ?>.png" alt="Foto">
                        </div>
                        <div class="title-container">
                            <h4 class="card-title"><?= htmlspecialchars($tournament['name']) ?></h4>
                        </div>
                        <div class="description-container">
                            <p class="card-text"><?= htmlspecialchars($tournament['description']) ?></p>
                            <?php if ($tournament['status'] === 'upcoming') : ?>
                                <?php if ($tournament['is_registered']) : ?>
                                    <form data-id="<?= $tournament['id'] ?>" class="form-subscribe" 
                                    action="/tournaments/<?= $tournament['id'] ?>/unsubscribe" method="post">
                                        <?= csrf_field() ?>
                                    </form>
                                    <button class="play-button unsubscribe" data-id="<?= $tournament['id'] ?>">Unsubscribe</button>
                                <?php else : ?>
                                    <form data-id="<?= $tournament['id'] ?>" class="form-subscribe" 
                                    action="/tournaments/<?= $tournament['id'] ?>/subscribe" method="post">
                                        <?= csrf_field() ?>
                                    </form>
                                    <button class="play-button subscribe" data-id="<?= $tournament['id'] ?>">Subscribe</button>
                                <?php endif; ?>
                            <?php elseif ($tournament['status'] === 'ongoing') : ?>
                                <?php if ($tournament['is_registered']) : ?>
                                    <a href="/tournaments/<?= $tournament['id'] ?>/dashboard" class="play-button">Play</a>
                                <?php else : ?>
                                    <button class="play-button bg-secondary disabled">Not registered</button>
                                <?php endif; ?>
                            <?php elseif ($tournament['status'] === 'completed') : ?>
                                <a href="/tournaments/<?= $tournament['id'] ?>/results" class="play-button">Result</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
