delimiter $$

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `password` char(32) DEFAULT NULL,
  `active` bit(1) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `gender` tinyint(4) DEFAULT NULL,
  `activationdate` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail_UNIQUE` (`mail`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8$$

delimiter $$

CREATE TABLE `validationkeys` (
  `userid` int(11) NOT NULL,
  `validationkey` char(36) NOT NULL,
  `datecreated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`),
  CONSTRAINT `FK_USER` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8$$

delimiter $$

CREATE TABLE `routepoints` (
  `id` int(11) NOT NULL,
  `routeid` int(11) NOT NULL,
  `lat` bigint(20) DEFAULT NULL,
  `lon` bigint(20) DEFAULT NULL,
  `ele` float DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`,`routeid`),
  KEY `route` (`routeid`),
  CONSTRAINT `route` FOREIGN KEY (`routeid`) REFERENCES `routes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8$$


delimiter $$

CREATE TABLE `routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `latestupdate` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`,`userid`),
  KEY `user` (`userid`),
  CONSTRAINT `user` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8$$





