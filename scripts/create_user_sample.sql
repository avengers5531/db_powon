-- Create a sample user with a sample database
-- Don't run this script directly...

CREATE DATABASE SAMPLE_DB;
CREATE USER SAMPLE_USER IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON SAMPLE_DB.* TO SAMPLE_USER;
