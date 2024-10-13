<div class="content">
    <div class="video-container">
        <img src="/assets/VideoFade.png">
        <video autoplay loop muted playsinline src="/assets/HomeVideo.mp4" ></video>
    </div>
</div>

<div class="game-section">
    <div class="content-game">
        <h1 class="h1-colored ">Popular Games</h1>
        <div class="max-630">
            Scopri i migliori giochi classici e intrattenimento! La nostra selezione include i giochi più amati di tutti
            i tempi
        </div>
        <div class="game-position">
            <div class="game-list">
                <?php foreach ($games as $game): ?>
                    <?php 
                        $X = rand(0, 30);
                        $Y = rand(0, 30);
                    ?>
                    <div class="card" style="background-position: <?= $X ?>% <?= $Y ?>%;">
                        <img src="/assets/cardFoto.png" class="jumping-foto">
                        <div class="logo-container">
                            <img src="<?= $game['image_path'] ?>" alt="<?= htmlspecialchars($game['title']) ?>">
                        </div>
                        <div class="title-container">
                            <h4 class="card-title"><?= htmlspecialchars($game['title']) ?></h4>
                        </div>
                        <div class="description-container">
                            <p class="card-text"><?= htmlspecialchars($game['description']) ?></p>
                            <a class="play-button" href="<?= $game['link'] ?>">Let's play</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>