-- Drop and create database
DROP DATABASE IF EXISTS baseball_db;
CREATE DATABASE IF NOT EXISTS baseball_db;

-- Drop and grant user
DROP USER IF EXISTS 'baseball_username'@'localhost';
GRANT SELECT, INSERT, DELETE, UPDATE, EXECUTE ON baseball_db.* TO 'csuf_username'@'localhost';

USE baseball_db;

CREATE TABLE TEAM (
    ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL
);


CREATE TABLE Members (
    playerID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    age TINYINT UNSIGNED DEFAULT 1,
    position VARCHAR(150) NOT NULL DEFAULT"BENCHED",
    dob DATE,
    team_id INT UNSIGNED NOT NULL DEFAULT 0,
    Street VARCHAR(250),
    City VARCHAR(100),
    State VARCHAR(100),
    Country VARCHAR(100),
    ZipCode VARCHAR(10),
    CONSTRAINT valid_zipcode CHECK (ZipCode REGEXP '^(?!0{5})(?!9{5})\\d{5}(-(?!0{4}|9{4})\\d{4})?$'),
    CONSTRAINT fk_team_id FOREIGN KEY (team_id) REFERENCES TEAM(ID) ON DELETE CASCADE
);


CREATE TABLE Statistics (
    ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Player INT UNSIGNED NOT NULL,
    Games_played INT UNSIGNED NOT NULL,
    Plate_appearances TINYINT(2) UNSIGNED DEFAULT 0,
    Runs_Scored TINYINT(3) UNSIGNED DEFAULT 0,
    Hits TINYINT(3) UNSIGNED DEFAULT 0,
    Home_runs TINYINT(3) UNSIGNED DEFAULT 0,
    CONSTRAINT fk_player FOREIGN KEY (Player) REFERENCES Members(playerID) ON DELETE CASCADE
);


CREATE TABLE Matches (
    ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    home_team INT UNSIGNED NOT NULL,
    away_team INT UNSIGNED NOT NULL,
    home_score TINYINT UNSIGNED DEFAULT 0,
    away_score TINYINT UNSIGNED DEFAULT 0,
    match_date DATE,
    match_status VARCHAR(100),
    CONSTRAINT fk_home FOREIGN KEY (home_team) REFERENCES TEAM(ID),
    CONSTRAINT fk_away FOREIGN KEY (away_team) REFERENCES TEAM(ID)
);

CREATE TABLE Roles (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL
);


CREATE TABLE Users (
    ID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    member_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (role_id) REFERENCES Roles(ID) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES Members(playerID) ON DELETE CASCADE
);

-- create a stats for every member
DELIMITER $$
CREATE TRIGGER after_player_insert
AFTER INSERT ON Members
FOR EACH ROW
BEGIN
    INSERT INTO Statistics (ID, Player, Games_played)
    VALUES (NEW.playerID, NEW.playerID, 0);
END$$
DELIMITER ;


-- insert teams
INSERT INTO TEAM VALUES
    (0, "None", ""),
    (1, "Dodgers", "Los Angeles"),
    (2, "Yankees", "New York"),
    (3, "Cubs", "Chicago"),
    (4, "Red Sox", "Boston"),
    (5, "Giants", "San Francisco");

