-- Pieslēgšanās - mysql -u root

CREATE DATABASE viesu_gramata;

USE viesu_gramata;

CREATE TABLE IF NOT EXISTS Viesu_gramata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    Vards VARCHAR(100),
    Zina TEXT
);

SELECT * FROM Viesu_gramata;