<?php

/**
 * Install core configuration table.
 *
 * @package   Seamrog
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 * @version   0.2.0
 */

/**
 * @var $wpdb wpdb
 */

$table = Rootd_Config::TABLE_NAME;

$wpdb->query("
    #################################################################
    # Table: Core Configuration                                     #
    #################################################################

    CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}{$table}` (
        `config_id` INT NOT NULL AUTO_INCREMENT,
        `path`      VARCHAR(255),
        `value`     TEXT,
        PRIMARY KEY (`config_id`),
        CONSTRAINT `uc_config_path` UNIQUE (`path`),
        INDEX (`config_id`, `path`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci;
");