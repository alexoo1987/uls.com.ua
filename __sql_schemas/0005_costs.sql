CREATE TABLE `costs_type` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `subtract` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `costs` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `type` int(5) NOT NULL,
  `date` DATE NOT NULL,
  `user_id` int(5) NOT NULL,
  `amount` int(10) NOT NULL,
  `comment` VARCHAR (255) NOT NULL,
  PRIMARY KEY (`id`),
FOREIGN KEY(`type`) REFERENCES costs_type(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO permissions (`name`, `description`) VALUES ('costs_manage', 'Возможность добавлять затраты');

