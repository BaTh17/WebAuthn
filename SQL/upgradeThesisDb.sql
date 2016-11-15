-- For MYSQL Installation, you can run this script without changes
DROP DATABASE IF EXISTS THESIS;


-- create Database THESIS
CREATE DATABASE IF NOT EXISTS THESIS;

-- TODO Create a User entry with webflow and 1234 with global rights for local testing
GRANT USAGE ON *.* TO webflow1@localhost IDENTIFIED BY '1234';
GRANT ALL PRIVILEGES ON *.* TO webflow1@localhost;


USE THESIS;


-- create WF_USER Table
CREATE TABLE IF NOT EXISTS WF_USER (
  USERID int(11) NOT NULL AUTO_INCREMENT,
  NAME varchar(40) NOT NULL,
  FULLNAME varchar(255) NOT NULL,
  USERPASSWORD varchar(255) NOT NULL,
  EMAILADDRESS text NOT NULL,
  RESET_PASSWORD int(11) NOT NULL,
  AKTIV int(11) NOT NULL,
  PRIMARY KEY (USERID)
);


-- create Policy Table
CREATE TABLE IF NOT EXISTS PT_USER (
  PTID int(11) NOT NULL AUTO_INCREMENT,
  USERID int(11) NOT NULL,
  POLICY text NOT NULL,
  TESTVALUE1 text,
  TESTVALUE2 text,
  TESTVALUE3 text,
  TESTVALUE4 text,
  TESTVALUE5 text,
  CREATEDTIME int(11)  ,
  CHANGEDTIME int(11)  ,
  AKTIV int(11),
  PRIMARY KEY (PTID)
);


-- create Key Table
CREATE TABLE IF NOT EXISTS PUBLICKEYS (
  KEYID int(11) NOT NULL AUTO_INCREMENT,
  USERID int(11) NOT NULL,
  KEYVALUE TEXT NOT NULL,
  KEYIDENTIFIER TEXT NOT NULL,
  METADATA1 text,
  METADATA2 text,
  METADATA3 text,
  HOSTNAME text,
  CREATEDTIME int(11)  ,
  CHANGEDTIME int(11)  ,
  AKTIV int(11)  ,
  PRIMARY KEY (KEYID)
);


-- create SETTINGS Table
-- STATUS 0 = Inactive, Status 1 = active
CREATE TABLE IF NOT EXISTS SETTINGS (
  WINDOWS_HELLO_STATUS int(11) DEFAULT '0'
);



INSERT INTO WF_USER ( USERID, NAME,FULLNAME,USERPASSWORD,EMAILADDRESS,RESET_PASSWORD,AKTIV ) VALUES
(1, 'tscm', 'Marcel Tschanz', '123456', 'matscha7@gmail.com', 0, -1),
(2, 'schf', 'Fabian Schwab', '123456', 'fjschwab@outlook.com', 0, -1),
(3, 'five', 'FIVE', '123456', 'matscha7@gmail.com', 0, -1),
(4, 'five1', 'FIVE_1', '123456', 'matscha7@gmail.com', 0, -1),
(5, 'test1', 'Tester_1', '123456', 'matscha7@gmail.com', 0, -1),
(6, 'hello', 'helloUser', '123456', 'matscha7@gmail.com', 0, -1);

-- 0 = Password only
-- 1 = 2-FA
-- 2 = Passwordless
INSERT INTO PT_USER ( PTID, USERID,POLICY,TESTVALUE1,TESTVALUE2,TESTVALUE3,TESTVALUE4,TESTVALUE5,CREATEDTIME,CHANGEDTIME,AKTIV ) VALUES
(1, 1, '1', 'ist Marcel Tschanz tscm mit 2-FA', NULL,NULL,NULL,NULL,now(), now(), -1),
(2, 3, '1', 'ist five ist2-FA', NULL,NULL,NULL,NULL,now(), now(), -1),
(3, 4, '2', 'ist five1 mit Passwordless', NULL,NULL,NULL,NULL,now(), now(), -1),
(4, 2, '0', 'ist schf mit password only', NULL,NULL,NULL,NULL,now(), now(), -1),
(6, 5, '2', 'ist test1 mit Passwordless', NULL,NULL,NULL,NULL,now(), now(), -1);

-- Activate Windows hello
INSERT INTO SETTINGS SET WINDOWS_HELLO_STATUS = 1;





