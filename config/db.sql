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