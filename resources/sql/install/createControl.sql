DROP TABLE IF EXISTS {{ table }};

CREATE TABLE {{ table }} (
    id tinyint(3) unsigned NOT NULL auto_increment,
    gamename varchar(50) NOT NULL default '',
    gamesize smallint(5) unsigned NOT NULL default '0',
    gameopen tinyint(3) unsigned NOT NULL default '0',
    gameurl varchar(200) NOT NULL default '',
    adminemail varchar(100) NOT NULL default '',
    forumtype tinyint(3) unsigned NOT NULL default '0',
    forumaddress varchar(200) NOT NULL default '',
    class1name varchar(50) NOT NULL default '',
    class2name varchar(50) NOT NULL default '',
    class3name varchar(50) NOT NULL default '',
    diff1name varchar(50) NOT NULL default '',
    diff1mod float unsigned NOT NULL default '0',
    diff2name varchar(50) NOT NULL default '',
    diff2mod float unsigned NOT NULL default '0',
    diff3name varchar(50) NOT NULL default '',
    diff3mod float unsigned NOT NULL default '0',
    compression tinyint(3) unsigned NOT NULL default '0',
    verifyemail tinyint(3) unsigned NOT NULL default '0',
    shownews tinyint(3) unsigned NOT NULL default '0',
    showbabble tinyint(3) unsigned NOT NULL default '0',
    showonline tinyint(3) unsigned NOT NULL default '0',
    PRIMARY KEY  (id)
);