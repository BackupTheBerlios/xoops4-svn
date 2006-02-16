CREATE TABLE `profile_category` (
  `catid` int(12) unsigned NOT NULL auto_increment,
  `cat_title` varchar(255) NOT NULL default '',
  `cat_description` text NOT NULL,
  `cat_weight` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`catid`)
) TYPE=MyISAM;

CREATE TABLE `profile_fieldcategory` (
  `fieldid` int(12) unsigned NOT NULL,
  `catid` int(12) unsigned NOT NULL,
  `field_weight` tinyint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldid`)
) TYPE=MyISAM;