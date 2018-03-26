ALTER TABLE `#__redproductfinder_types` ADD `class_name` varchar(255) NOT NULL;

CREATE TABLE IF NOT EXISTS `#__redproductfinder_keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `times` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='redPRODUCTFINDER Keywords';