DROP TABLE IF EXISTS {{ table }};

CREATE TABLE {{ table }} (
  id mediumint(8) unsigned NOT NULL auto_increment,
  postdate datetime NOT NULL default '0000-00-00 00:00:00',
  content text NOT NULL,
  PRIMARY KEY  (id)
);