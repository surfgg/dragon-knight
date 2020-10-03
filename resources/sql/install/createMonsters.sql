DROP TABLE IF EXISTS {{ table }};

CREATE TABLE {{ table }} (
  id smallint(5) unsigned NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  maxhp smallint(5) unsigned NOT NULL default '0',
  maxdam smallint(5) unsigned NOT NULL default '0',
  armor smallint(5) unsigned NOT NULL default '0',
  level smallint(5) unsigned NOT NULL default '0',
  maxexp smallint(5) unsigned NOT NULL default '0',
  maxgold smallint(5) unsigned NOT NULL default '0',
  immune tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
);