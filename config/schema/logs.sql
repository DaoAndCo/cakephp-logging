CREATE TABLE `logs` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `created` DATETIME NOT NULL,
    `level` VARCHAR(50) NOT NULL,
    `scope` VARCHAR(50) NULL DEFAULT NULL,
    `user_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    `message` TEXT NULL,
    `context` TEXT NULL,
    PRIMARY KEY (`id`),
    INDEX `user_id` (`user_id`),
    INDEX `scope` (`scope`),
    INDEX `level` (`level`)
)
COLLATE='utf8_general_ci'
;