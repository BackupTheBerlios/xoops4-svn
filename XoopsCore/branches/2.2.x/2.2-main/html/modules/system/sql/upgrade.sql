CREATE TABLE `user_profile` (
  `profileid` int(12) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`profileid`)
) TYPE=MyISAM;

CREATE TABLE `user_profile_field` (
  `fieldid` int(12) unsigned NOT NULL auto_increment,
  `field_type` varchar(30) NOT NULL default '',
  `field_valuetype` tinyint(2) unsigned NOT NULL default '0',
  `field_name` varchar(255) NOT NULL default '',
  `field_title` varchar(255) NOT NULL default '',
  `field_description` text NOT NULL,
  `field_required` tinyint(2) unsigned NOT NULL default '0',
  `field_maxlength` tinyint(6) unsigned NOT NULL default '0',
  `field_weight` tinyint(6) unsigned NOT NULL default '0',
  `field_default` text NOT NULL,
  `field_moduleid` int(12) unsigned NOT NULL default '0',
  `field_notnull` tinyint(2) unsigned NOT NULL default '0',
  `field_edit` tinyint(2) unsigned NOT NULL default '0',
  `field_show` tinyint(2) unsigned NOT NULL default '0',
  `field_config` tinyint(2) unsigned NOT NULL default '0',
  `field_options` text NOT NULL default '',
  `field_register` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`fieldid`),
  UNIQUE KEY `field_name` (`field_name`)
) TYPE=MyISAM;

