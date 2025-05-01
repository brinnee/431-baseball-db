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
GRANT SELECT, INSERT, UPDATE ON baseball_db.Members TO 'manager'@'localhost';
GRANT SELECT, INSERT, UPDATE ON baseball_db.Statistics TO 'manager'@'localhost';
GRANT SELECT, INSERT, UPDATE ON baseball_db.TEAM TO 'manager'@'localhost';
FLUSH PRIVILEGES;

