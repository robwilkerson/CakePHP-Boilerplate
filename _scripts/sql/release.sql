/**
 * Installs the project database, including supporting scripts 
 *  - Cake's session.sql DDL
 *  - AuditLog plugin SQL
 */

DROP DATABASE IF EXISTS cakephp_boilerplate;

CREATE DATABASE cakephp_boilerplate
  DEFAULT CHARACTER SET 'utf8'
  DEFAULT COLLATE 'utf8_unicode_ci';

-- GRANT ALL ON @DB_NAME@.* to @DB_NAME@_@DB_USERNAME@ IDENTIFIED BY '@DB_PASSWORD@';
-- GRANT ALL ON @DB_NAME@.* to @DB_NAME@_@DB_USERNAME@@localhost IDENTIFIED BY '@DB_PASSWORD@';
 
USE cakephp_boilerplate;

SET NAMES utf8;
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS cake_sessions;
SOURCE ../../cake/console/templates/skel/config/schema/sessions.sql;

DROP TABLE IF EXISTS audits;
DROP TABLE IF EXISTS audit_deltas;
SOURCE ../../app/plugins/audit_log/install.sql;

DROP TABLE IF EXISTS users;
CREATE TABLE users(
  id              char(36)        NOT NULL,
  first_name      varchar(255)    NOT NULL,
  last_name       varchar(255)    NOT NULL,
  email           varchar(255)    NOT NULL,
  password        varchar(255)    NULL,
  active          boolean         NOT NULL DEFAULT 1,
  last_login      datetime        NULL,
  created         datetime        NOT NULL,
  modified        datetime        NOT NULL,
  
  PRIMARY KEY( id ),
  CONSTRAINT uix__email UNIQUE INDEX( email )
) ENGINE=InnoDB;

SET foreign_key_checks = 1;
