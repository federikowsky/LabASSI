<?php $logged = session()->has('username') ? true : false ?>

<header class="container-fluid header d-flex justify-content-between"> <!-- sticky-top -->
    <!-- Logo container -->
    <div class="logo">
        <a class="d-flex justify-content-center" href="/">
            <img src="/assets/Logo.png" id="logoimg">
        </a>
    </div>

    <!-- Navbar container -->
    <nav class="navbar navbar-expand-lg" id="mynav">

        <div class="d-flex justify-content-center">
            <div class="navbar-toggler header-link-container px-3" id="search-btn">
                <i class="fa fa-search fa-lg"></i>
            </div>
            <button class="navbar-toggler" type="button" id="hamburger">
                <div class="animated-togglebutton">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </div>

        <div class="collapse navbar-collapse flex-row">
            <div class="navbar-nav">
                <div class="header-link-container mx-2 pl-2">
                    <img src="/assets/forme.png" alt="" class="jumping-foto2">
                    <a class="nav-item nav-link" href="/about-us">About Us</a>
                </div>
                <div class="header-link-container mx-2">
                    <img src="/assets/forme.png" alt="" class="jumping-foto2">
                    <a class="nav-item nav-link" href="/tournaments">Tornei</a>
                </div>
                <div class="header-link-container mx-2">
                    <img src="/assets/forme.png" alt="" class="jumping-foto2">
                    <a class="nav-item nav-link" href="/game/ranking">Classifiche</a>
                </div>
                <?php if ($logged): ?>
                    <!-- Se l'utente è loggato, mostra il link al profilo -->
                    <div class="header-link-container mx-2">
                        <img src="/assets/forme.png" alt="" class="jumping-foto2">
                        <a class="nav-item nav-link" href="/user/profile">Profile</a>
                    </div>
                <?php else: ?>
                    <!-- Se l'utente non è loggato, mostra il link di login -->
                    <div class="header-link-container mx-2">
                        <img src="/assets/forme.png" alt="" class="jumping-foto2">
                        <a class="nav-item nav-link" href="/auth/login" name="personal">Login</a>
                    </div>
                <?php endif; ?>
                <div class="header-link-container mx-2 pe-2" id="search-btn2">
                    <i class="fa fa-search fa-lg"></i>
                </div>
            </div>
        </div>
    </nav>

</header>


<!-- Dropdown Responsive Navbar -->
<div class="dropdown-nav-container">
    <div class="dropdown-wrapp">
        <a class="nav-item nav-link dropdown-link" href="/about-us">About Us</a>
    </div>
    <div class="dropdown-separator"></div>
    <div class="dropdown-wrapp">
        <a class="nav-item nav-link dropdown-link" href="/tournaments">Tornei</a>
    </div>
    <div class="dropdown-separator"></div>
    <div class="dropdown-wrapp">
        <a class="nav-item nav-link dropdown-link" href="/game/ranking">Classifiche</a>
    </div>
    <div class="dropdown-separator"></div>
    <div class="dropdown-wrapp">
        <?php if ($logged): ?>
            <a class="nav-item nav-link dropdown-link" href="/user/profile">Profile</a>
        <?php else: ?>
            <a class="nav-item nav-link dropdown-link" href="/auth/login">Login</a>
        <?php endif; ?>
    </div>
</div>

<!-- Searchbar -->
<div class="dropdown-search-container">

    <div class="search-container">
        <div class="dropdown-wrapp d-flex align-items-center">
            <input type="text" class="search-input" maxlength="256" name="search" filter-by="*" data-name="Search" placeholder="Cerca">
            <div class="close-search">
                <img src="https://assets.website-files.com/61c070585317d242d3a59789/61c070585317d200afa59815_search-close.svg" loading="lazy">
            </div>
        </div>

        <div class="dropdown-separator-search-input"></div>
    </div>


    <div class="container-fluid search-game-result">

    </div>
</div>


<main class="d-flex flex-fill flex-column justify-content-center">
