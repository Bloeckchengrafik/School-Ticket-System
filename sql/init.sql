DROP TABLE IF EXISTS ReadTo;
DROP TABLE IF EXISTS UserMagicLinkKey;
DROP TABLE IF EXISTS UserPassKey;
DROP TABLE IF EXISTS isMemberIn;
DROP TABLE IF EXISTS Message;
DROP TABLE IF EXISTS Ticket;
DROP TABLE IF EXISTS Room;
DROP TABLE IF EXISTS PresetMessage;
DROP TABLE IF EXISTS Device;
DROP TABLE IF EXISTS User;
DROP TABLE IF EXISTS Email;

CREATE OR REPLACE TABLE User
(
    user_id       INTEGER PRIMARY KEY AUTO_INCREMENT,
    first_name    varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, -- Using utf8mb4_unicode_ci because this is unsanitized user input
    last_name     varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    email         varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    account_class enum ('admin', 'teacher', 'supporter')  NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE OR REPLACE TABLE UserPassKey
(
    user_id       INTEGER PRIMARY KEY,
    password_hash varchar(255) NOT NULL,
    last_updated  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User (user_id)
);

CREATE OR REPLACE TABLE UserMagicLinkKey
(
    user_id INTEGER,
    code    varchar(255) NOT NULL,
    PRIMARY KEY (user_id, code),
    FOREIGN KEY (user_id) REFERENCES User (user_id)
);

CREATE OR REPLACE TABLE Room
(
    room_id  varchar(255)                                                          NOT NULL,
    building enum ('Hauptgebäude', 'Westgebäude', 'Q-Gebäude', 'Externes Gebäude') NOT NULL,
    PRIMARY KEY (room_id)
);

CREATE OR REPLACE TABLE Device
(
    device_id          INTEGER PRIMARY KEY AUTO_INCREMENT,
    device_name        varchar(255) NOT NULL,
    device_description varchar(255) NOT NULL
);

CREATE OR REPLACE TABLE Ticket
(
    ticket_id  INTEGER PRIMARY KEY AUTO_INCREMENT,
    title      varchar(255)            NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status     enum ('open', 'closed') NOT NULL,
    user_id    INTEGER                 NOT NULL,
    room_id    varchar(255)            NOT NULL,
    device_id  INTEGER                 NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User (user_id),
    FOREIGN KEY (room_id) REFERENCES Room (room_id),
    FOREIGN KEY (device_id) REFERENCES Device (device_id)
);

CREATE OR REPLACE TABLE isMemberIn
(
    user_id   INTEGER,
    ticket_id INTEGER,
    PRIMARY KEY (user_id, ticket_id),
    FOREIGN KEY (user_id) REFERENCES User (user_id),
    FOREIGN KEY (ticket_id) REFERENCES Ticket (ticket_id)
);

CREATE OR REPLACE TABLE Message
(
    message_id   INTEGER PRIMARY KEY AUTO_INCREMENT,
    content      varchar(255)            NOT NULL,
    message_type enum ('standard', 'hr') NOT NULL DEFAULT 'standard',
    created_at   TIMESTAMP                        DEFAULT CURRENT_TIMESTAMP,
    user_id      INTEGER                 NOT NULL,
    ticket_id    INTEGER                 NOT NULL,
    FOREIGN KEY (user_id) REFERENCES User (user_id),
    FOREIGN KEY (ticket_id) REFERENCES Ticket (ticket_id)
);

CREATE OR REPLACE TABLE PresetMessage
(
    preset_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    device_id INTEGER      NOT NULL,
    name      varchar(255) NOT NULL,
    content   varchar(255) NOT NULL,
    FOREIGN KEY (device_id) REFERENCES Device (device_id)
);

CREATE OR REPLACE TABLE ReadTo
(
    user_id    INTEGER,
    message_id INTEGER,
    ticket_id  INTEGER,
    PRIMARY KEY (user_id, message_id, ticket_id),
    FOREIGN KEY (user_id) REFERENCES User (user_id),
    FOREIGN KEY (message_id) REFERENCES Message (message_id),
    FOREIGN KEY (ticket_id) REFERENCES Ticket (ticket_id)
);

CREATE OR REPLACE TABLE Email
(
    email_id INTEGER PRIMARY KEY AUTO_INCREMENT,
    receiver varchar(255) NOT NULL,
    subject  varchar(255) NOT NULL,
    content  TEXT         NOT NULL
);

INSERT INTO dev.Device (device_name, device_description)
VALUES ('Beamer', 'groß, weiß, über der Tafel');

INSERT INTO dev.Device (device_name, device_description)
VALUES ('Lautsprecher', 'oben rechts und oben links');

INSERT INTO dev.Device (device_name, device_description)
VALUES ('Projektionsfläche', 'groß, weiß, hinter der Tafel');

INSERT INTO dev.Device (device_name, device_description)
VALUES ('Bedienbox', 'auch: Epsonbox');

INSERT INTO dev.Device (device_name, device_description)
VALUES ('PC', 'Kasten unter dem Bildschirm');

INSERT INTO dev.Device (device_name, device_description)
VALUES ('Bildschirm', 'Kasten auf dem PC');

INSERT INTO dev.Device (device_name, device_description)
VALUES ('Dokumentenkamera', 'Hat die Aufschrift "EPSON"');

INSERT INTO dev.Device (device_name, device_description)
VALUES ('Maus', 'Bediengerät mit wenigen Tasten');

INSERT INTO dev.Device (device_name, device_description)
VALUES ('Tastatur', 'Bediengerät mit vielen Tasten');

INSERT INTO dev.Device (device_name, device_description)
VALUES ('Switch', 'Gerät mit Knopf darauf');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (1, 'Beamer geht nicht an', 'Der Beamer in diesem Raum geht nicht an');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (1, 'Beamer hat nur ein blaues Bild', 'Der Beamer hat nur ein blaues Bild');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (2, 'Keinen Ton', 'Ich höre hier keinen Ton');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (2, 'Ton verzerrt', 'Der Ton ist hier sehr verzerrt');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (3, 'Nichts zu erkennen', 'Auf der Projektionsfläche ist nichts zu erkennen');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (4, 'Die Bedienbox fehlt', 'Die Bedienbox fehlt in diesem Raum');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (5, 'Nicht an', 'Der PC geht nicht an');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (6, 'Nicht an', 'Der Bildschirm geht nicht an');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (7, 'Funktioniert nicht', 'Die Dokumentenkamera funktioniert nicht');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (8, 'Funktioniert nicht', 'Die Maus funktioniert nicht');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (9, 'Funktioniert nicht', 'Die Tastatur funktioniert nicht');

INSERT INTO dev.PresetMessage (device_id, name, content)
VALUES (10, 'Schalten geht nicht', 'Ich kann nicht zwischen den Quellen Hin-und-her-wechseln');

