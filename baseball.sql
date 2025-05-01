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


CREATE TABLE Players (
    playerID INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    age TINYINT UNSIGNED DEFAULT 1,
    position VARCHAR(150) NOT NULL,
    dob DATE,
    team_id INT UNSIGNED NOT NULL,
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
    Plate_appearance TINYINT(2) UNSIGNED DEFAULT 0,
    Runs_Scored TINYINT(3) UNSIGNED DEFAULT 0,
    Hits TINYINT(3) UNSIGNED DEFAULT 0,
    Home_runs TINYINT(3) UNSIGNED DEFAULT 0,
    CONSTRAINT fk_player FOREIGN KEY (Player) REFERENCES Players(playerID) ON DELETE CASCADE
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
    member_id INT UNSIGNED NULL, -- set to null so coaches and visitors don't need a member id
    FOREIGN KEY (role_id) REFERENCES Roles(ID) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES Players(playerID) ON DELETE CASCADE
);

-- insert teams
INSERT INTO TEAM VALUES
    (0, "None", ""),
    (1, "Dodgers", "Los Angeles"),
    (2, "Yankees", "New York"),
    (3, "Cubs", "Chicago"),
    (4, "Red Sox", "Boston"),
    (5, "Giants", "San Francisco");

-- insert players and coaches
INSERT INTO Players (playerID, name, age, position, dob, team_id, Street, City, State, Country, ZipCode) VALUES 
(100, 'Woody Sree', 24, 'Forward', '2001-06-15', 1, '123 Maple Ave', 'Irvine', 'CA', 'USA', '92612'),
(101, 'Maya Chen', 22, 'Midfielder', '2002-03-22', 2, '456 Oak St', 'Anaheim', 'CA', 'USA', '92805'),
(102, 'Leo Thompson', 27, 'Defender', '1997-09-10', 1, '789 Pine Blvd', 'Santa Ana', 'CA', 'USA', '92701'),
(103, 'Ava Patel', 20, 'Goalkeeper', '2004-11-05', 3, '321 Birch Ln', 'Fullerton', 'CA', 'USA', '92831'),
(104, 'Jaxon Rivera', 25, 'Striker', '1999-01-30', 2, '654 Cedar Dr', 'Tustin', 'CA', 'USA', '92780');


-- insert stats
INSERT INTO Statistics (Player, Games_played, Plate_appearance, Runs_Scored, Hits, Home_runs) VALUES
(100, 15, 52, 12, 18, 4),
(101, 18, 60, 9, 21, 2),
(102, 22, 74, 7, 25, 1),
(103, 20, 50, 4, 13, 0),
(104, 17, 65, 14, 27, 5);


-- insert matches
INSERT INTO Matches (home_team,away_team,home_score,away_score,match_date,match_status) VALUES
(1,2,4,2,'2025-04-15',"FINAL"),
(1,5,2,3,'2025-04-22',"FINAL"),
(1,3,7,6,'2025-04-29',"FINAL"),
(1,4,0,0,'2025-05-05',"UPCOMING");

-- insert roles
INSERT INTO Roles (ID, role_name) VALUES
(1, 'manager'),
(2, 'coach'),
(3, 'player'),
(4, 'visitor');

-- manager
CREATE USER IF NOT EXISTS 'manager'@'localhost' IDENTIFIED BY 'secureManagerPassword';
GRANT SELECT, INSERT, UPDATE ON baseball_db.Players TO 'manager'@'localhost';
GRANT SELECT, INSERT, UPDATE ON baseball_db.Statistics TO 'manager'@'localhost';
GRANT SELECT, INSERT, UPDATE ON baseball_db.TEAM TO 'manager'@'localhost';
FLUSH PRIVILEGES;