-- insert players
INSERT INTO Members (name, age, position, dob, team_id, Street, City, State, Country, ZipCode) VALUES
    -- Insert players
    -- Dodgers
    ("Jack Turner", 25, "P", "1999-05-15", 1, "100 Main St", "Los Angeles", "CA", "USA", "90001"),
    ("Luis Martinez", 28, "C", "1996-03-21", 1, "101 Elm St", "Los Angeles", "CA", "USA", "90002"),
    ("Chris Owens", 30, "1B", "1994-08-10", 1, "102 Oak St", "Los Angeles", "CA", "USA", "90003"),
    ("Michael Smith", 24, "SS", "2000-12-05", 1, "103 Pine St", "Los Angeles", "CA", "USA", "90004"),
    ("Tony Kim", 27, "BENCHED", "1997-01-19", 1, "104 Cedar St", "Los Angeles", "CA", "USA", "90005"),

    -- Yankees
    ("David Lee", 26, "P", "1998-06-18", 2, "200 Broadway", "New York", "NY", "USA", "10001"),
    ("Ryan Brown", 31, "C", "1993-09-02", 2, "201 5th Ave", "New York", "NY", "USA", "10002"),
    ("Kyle Johnson", 29, "Second Base", "1995-07-22", 2, "202 Madison St", "New York", "NY", "USA", "10003"),
    ("Jamal Harris", 23, "SS", "2001-11-09", 2, "203 Wall St", "New York", "NY", "USA", "10004"),
    ("Jose Rivera", 27, "LF", "1997-04-14", 2, "204 Lexington Ave", "New York", "NY", "USA", "10005"),

    -- Cubs
    ("Marcus Allen", 25, "P", "1999-05-05", 3, "300 Lakeshore Dr", "Chicago", "IL", "USA", "60601"),
    ("Ethan White", 24, "C", "2000-03-30", 3, "301 Wacker Dr", "Chicago", "IL", "USA", "60602"),
    ("Daniel Brooks", 27, "3B", "1997-07-17", 3, "302 Clark St", "Chicago", "IL", "USA", "60603"),
    ("Brandon Young", 28, "SS", "1996-10-25", 3, "303 State St", "Chicago", "IL", "USA", "60604"),
    ("Aaron Green", 29, "CF", "1995-02-11", 3, "304 Michigan Ave", "Chicago", "IL", "USA", "60605"),

    -- Red Sox
    ("Jake Peterson", 26, "P", "1998-09-13", 4, "400 Beacon St", "Boston", "MA", "USA", "02108"),
    ("Luke Adams", 27, "C", "1997-05-22", 4, "401 Boylston St", "Boston", "MA", "USA", "02109"),
    ("Zach Davis", 24, "1B", "2000-12-30", 4, "402 Newbury St", "Boston", "MA", "USA", "02110"),
    ("Connor Hill", 28, "SS", "1996-07-08", 4, "403 Commonwealth Ave", "Boston", "MA", "USA", "02111"),
    ("Seth Nelson", 30, "RF", "1994-11-15", 4, "404 Huntington Ave", "Boston", "MA", "USA", "02112"),

    -- Giants
    ("Tyler Moore", 23, "P", "2001-04-04", 5, "500 Market St", "San Francisco", "CA", "USA", "94101"),
    ("Eric Turner", 29, "C", "1995-06-16", 5, "501 Castro St", "San Francisco", "CA", "USA", "94102"),
    ("Kevin Scott", 27, "Second Base", "1997-08-08", 5, "502 Mission St", "San Francisco", "CA", "USA", "94103"),
    ("Hunter Bell", 26, "SS", "1998-03-27", 5, "503 Van Ness Ave", "San Francisco", "CA", "USA", "94104"),
    ("Noah Grant", 31, "CF", "1993-01-03", 5, "504 Haight St", "San Francisco", "CA", "USA", "94105");

-- insert coaches
INSERT INTO Members (name, age, position, dob, team_id, Street, City, State, Country, ZipCode) VALUES
    ("Bill Jenkins", 50, "COACH", "1975-08-15", 1, "123 Dugout Rd", "Los Angeles", "CA", "USA", "90006"),
    ("Mike Torres", 54, "COACH", "1971-04-22", 2, "456 Bronx Blvd", "New York", "NY", "USA", "10451"),
    ("Linda Chen", 48, "COACH", "1977-09-03", 3, "789 Wrigley Ln", "Chicago", "IL", "USA", "60613"),
    ("Sarah O'Neil", 52, "COACH", "1972-02-28", 4, "321 Fenway St", "Boston", "MA", "USA", "02215"),
    ("James Lee", 49, "COACH", "1976-12-11", 5, "555 Giant Way", "San Francisco", "CA", "USA", "94107");

-- insert stats
-- Dodgers
UPDATE statistics SET games_played = 132, plate_appearances = 510, runs_scored = 87, hits = 143, home_runs = 21 WHERE ID = 1;
UPDATE statistics SET games_played = 145, plate_appearances = 480, runs_scored = 76, hits = 130, home_runs = 15 WHERE ID = 2;
UPDATE statistics SET games_played = 118, plate_appearances = 430, runs_scored = 69, hits = 110, home_runs = 9 WHERE ID = 3;
UPDATE statistics SET games_played = 151, plate_appearances = 552, runs_scored = 103, hits = 165, home_runs = 24 WHERE ID = 4;
UPDATE statistics SET games_played = 97,  plate_appearances = 220, runs_scored = 12,  hits = 45,  home_runs = 2  WHERE ID = 5;

-- Yankees
UPDATE statistics SET games_played = 140, plate_appearances = 560, runs_scored = 80, hits = 150, home_runs = 18 WHERE ID = 6;
UPDATE statistics SET games_played = 143, plate_appearances = 520, runs_scored = 75, hits = 134, home_runs = 20 WHERE ID = 7;
UPDATE statistics SET games_played = 136, plate_appearances = 500, runs_scored = 70, hits = 125, home_runs = 13 WHERE ID = 8;
UPDATE statistics SET games_played = 128, plate_appearances = 490, runs_scored = 88, hits = 140, home_runs = 17 WHERE ID = 9;
UPDATE statistics SET games_played = 139, plate_appearances = 510, runs_scored = 95, hits = 160, home_runs = 25 WHERE ID = 10;

