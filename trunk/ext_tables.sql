

#
# Table structure for table 'tx_caretaker_instancegroup'
#
CREATE TABLE tx_caretaker_instancegroup (
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
	host varchar(255) DEFAULT '' NOT NULL,
	ip varchar(255) DEFAULT '' NOT NULL,

	groups text NOT NULL,
	instancegroup int(11) DEFAULT '0' NOT NULL,
	notifications varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_caretaker_testgroup'
#
CREATE TABLE tx_caretaker_testgroup (
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
	instances int(11) DEFAULT '0' NOT NULL,
	
	tests blob NOT NULL,
	notifications varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

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
	test_interval_start_hour tinyint(4) DEFAULT '0' NOT NULL,
	test_interval_stop_hour tinyint(4) DEFAULT '0' NOT NULL,
	test_service varchar(255) DEFAULT '' NOT NULL,
	test_conf text NOT NULL,
	
	notifications varchar(255) DEFAULT '' NOT NULL,

	groups int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


#
# Table structure for table 'tx_caretaker_instance_testgroup_mm'
#
CREATE TABLE tx_caretaker_instance_testgroup_mm (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	sorting_foreign int(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_caretaker_instance_instancegroup_mm'
#
CREATE TABLE tx_caretaker_instance_instancegroup_mm (
	uid int(11) NOT NULL auto_increment,

	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,

	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	sorting_foreign int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_caretaker_testgroup_test_mm'
#
CREATE TABLE tx_caretaker_testgroup_test_mm (

	uid int(11) NOT NULL auto_increment,
	uid_local int(11) DEFAULT '0' NOT NULL,
	uid_foreign int(11) DEFAULT '0' NOT NULL,
	
	tablenames varchar(30) DEFAULT '' NOT NULL,
	sorting int(11) DEFAULT '0' NOT NULL,
	sorting_foreign int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'tx_caretaker_testresult'
#
CREATE TABLE tx_caretaker_testresult (
	uid int(11) NOT NULL auto_increment,
	tstamp int(11) DEFAULT '0' NOT NULL,

	test_uid int(11) DEFAULT '0' NOT NULL,
	instance_uid int(11) DEFAULT '0' NOT NULL,
	
	result_status int(11) DEFAULT '0' NOT NULL,
	result_value float DEFAULT '0' NOT NULL,
	result_msg varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY test_instance_uid (test_uid,instance_uid)
	
);

#
# Table structure for table 'tx_caretaker_aggregatorresult'
#
CREATE TABLE tx_caretaker_aggregatorresult (
	uid int(11) NOT NULL auto_increment,
	tstamp int(11) DEFAULT '0' NOT NULL,
	
	aggregator_uid int(11) DEFAULT '0' NOT NULL,
	aggregator_type varchar(255) DEFAULT '' NOT NULL,
	instance_uid int(11) DEFAULT '0' NOT NULL,
	
	result_status int(11) DEFAULT '0' NOT NULL,
	result_value float DEFAULT '0' NOT NULL,
	result_num_ok        int(11) DEFAULT '0' NOT NULL,
	result_num_warnig    int(11) DEFAULT '0' NOT NULL,
	result_num_error     int(11) DEFAULT '0' NOT NULL,
	result_num_undefined int(11) DEFAULT '0' NOT NULL,
	result_msg varchar(255) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY aggregator (aggregator_uid,aggregator_type,instance_uid)
);

