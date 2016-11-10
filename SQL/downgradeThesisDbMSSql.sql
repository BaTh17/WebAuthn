--try to undo all changes made by upgradeThesisDbMSSQL.sql script
-- Deletes Column WINDOWS_HELLO_STATUS from SETTINGS Table if it exists
-- Delete Table PUBLICKEYS if it exists
-- Delete Table PT_USER if it exists
--
-- Tested and works with SQL Server 2012
USE FIVE_KREDIWF_DEMO;


IF EXISTS(
    SELECT *
    FROM sys.columns 
    WHERE Name      = N'WINDOWS_HELLO_STATUS'
      AND Object_ID = Object_ID(N'SETTINGS'))
BEGIN
    ALTER TABLE SETTINGS DROP COLUMN WINDOWS_HELLO_STATUS;
END

if OBJECT_ID('dbo.PUBLICKEYS', 'U') IS NOT NULL DROP TABLE PUBLICKEYS;
if OBJECT_ID('dbo.PT_USER', 'U') IS NOT NULL DROP TABLE PT_USER;
