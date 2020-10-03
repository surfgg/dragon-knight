DROP TABLE IF EXISTS {{ table }};

CREATE TABLE {{ table }} (
  id smallint(5) unsigned NOT NULL auto_increment,
  name varchar(30) NOT NULL default '',
  mp smallint(5) unsigned NOT NULL default '0',
  attribute smallint(5) unsigned NOT NULL default '0',
  type smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
);