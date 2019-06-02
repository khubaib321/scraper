DROP DATABASE IF EXISTS scraper;
CREATE DATABASE scraper;
USE scraper;

DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `location` VARCHAR(255) DEFAULT '',
    PRIMARY KEY (`id`),
    CONSTRAINT const_name_location UNIQUE (`name` , `location`)
)  ENGINE=INNODB;

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (`id`)
)  ENGINE=INNODB;

DROP TABLE IF EXISTS `companies_jobs`;
CREATE TABLE `companies_jobs` (
    `id` INT NOT NULL UNIQUE,
    `company_id` INT NOT NULL,
    `job_id` INT NOT NULL,
    PRIMARY KEY (`company_id`, `job_id`),
    CONSTRAINT fk_job FOREIGN KEY (`job_id`)
        REFERENCES jobs (`id`) ON DELETE CASCADE,
    CONSTRAINT fk_company FOREIGN KEY (`company_id`)
        REFERENCES companies (`id`) ON DELETE CASCADE
)  ENGINE=INNODB;
