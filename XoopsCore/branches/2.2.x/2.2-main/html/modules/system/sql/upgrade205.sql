CREATE TABLE `block_instance` (
  `instanceid` int(12) unsigned NOT NULL auto_increment,
  `bid` int(12) unsigned NOT NULL,
  `options` text NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `side` tinyint(1) unsigned NOT NULL default '0',
  `weight` smallint(5) unsigned NOT NULL default '0',
  `visible` tinyint(1) unsigned NOT NULL default '0',
  `bcachetime` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`instanceid`),
  KEY `join` (`instanceid`, `visible`, `weight`)
) TYPE=MyISAM;