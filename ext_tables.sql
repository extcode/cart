#
# Table structure for table 'tx_cart_domain_model_order_item'
#
CREATE TABLE tx_cart_domain_model_order_item (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    cart_pid int(11) DEFAULT '0' NOT NULL,

    fe_user int(11) unsigned DEFAULT '0',

    billing_address int(11) unsigned DEFAULT '0' NOT NULL,
    shipping_address int(11) unsigned DEFAULT '0' NOT NULL,

    order_number varchar(255) DEFAULT '' NOT NULL,
    order_date int(11) unsigned DEFAULT '0' NOT NULL,
    order_pdfs int(11) unsigned DEFAULT '0',
    invoice_number varchar(255) DEFAULT '' NOT NULL,
    invoice_date int(11) unsigned DEFAULT '0' NOT NULL,
    invoice_pdfs int(11) unsigned DEFAULT '0',
    delivery_number varchar(255) DEFAULT '' NOT NULL,
    delivery_date int(11) unsigned DEFAULT '0' NOT NULL,
    delivery_pdfs int(11) unsigned DEFAULT '0',

    currency varchar(255) DEFAULT '' NOT NULL,
    currency_code varchar(255) DEFAULT '' NOT NULL,
    currency_sign varchar(255) DEFAULT '' NOT NULL,
    currency_translation varchar(255) DEFAULT '' NOT NULL,
    gross double(11,2) DEFAULT '0.00' NOT NULL,
    net double(11,2) DEFAULT '0.00' NOT NULL,
    total_gross double(11,2) DEFAULT '0.00' NOT NULL,
    total_net double(11,2) DEFAULT '0.00' NOT NULL,

    tax int(11) unsigned DEFAULT '0' NOT NULL,
    total_tax int(11) unsigned DEFAULT '0' NOT NULL,
    tax_class int(11) unsigned DEFAULT '0' NOT NULL,
    products int(11) unsigned DEFAULT '0' NOT NULL,
    discounts int(11) unsigned DEFAULT '0' NOT NULL,
    shipping int(11) unsigned DEFAULT '0',
    payment int(11) unsigned DEFAULT '0',

    comment text,

    additional text,
    additional_data text,

    INDEX `parent` (pid), INDEX `t3ver_oid` (t3ver_oid,t3ver_wsid),
    PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_cart_domain_model_order_address'
#
CREATE TABLE tx_cart_domain_model_order_address (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    record_type varchar(255) DEFAULT '' NOT NULL,

    item int(11) unsigned DEFAULT '0' NOT NULL,

    title varchar(255) DEFAULT '' NOT NULL,
    salutation varchar(255) DEFAULT '' NOT NULL,
    first_name varchar(255) DEFAULT '' NOT NULL,
    last_name varchar(255) DEFAULT '' NOT NULL,
    email varchar(255) DEFAULT '' NOT NULL,
    phone varchar(255) DEFAULT '' NOT NULL,
    fax varchar(255) DEFAULT '' NOT NULL,
    company varchar(255) DEFAULT '' NOT NULL,
    tax_identification_number varchar(255) DEFAULT '' NOT NULL,
    street varchar(255) DEFAULT '' NOT NULL,
    street_number varchar(255) DEFAULT '' NOT NULL,
    zip varchar(255) DEFAULT '' NOT NULL,
    city varchar(255) DEFAULT '' NOT NULL,
    country varchar(255) DEFAULT '' NOT NULL,

    additional text,

    INDEX `parent` (pid), INDEX `t3ver_oid` (t3ver_oid,t3ver_wsid),
    PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_cart_domain_model_order_taxclass'
#
CREATE TABLE tx_cart_domain_model_order_taxclass (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    item int(11) unsigned DEFAULT '0' NOT NULL,

    title varchar(255) DEFAULT '' NOT NULL,
    value varchar(255) DEFAULT '' NOT NULL,
    calc double(11,2) DEFAULT '0.00' NOT NULL,

    INDEX `parent` (pid), INDEX `t3ver_oid` (t3ver_oid,t3ver_wsid),
    PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_cart_domain_model_order_tax'
#
CREATE TABLE tx_cart_domain_model_order_tax (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    item int(11) unsigned DEFAULT '0' NOT NULL,

    tax double(11,2) DEFAULT '0.00' NOT NULL,
    tax_class int(11) unsigned DEFAULT '0' NOT NULL,

    INDEX `parent` (pid), INDEX `t3ver_oid` (t3ver_oid,t3ver_wsid),
    PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_cart_domain_model_order_product'
#
CREATE TABLE tx_cart_domain_model_order_product (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    item int(11) unsigned DEFAULT '0' NOT NULL,

    product_id int(11) unsigned DEFAULT '0' NOT NULL,
    product_type varchar(255) DEFAULT '' NOT NULL,

    sku varchar(255) DEFAULT '' NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    count int(11) DEFAULT '0' NOT NULL,
    price double(11,2) DEFAULT '0.00' NOT NULL,
    discount double(11,2) DEFAULT '0.00' NOT NULL,
    gross double(11,2) DEFAULT '0.00' NOT NULL,
    net double(11,2) DEFAULT '0.00' NOT NULL,
    tax double(11,2) DEFAULT '0.00' NOT NULL,
    tax_class int(11) unsigned DEFAULT '0' NOT NULL,

    additional text,
    additional_data text,

    product_additional int(11) unsigned DEFAULT '0' NOT NULL,

    INDEX `parent` (pid), INDEX `t3ver_oid` (t3ver_oid,t3ver_wsid),
    PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_cart_domain_model_order_discount'
#
CREATE TABLE tx_cart_domain_model_order_discount (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    item int(11) unsigned DEFAULT '0' NOT NULL,

    title varchar(255) DEFAULT '' NOT NULL,
    code varchar(255) DEFAULT '' NOT NULL,
    gross double(11,2) DEFAULT '0.00' NOT NULL,
    net double(11,2) DEFAULT '0.00' NOT NULL,
    tax_class_id int(11) unsigned DEFAULT '1' NOT NULL,
    tax double(11,2) DEFAULT '0.00' NOT NULL,

    INDEX `parent` (pid), INDEX `t3ver_oid` (t3ver_oid,t3ver_wsid),
    PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_cart_domain_model_order_productadditional'
#
CREATE TABLE tx_cart_domain_model_order_productadditional (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    product int(11) unsigned DEFAULT '0' NOT NULL,

    additional_type varchar(255) DEFAULT '' NOT NULL,
    additional_key varchar(255) DEFAULT '' NOT NULL,
    additional_value varchar(255) DEFAULT '' NOT NULL,

    additional text,
    additional_data text,

    INDEX `parent` (pid), INDEX `t3ver_oid` (t3ver_oid,t3ver_wsid),
    PRIMARY KEY (uid)
);


#
# Table structure for table 'tx_cart_domain_model_order_shipping'
#
CREATE TABLE tx_cart_domain_model_order_shipping (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    item int(11) unsigned DEFAULT '0' NOT NULL,

    service_country varchar(255) DEFAULT '' NOT NULL,
    service_id int(11) DEFAULT '0' NOT NULL,
    name varchar(255) DEFAULT '' NOT NULL,
    status varchar(255) DEFAULT '0' NOT NULL,
    gross double(11,2) DEFAULT '0.00' NOT NULL,
    net double(11,2) DEFAULT '0.00' NOT NULL,
    tax double(11,2) DEFAULT '0.00' NOT NULL,
    tax_class int(11) unsigned DEFAULT '0' NOT NULL,

    note text,

    INDEX `parent` (pid), INDEX `t3ver_oid` (t3ver_oid,t3ver_wsid),
    PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_cart_domain_model_order_payment'
#
CREATE TABLE tx_cart_domain_model_order_payment (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    item int(11) unsigned DEFAULT '0' NOT NULL,

    service_country varchar(255) DEFAULT '' NOT NULL,
    service_id int(11) DEFAULT '0' NOT NULL,
    name varchar(255) DEFAULT '' NOT NULL,
    provider varchar(255) DEFAULT '' NOT NULL,
    status varchar(255) DEFAULT '0' NOT NULL,
    gross double(11,2) DEFAULT '0.00' NOT NULL,
    net double(11,2) DEFAULT '0.00' NOT NULL,
    tax double(11,2) DEFAULT '0.00' NOT NULL,
    tax_class int(11) unsigned DEFAULT '0' NOT NULL,

    note text,

    transactions int(11) unsigned DEFAULT '0' NOT NULL,

    INDEX `parent` (pid), INDEX `t3ver_oid` (t3ver_oid,t3ver_wsid),
    PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_cart_domain_model_order_transaction'
#
CREATE TABLE tx_cart_domain_model_order_transaction (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    payment int(11) unsigned DEFAULT '0' NOT NULL,

    provider varchar(20) DEFAULT ''  NOT NULL,
    txn_id varchar(255) DEFAULT ''  NOT NULL,
    txn_txt text,
    status varchar(255) DEFAULT 'unknown' NOT NULL,
    external_status_code varchar(255) DEFAULT '' NOT NULL,
    note text,

    INDEX `parent` (pid),
    PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_cart_domain_model_coupon'
#
CREATE TABLE tx_cart_domain_model_coupon (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    title varchar(255) DEFAULT '' NOT NULL,
    code varchar(255) DEFAULT '' NOT NULL,
    coupon_type varchar(255) DEFAULT 'cartdiscount' NOT NULL,
    discount double(11,2) DEFAULT '0.00' NOT NULL,
    tax_class_id int(11) unsigned DEFAULT '1' NOT NULL,
    cart_min_price double(11,2) DEFAULT '0.00' NOT NULL,
    is_combinable tinyint(4) unsigned DEFAULT '0' NOT NULL,
    is_relative_discount tinyint(4) unsigned DEFAULT '0' NOT NULL,
    handle_available tinyint(4) unsigned DEFAULT '0' NOT NULL,
    number_available int(11) DEFAULT '0' NOT NULL,
    number_used int(11) DEFAULT '0' NOT NULL,

    INDEX `parent` (pid), INDEX `t3ver_oid` (t3ver_oid,t3ver_wsid),
    PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_cart_domain_model_cart'
#
CREATE TABLE tx_cart_domain_model_cart (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    f_hash varchar(255) DEFAULT '' NOT NULL,
    s_hash varchar(255) DEFAULT '' NOT NULL,

    fe_user int(11) DEFAULT '0' NOT NULL,

    order_item int(11) unsigned DEFAULT '0' NOT NULL,

    cart text,

    was_ordered tinyint(4) unsigned DEFAULT '0' NOT NULL,

    INDEX `parent` (pid),
    PRIMARY KEY (uid)
);

#
# Table structure for table 'tx_cart_domain_model_tag'
#
CREATE TABLE tx_cart_domain_model_tag (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    title varchar(255) DEFAULT '' NOT NULL,

    KEY language (l10n_parent,sys_language_uid),

    INDEX `parent` (pid), INDEX `t3ver_oid` (t3ver_oid,t3ver_wsid),
    PRIMARY KEY (uid)
);
