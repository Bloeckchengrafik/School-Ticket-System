DROP TABLE IF EXISTS UserMagicLinkKey;
DROP TABLE IF EXISTS UserPassKey;
DROP TABLE IF EXISTS User;

CREATE OR REPLACE TABLE User (
    user_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    first_name varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, -- Using utf8mb4_unicode_ci because this is unsanitized user input
    last_name varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    email varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    account_class enum('admin', 'teacher', 'supporter') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE OR REPLACE TABLE UserPassKey (
    user_id INTEGER PRIMARY KEY,
    password_hash varchar(255) NOT NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);

CREATE OR REPLACE TABLE UserMagicLinkKey (
    user_id INTEGER PRIMARY KEY,
    code varchar(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);
