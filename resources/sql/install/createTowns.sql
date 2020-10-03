DROP TABLE IF EXISTS {{ table }};

CREATE TABLE {{ table }} (
  id tinyint(3) unsigned NOT NULL auto_increment,
  name varchar(30) NOT NULL default '',
  latitude smallint(6) NOT NULL default '0',
  longitude smallint(6) NOT NULL default '0',
  innprice tinyint(4) NOT NULL default '0',
  mapprice smallint(6) NOT NULL default '0',
  travelpoints smallint(5) unsigned NOT NULL default '0',
  itemslist text NOT NULL,
  PRIMARY KEY  (id)
);