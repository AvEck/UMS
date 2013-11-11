UMS
===

A Simple Login and User Management System

Index.php - This file contains the simple login script it tries to authenticate the user and give him passage into the application<br />
Profile.php - The 'main' page of every account, the page determines whether the user is ADMIN and adjusts accordingly<br />
Register.php - The register page is used to create new users, it is reused when an admin wants to add a new user<br />
Logout.php - A small script which logs the user out of his session.<br />
Forgot.php - The user is able to send himself a new password link with this page<br />
Functions.php - Holds 3 functions, 2 to calculate and to store the action used and 1 to verify the login of the user.<br />
<br />
UserManagement.php - Everything that has anything to do with User Management is stored in this class. <br />
Methods within the class can be used from all over the app to modify/delete/verify or resend password links<br />
<br />
DBtemplate.sql - Contains the MySQL structure exported from PHPmyAdmin. 1 admin account has been set up with the credentials:
pass: Alexander user: alexander
