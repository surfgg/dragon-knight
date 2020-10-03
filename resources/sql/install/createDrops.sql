DROP TABLE IF EXISTS {{ table }};

CREATE TABLE {{ table }} (
    id mediumint(8) unsigned NOT NULL auto_increment,
    name varchar(30) NOT NULL default '',
    mlevel smallint(5) unsigned NOT NULL default '0',
    type smallint(5) unsigned NOT NULL default '0',
    attribute1 varchar(30) NOT NULL default '',
    attribute2 varchar(30) NOT NULL default '',
    PRIMARY KEY  (id)
);