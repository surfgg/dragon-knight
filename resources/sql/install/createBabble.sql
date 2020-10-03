DROP TABLE IF EXISTS {{ table }};

CREATE TABLE {{ table }} (
    id int(10) unsigned NOT NULL auto_increment,
    posttime time NOT NULL default '00:00:00',
    author varchar(30) NOT NULL default '',
    babble varchar(120) NOT NULL default '',
    PRIMARY KEY  (id)
);