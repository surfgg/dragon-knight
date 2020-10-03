DROP TABLE IF EXISTS {{ table }};

CREATE TABLE {{ table }} (
  id smallint(5) unsigned NOT NULL auto_increment,
  type tinyint(3) unsigned NOT NULL default '0',
  name varchar(30) NOT NULL default '',
  buycost smallint(5) unsigned NOT NULL default '0',
  attribute smallint(5) unsigned NOT NULL default '0',
  special varchar(50) NOT NULL default '',
  PRIMARY KEY  (id)
);