-- Cubs
UPDATE statistics SET games_played = 137, plate_appearances = 540, runs_scored = 78, hits = 138, home_runs = 22 WHERE ID = 11;
UPDATE statistics SET games_played = 150, plate_appearances = 580, runs_scored = 81, hits = 155, home_runs = 19 WHERE ID = 12;
UPDATE statistics SET games_played = 144, plate_appearances = 520, runs_scored = 74, hits = 145, home_runs = 12 WHERE ID = 13;
UPDATE statistics SET games_played = 129, plate_appearances = 470, runs_scored = 61, hits = 108, home_runs = 8  WHERE ID = 14;
UPDATE statistics SET games_played = 134, plate_appearances = 505, runs_scored = 90, hits = 160, home_runs = 30 WHERE ID = 15;

-- Red Sox
UPDATE statistics SET games_played = 130, plate_appearances = 510, runs_scored = 77, hits = 148, home_runs = 14 WHERE ID = 16;
UPDATE statistics SET games_played = 146, plate_appearances = 565, runs_scored = 89, hits = 160, home_runs = 23 WHERE ID = 17;
UPDATE statistics SET games_played = 142, plate_appearances = 535, runs_scored = 85, hits = 150, home_runs = 19 WHERE ID = 18;
UPDATE statistics SET games_played = 138, plate_appearances = 495, runs_scored = 72, hits = 126, home_runs = 10 WHERE ID = 19;
UPDATE statistics SET games_played = 149, plate_appearances = 550, runs_scored = 92, hits = 170, home_runs = 28 WHERE ID = 20;

-- Giants
UPDATE statistics SET games_played = 135, plate_appearances = 540, runs_scored = 79, hits = 142, home_runs = 16 WHERE ID = 21;
UPDATE statistics SET games_played = 141, plate_appearances = 560, runs_scored = 83, hits = 155, home_runs = 21 WHERE ID = 22;
UPDATE statistics SET games_played = 125, plate_appearances = 495, runs_scored = 70, hits = 120, home_runs = 14 WHERE ID = 23;
UPDATE statistics SET games_played = 138, plate_appearances = 520, runs_scored = 86, hits = 160, home_runs = 26 WHERE ID = 24;
UPDATE statistics SET games_played = 147, plate_appearances = 580, runs_scored = 99, hits = 180, home_runs = 33 WHERE ID = 25;

-- insert matches
INSERT INTO Matches (home_team,away_team,home_score,away_score,match_date,match_status) VALUES
    (1, 2, 5, 3, '2024-03-10', 'FINAL'),
    (3, 1, 2, 4, '2024-04-01', 'FINAL'),
    (4, 1, 3, 1, '2024-04-15', 'FINAL'),
    (1, 5, 4, 2, '2024-04-28', 'FINAL'),
    (2, 3, 1, 0, '2024-03-15', 'FINAL'),
    (4, 2, 6, 4, '2024-04-10', 'FINAL'),
    (2, 5, 2, 3, '2024-04-18', 'FINAL'),
    (4, 3, 5, 3, '2024-03-22', 'FINAL'),
    (5, 3, 3, 4, '2024-03-30', 'FINAL'),
    (4, 5, 2, 0, '2024-04-05', 'FINAL'),
    (2, 1, NULL, NULL, '2025-05-20', 'UPCOMING'),
    (1, 3, NULL, NULL, '2025-05-22', 'UPCOMING'),
    (1, 4, NULL, NULL, '2025-05-25', 'UPCOMING'),
    (5, 1, NULL, NULL, '2025-05-30', 'UPCOMING'),
    (3, 2, NULL, NULL, '2025-06-02', 'UPCOMING'),
    (2, 4, NULL, NULL, '2025-06-05', 'UPCOMING'),
    (5, 2, NULL, NULL, '2025-06-08', 'UPCOMING'),
    (3, 4, NULL, NULL, '2025-06-11', 'UPCOMING'),
    (3, 5, NULL, NULL, '2025-06-15', 'UPCOMING'),
    (5, 4, NULL, NULL, '2025-06-18', 'UPCOMING');

-- insert roles
INSERT INTO Roles (ID, role_name) VALUES
(1, 'manager'),
(2, 'coach'),
(3, 'player'),
(4, 'visitor');

-- manager
CREATE USER IF NOT EXISTS 'manager'@'localhost' IDENTIFIED BY 'secureManagerPassword';
GRANT SELECT, INSERT, UPDATE ON baseball_db.Members TO 'manager'@'localhost';
GRANT SELECT, INSERT, UPDATE ON baseball_db.Statistics TO 'manager'@'localhost';
GRANT SELECT, INSERT, UPDATE ON baseball_db.TEAM TO 'manager'@'localhost';
FLUSH PRIVILEGES;

