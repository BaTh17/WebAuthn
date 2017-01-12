--try to undo all changes made by upgradeThesisDbMSSQL.sql script
-- Deletes Column WINDOWS_HELLO_STATUS from SETTINGS Table if it exists
-- Delete Table PUBLICKEYS if it exists
-- Delete Table PT_USER if it exists
-- Some of the users are already part of the webflow users, so the downgrade script doesn't delete them (dependencies with tasks)
--
-- Tested and works with SQL Server 2012
USE FIVE_KREDIWF_DEMO;


IF EXISTS(
    SELECT *
    FROM sys.columns 
    WHERE Name      = N'WINDOWS_HELLO_STATUS'
      AND Object_ID = Object_ID(N'SETTINGS'))
BEGIN
    /* On Error: use the constraint name from the error message in the drop constraint statement 1 line below and uncomment it, rerun code*/
    /*ALTER TABLE SETTINGS DROP CONSTRAINT DF__SETTINGS__WINDOW__110B679F;*/
    ALTER TABLE SETTINGS DROP COLUMN WINDOWS_HELLO_STATUS;
END

if OBJECT_ID('dbo.PUBLICKEYS', 'U') IS NOT NULL DROP TABLE PUBLICKEYS;
if OBJECT_ID('dbo.PT_USER', 'U') IS NOT NULL DROP TABLE PT_USER;
