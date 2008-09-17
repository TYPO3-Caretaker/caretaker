#
# Table structure for table 'tx_caretaker_test'
#
CREATE TABLE tx_caretaker_test (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
    starttime int(11) DEFAULT '0' NOT NULL,
    endtime int(11) DEFAULT '0' NOT NULL,
    fe_group int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,

	test_interval int(11) DEFAULT '0' NOT NULL,
	test_service varchar(255) DEFAULT '' NOT NULL,
	test_mode  varchar(255) DEFAULT '' NOT NULL,
	test_conf text NOT NULL,

	instances int(11) DEFAULT '0' NOT NULL,
	groups int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_caretaker_instance'
#
CREATE TABLE tx_caretaker_instance (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
    starttime int(11) DEFAULT '0' NOT NULL,
    endtime int(11) DEFAULT '0' NOT NULL,
    fe_group int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,
	public_key varchar(255) DEFAULT '' NOT NULL,
	url varchar(255) DEFAULT '' NOT NULL,

	groups text NOT NULL,
	tests int(11) DEFAULT '0' NOT NULL,

	flexinfo text NOT NULL,	

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_caretaker_instance_test_rel'
#
CREATE TABLE tx_caretaker_instance_test_rel (
	uid int(11) DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	instance_id int(11) DEFAULT '0' NOT NULL,
	test_id    int(11) DEFAULT '0' NOT NULL,
	instance_sorting int(11) DEFAULT '0' NOT NULL,
	test_sorting int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
);

#
# Table structure for table 'tx_caretaker_instancegroup'
#
CREATE TABLE tx_caretaker_group (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
    starttime int(11) DEFAULT '0' NOT NULL,
    endtime int(11) DEFAULT '0' NOT NULL,
    fe_group int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,

	title varchar(255) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,
	parent_group int(11) DEFAULT '0' NOT NULL,

	tests blob NOT NULL,

	flexinfo text NOT NULL,	

	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_caretaker_instance_group_mm'
#
CREATE TABLE tx_caretaker_instance_group_mm (
	pid int(11) DEFAULT '0' NOT NULL,
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_caretaker_group_test_rel'
#
CREATE TABLE tx_caretaker_group_test_rel (
	uid int(11) DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	group_id int(11) DEFAULT '0' NOT NULL,
	test_id    int(11) DEFAULT '0' NOT NULL,
	group_sorting int(11) DEFAULT '0' NOT NULL,
	test_sorting int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
);

#
# Table structure for table 'tx_caretaker_accounts'
#
CREATE TABLE tx_caretaker_accounts (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    starttime int(11) DEFAULT '0' NOT NULL,
    endtime int(11) DEFAULT '0' NOT NULL,
    fe_group int(11) DEFAULT '0' NOT NULL,

	protocol varchar(255) DEFAULT '' NOT NULL,
    username varchar(255) DEFAULT '' NOT NULL,
    password varchar(255) DEFAULT '' NOT NULL,
    url varchar(255) DEFAULT '' NOT NULL,
    description text NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

#
# Table structure for table 'tx_caretaker_testresults'
#
CREATE TABLE tx_caretaker_testresults (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,

	test_uid int(11) DEFAULT '0' NOT NULL,
	instance_uid int(11) DEFAULT '0' NOT NULL,
	group_uid int(11) DEFAULT '0' NOT NULL,
	
	result_status int(11) DEFAULT '0' NOT NULL,
	result_value int(11) DEFAULT '0' NOT NULL,
	result_msg varchar(255) DEFAULT '' NOT NULL,
	result_data tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

