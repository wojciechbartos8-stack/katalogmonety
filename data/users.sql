-- Utworzenie bazy danych
DROP DATABASE IF EXISTS katalogmonety;
CREATE DATABASE katalogmonety CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE katalogmonety;

-- Utworzenie tabeli users
CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    login VARCHAR(50) NOT NULL,
    haslo VARCHAR(255) NOT NULL,
    rola VARCHAR(50) DEFAULT 'user',
    wojewodztwo VARCHAR(100) DEFAULT NULL,
    miasto VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Wstawienie u¿ytkownika administratora
INSERT INTO users (login, haslo, rola, wojewodztwo, miasto)
VALUES ('admin', '1234', 'admin', 'Zachodniopomorskie', 'Gryfino');
