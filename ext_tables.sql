#
# Add field for table "sys_language"
#
CREATE TABLE sys_language (
	static_lang_isocode smallint(6) DEFAULT '0' NOT NULL
);

#
# Table structure for table "static_countries"
#
CREATE TABLE static_countries (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	deleted smallint(6) DEFAULT '0' NOT NULL,
	cn_iso_2 varchar(2) DEFAULT '' NOT NULL,
	cn_iso_3 varchar(3) DEFAULT '' NOT NULL,
	cn_iso_nr int(11) DEFAULT '0' NOT NULL,
	cn_parent_territory_uid int(11) DEFAULT '0' NOT NULL,
	cn_parent_tr_iso_nr int(11) DEFAULT '0' NOT NULL,
	cn_official_name_local varchar(128) DEFAULT '' NOT NULL,
	cn_official_name_en varchar(128) DEFAULT '' NOT NULL,
	cn_capital varchar(45) DEFAULT '' NOT NULL,
	cn_tldomain varchar(2) DEFAULT '' NOT NULL,
	cn_currency_uid int(11) DEFAULT '0' NOT NULL,
	cn_currency_iso_3 varchar(3) DEFAULT '' NOT NULL,
	cn_currency_iso_nr int(11) DEFAULT '0' NOT NULL,
	cn_phone int(11) DEFAULT '0' NOT NULL,
	cn_eu_member smallint(6) DEFAULT '0' NOT NULL,
	cn_uno_member smallint(6) DEFAULT '0' NOT NULL,
	cn_address_format smallint(6) DEFAULT '0' NOT NULL,
	cn_zone_flag smallint(6) DEFAULT '0' NOT NULL,
	cn_short_local varchar(70) DEFAULT '' NOT NULL,
	cn_short_en varchar(50) DEFAULT '' NOT NULL,
	cn_country_zones int(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid)
);

#
# Table structure for table "static_country_zones"
#
CREATE TABLE static_country_zones (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	deleted smallint(6) DEFAULT '0' NOT NULL,
	zn_country_iso_2 varchar(2) DEFAULT '' NOT NULL,
	zn_country_iso_3 varchar(3) DEFAULT '' NOT NULL,
	zn_country_iso_nr int(11) DEFAULT '0' NOT NULL,
	zn_code varchar(45) DEFAULT '' NOT NULL,
	zn_name_local varchar(128) DEFAULT '' NOT NULL,
	zn_name_en varchar(50) DEFAULT '' NOT NULL,
	zn_country_uid int(11) DEFAULT '0' NOT NULL,
	zn_country_table tinytext,
	PRIMARY KEY (uid)
);

#
# Table structure for table "static_currencies"
#
CREATE TABLE static_currencies (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	deleted smallint(6) DEFAULT '0' NOT NULL,
	cu_iso_3 varchar(3) DEFAULT '' NOT NULL,
	cu_iso_nr int(11) DEFAULT '0' NOT NULL,
	cu_name_en varchar(50) DEFAULT '' NOT NULL,
	cu_symbol_left varchar(12) DEFAULT '' NOT NULL,
	cu_symbol_right varchar(12) DEFAULT '' NOT NULL,
	cu_thousands_point varchar(1) DEFAULT '' NOT NULL,
	cu_decimal_point varchar(1) DEFAULT '' NOT NULL,
	cu_decimal_digits smallint(6) DEFAULT '0' NOT NULL,
	cu_sub_name_en varchar(20) DEFAULT '' NOT NULL,
	cu_sub_divisor int(11) DEFAULT '1' NOT NULL,
	cu_sub_symbol_left varchar(12) DEFAULT '' NOT NULL,
	cu_sub_symbol_right varchar(12) DEFAULT '' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table "static_languages"
#
CREATE TABLE static_languages (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
  	deleted smallint(6) DEFAULT '0' NOT NULL,
	lg_iso_2 varchar(2) DEFAULT '' NOT NULL,
	lg_name_local varchar(99) DEFAULT '' NOT NULL,
	lg_name_en varchar(50) DEFAULT '' NOT NULL,
	lg_typo3 varchar(2) DEFAULT '' NOT NULL,
	lg_country_iso_2 varchar(2) DEFAULT '' NOT NULL,
	lg_collate_locale varchar(5) DEFAULT '' NOT NULL,
	lg_sacred smallint(6) DEFAULT '0' NOT NULL,
	lg_constructed smallint(6) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table "static_territories"
#
CREATE TABLE static_territories (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	deleted smallint(6) DEFAULT '0' NOT NULL,
	tr_iso_nr int(11) DEFAULT '0' NOT NULL,
	tr_parent_territory_uid int(11) DEFAULT '0' NOT NULL,
	tr_parent_iso_nr int(11) DEFAULT '0' NOT NULL,
	tr_name_en varchar(50) DEFAULT '' NOT NULL,
	PRIMARY KEY (uid)
);
