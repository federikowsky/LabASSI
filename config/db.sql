CREATE TABLE users
(
    id                INT AUTO_INCREMENT PRIMARY KEY,
    username          VARCHAR(25)  NOT NULL,
    email             VARCHAR(255) NOT NULL,
    password          VARCHAR(255) DEFAULT NULL,
    is_admin          TINYINT(1)   NOT NULL DEFAULT 0,
    active            TINYINT(1)            DEFAULT 0,
    activation_code   VARCHAR(255) DEFAULT NULL,
    activation_expiry DATETIME     DEFAULT NULL,
    activated_at      DATETIME              DEFAULT NULL,
    created_at        TIMESTAMP    NOT NULL DEFAULT current_timestamp(),
    updated_at        DATETIME              DEFAULT current_timestamp() ON UPDATE current_timestamp()
);

CREATE TABLE user_oauth
(
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT         NOT NULL,
    provider        VARCHAR(50) NOT NULL,
    provider_id     VARCHAR(255) NOT NULL,
    refresh_token   VARCHAR(255) DEFAULT NULL,
    created_at      TIMESTAMP   NOT NULL DEFAULT current_timestamp(),
    updated_at      DATETIME    NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),

    CONSTRAINT uo_fk_user_id
        FOREIGN KEY (user_id)
            REFERENCES users (id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);


CREATE TABLE user_tokens
(
    id               INT AUTO_INCREMENT PRIMARY KEY,
    selector         VARCHAR(255) NOT NULL,
    hashed_validator VARCHAR(255) NOT NULL,
    user_id          INT      NOT NULL,
    expiry           DATETIME NOT NULL,
    CONSTRAINT fk_user_id
        FOREIGN KEY (user_id)
            REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE games
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    title       VARCHAR(255) NOT NULL,
    description TEXT         NOT NULL,
    image_path  VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT current_timestamp(),
    updated_at  DATETIME     NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
);

CREATE TABLE game_data (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    game_id    INT NOT NULL,
    score      FLOAT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
    updated_at DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    
    CONSTRAINT gd_fk_user_id
        FOREIGN KEY (user_id)
            REFERENCES users (id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,

    CONSTRAINT gd_fk_game_id
        FOREIGN KEY (game_id)
            REFERENCES games (id)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);

CREATE TABLE tournaments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL UNIQUE,
    description TEXT NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    status ENUM('upcoming', 'ongoing', 'completed') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE dummy_players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE
);

CREATE TABLE tournament_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    participant_id INT NOT NULL, 
    is_dummy BOOLEAN DEFAULT FALSE, 
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE
);

CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id INT NOT NULL,
    participant1_id INT DEFAULT NULL, 
    participant2_id INT DEFAULT NULL, 
    is_dummy1 BOOLEAN DEFAULT FALSE, 
    is_dummy2 BOOLEAN DEFAULT FALSE, 
    match_time DATETIME NOT NULL,
    round INT NOT NULL,
    status ENUM('upcoming', 'ongoing', 'completed') DEFAULT 'upcoming',
    result VARCHAR(255) DEFAULT NULL,
    winner_id INT DEFAULT NULL,
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE
);
