-- For MYSQL Installation, you can run this script without changes
-- For MSSQL, replace "--GO" with "GO" to execute the lines before going to the next code block

-- create Database THESIS
CREATE DATABASE IF NOT EXISTS THESIS;
--GO

USE THESIS;
--GO

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
--GO

-- create Policy Table
CREATE TABLE IF NOT EXISTS PT_USER (
  USERID int(11) NOT NULL,
  UID int(11) NOT NULL,
  POLICY text NOT NULL,
  TESTVALUE1 text,
  TESTVALUE2 text,
  TESTVALUE3 text,
  TESTVALUE4 text,
  TESTVALUE5 text,
  CREATED int(11)  ,
  CHANGED int(11)  ,
  AKTIV int(11)
);
--GO

-- create Key Table
CREATE TABLE IF NOT EXISTS KEYS (
  KEYID int(11) NOT NULL AUTO_INCREMENT,
  UID int(11) NOT NULL,
  KEY TEXT NOT NULL,
  METADATA1 text,
  METADATA2 text,
  METADATA3 text,
  HOSTNAME text,
  CREATED int(11)  ,
  CHANGED int(11)  ,
  AKTIV int(11)  ,
  PRIMARY KEY (POLICYID)
);
--GO

-- create SETTINGS Table
-- STATUS 0 = Inactive, Status 1 = active
CREATE TABLE IF NOT EXISTS SETTINGS (
  WINDOWS_HELLO_STATUS int(11) DEFAULT '0'
);
--GO


---
---Insert Content
---
INSERT INTO WF_USER ( USERID, NAME,FULLNAME,USERPASSWORD,EMAILADDRESS,RESET_PASSWORD,AKTIV ) VALUES
(1, 'tscm', 'Marcel Tschanz', '1234', 'matscha7@gmail.com', 0, -1),
(2, 'schf', 'Fabian Schwab', '1234', 'fjschwab@outlook.com', 0, -1),
(3, 'five', 'FIVE', '1234', 'matscha7@gmail.com', 0, -1),
(4, 'five1', 'FIVE 1', '1234', 'matscha7@gmail.com', 0, -1),
(5, 'test1', 'Tester 1', '1234', 'matscha7@gmail.com', 0, -1);

/*
INSERT INTO PT_USER ( USERID, NAME,FULLNAME,USERPASSWORD,EMAILADDRESS,RESET_PASSWORD,AKTIV ) VALUES
(1, 'tscm', 'Marcel Tschanz', '1234', 'matscha7@gmail.com', 0, -1),
(2, 'schf', 'Fabian Schwab', '1234', 'fjschwab@outlook.com', 0, -1),
(3, 'five', 'FIVE', '1234', 'matscha7@gmail.com', 0, -1),
(4, 'five1', 'FIVE 1', '1234', 'matscha7@gmail.com', 0, -1),
(5, 'test1', 'Tester 1', '1234', 'matscha7@gmail.com', 0, -1);
*/

-- Insert User
/*
IF NOT EXISTS ( SELECT 1 
                  FROM ENUM 
                 WHERE ENUMGRUPPEID = 1 
                   AND ENUMID = 2 
              ) 
BEGIN 
    INSERT INTO ENUM 
            (   ENUMGRUPPEID, 
                ENUMID, 
                ENUMBEZEICHNUNG, 
                ENUMKURZ 
            ) 
     VALUES (  1, 
               2, 
               'Lagermaterial', 
               'LA' 
            ) 
     ; 
END;
*/

--GO
-- Activate Windows hello
INSERT INTO SETTINGS SET WINDOWS_HELLO_STATUS = 1;
--GO




