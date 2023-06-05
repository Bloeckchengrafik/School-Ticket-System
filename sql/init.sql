DROP TABLE IF EXISTS UserMagicLinkKey;
DROP TABLE IF EXISTS UserPassKey;
DROP TABLE IF EXISTS isMemberIn;
DROP TABLE IF EXISTS Message;
DROP TABLE IF EXISTS Ticket;
DROP TABLE IF EXISTS Room;
DROP TABLE IF EXISTS PresetMessage;
DROP TABLE IF EXISTS Device;
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
    user_id INTEGER,
    code varchar(255) NOT NULL,
    PRIMARY KEY (user_id, code),
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);

CREATE OR REPLACE TABLE Room (
    room_id INTEGER AUTO_INCREMENT,
    building enum('Hauptgebäude', 'Westgebäuse', 'Q-Gebäude', 'Externes Gebäude') NOT NULL,
    PRIMARY KEY (room_id, building)
);

CREATE OR REPLACE TABLE Device (
    device_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    device_name varchar(255) NOT NULL,
    device_description varchar(255) NOT NULL
);

CREATE OR REPLACE TABLE Ticket (
    ticket_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    title varchar(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status enum('open', 'closed') NOT NULL,
    user_id INTEGER NOT NULL,
    room_id INTEGER NOT NULL,
    device_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (room_id) REFERENCES Room(room_id),
    FOREIGN KEY (device_id) REFERENCES Device(device_id)
);

CREATE OR REPLACE TABLE isMemberIn (
    user_id INTEGER,
    ticket_id INTEGER,
    PRIMARY KEY (user_id, ticket_id),
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (ticket_id) REFERENCES Ticket(ticket_id)
);

CREATE OR REPLACE TABLE Message (
    message_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    content varchar(255) NOT NULL,
    message_type enum('standard', 'hr') NOT NULL DEFAULT 'standard',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INTEGER NOT NULL,
    ticket_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (ticket_id) REFERENCES Ticket(ticket_id)
);

CREATE OR REPLACE TABLE PresetMessage (
    preset_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    device_id INTEGER NOT NULL,
    name varchar(255) NOT NULL,
    content varchar(255) NOT NULL,
    FOREIGN KEY (device_id) REFERENCES Device(device_id)
);