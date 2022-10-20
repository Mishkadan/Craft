CREATE TABLE IF NOT EXISTS `#__pushnotification_config` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`appid` VARCHAR(255)  NOT NULL ,
`restapikey` text COLLATE utf8mb4_unicode_ci NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__pushnotification_autosends` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`jelement` VARCHAR(255)  NOT NULL ,
`cid` INT(11)  NOT NULL ,
`send_state` tinyint(3) NOT NULL DEFAULT '0',
`last_sent_on` datetime NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;