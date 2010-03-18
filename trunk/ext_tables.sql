

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
	description text NOT NULL,
	parent_group int(11) DEFAULT '0' NOT NULL,

	contacts int(11) DEFAULT '0' NOT NULL,
	notification_strategies int(11) DEFAULT '0' NOT NULL,

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
	description text NOT NULL,
	public_key text NOT NULL,
	url varchar(255) DEFAULT '' NOT NULL,
	host varchar(255) DEFAULT '' NOT NULL,

	groups text NOT NULL,
	tests text NOT NULL,
	instancegroup int(11) DEFAULT '0' NOT NULL,

	contacts int(11) DEFAULT '0' NOT NULL,
	notification_strategies int(11) DEFAULT '0' NOT NULL,

	testconfigurations text NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


CREATE TABLE tx_caretaker_node_strategy_mm (
	uid int(11) DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	uid_node int(11) DEFAULT '0' NOT NULL,
	uid_strategy int(11) DEFAULT '0' NOT NULL,
	node_table varchar(30) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),

	KEY uid_local (uid_node),
	KEY uid_foreign (uid_strategy)
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
	description text NOT NULL,
	parent_group int(11) DEFAULT '0' NOT NULL,
	instances int(11) DEFAULT '0' NOT NULL,

	tests blob NOT NULL,

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
	description text NOT NULL,

	test_interval int(11) DEFAULT '0' NOT NULL,
	test_interval_start_hour tinyint(4) DEFAULT '0' NOT NULL,
	test_interval_stop_hour tinyint(4) DEFAULT '0' NOT NULL,
	test_service varchar(255) DEFAULT '' NOT NULL,
	test_conf text NOT NULL,
	test_retry int(11) DEFAULT '0' NOT NULL,

	groups int(11) DEFAULT '0' NOT NULL,
	instances int(11) DEFAULT '0' NOT NULL,

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
# Table structure for table 'tx_caretaker_instance_test_mm'
#
CREATE TABLE tx_caretaker_instance_test_mm (

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
	result_msg text NOT NULL,
	result_values text NOT NULL,
	result_submessages text NOT NULL,

	PRIMARY KEY (uid),
	KEY test_instance_uid (test_uid,instance_uid)
	KEY test_instance_uid_tstamp (tstamp,test_uid,instance_uid)
	
);

#
# Table structure for table 'tx_caretaker_testresult'
#
CREATE TABLE tx_caretaker_lasttestresult (
	uid int(11) NOT NULL auto_increment,
	tstamp int(11) DEFAULT '0' NOT NULL,

	test_uid int(11) DEFAULT '0' NOT NULL,
	instance_uid int(11) DEFAULT '0' NOT NULL,

	result_status int(11) DEFAULT '0' NOT NULL,
	result_value float DEFAULT '0' NOT NULL,
	result_msg text NOT NULL,
	result_values text NOT NULL,
	result_submessages text NOT NULL,

	PRIMARY KEY (uid),
	KEY test_instance_uid (test_uid,instance_uid)
	KEY test_instance_uid_tstamp (tstamp,test_uid,instance_uid)

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
	result_num_undefined int(11) DEFAULT '0' NOT NULL,
	result_num_ok        int(11) DEFAULT '0' NOT NULL,
	result_num_warnig    int(11) DEFAULT '0' NOT NULL,
	result_num_error     int(11) DEFAULT '0' NOT NULL,

	result_msg varchar(255) DEFAULT '' NOT NULL,
	result_values text NOT NULL,
    result_submessages text NOT NULL,

	PRIMARY KEY (uid),
	KEY aggregator (aggregator_uid,aggregator_type,instance_uid)
);

#
# Table structure for table 'tx_caretaker_roles'
#
CREATE TABLE tx_caretaker_roles (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	id varchar(30) DEFAULT '' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_caretaker_node_address_mm'
#
CREATE TABLE tx_caretaker_node_address_mm (
	uid int(11) DEFAULT '0' NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	uid_node int(11) DEFAULT '0' NOT NULL,
	uid_address int(11) DEFAULT '0' NOT NULL,
	role int(11) DEFAULT '0' NOT NULL,
	node_table varchar(30) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),

	KEY uid_local (uid_node),
	KEY uid_foreign (uid_address)
);

#
# Table structure for table 'tx_caretaker_exitpoints'
#
CREATE TABLE tx_caretaker_exitpoints (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	id varchar(30) DEFAULT '' NOT NULL,
	name varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	service varchar(255) DEFAULT '' NOT NULL,
	config text NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_caretaker_strategies'
#
CREATE TABLE tx_caretaker_strategies (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,

	name varchar(50) DEFAULT '' NOT NULL,
	description text NOT NULL,
	config text NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);