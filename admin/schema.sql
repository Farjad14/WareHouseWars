DROP TABLE IF EXISTS users CASCADE;
DROP TABLE IF EXISTS highscores CASCADE;

CREATE TABLE users
(
id SERIAL primary key,
username varchar(255),
password varchar(255),
email varchar(255),
name varchar(255),
numgamesplayed integer,
lastlogin timestamp);

CREATE TABLE highscores
(
id SERIAL primary key,
username varchar(255),
userscore integer,
gameduration integer);
