# info2180-project2
Test for project 2

Copy this repository to htdocs in xampp folder like lab 5.
Run generate_hash in your terminal (php generate_hash). In the code, the admin password for login is set to "password123" . When the code returns the hashed password (very long string) open Schema.sql and paste the hashed password. I have already done all of this but these are the steps if you wish to do it yourself.


Make sure xampp apache and mysql are running (in config.php i have port set to 3307 due to a clash but the default is 3306) and head to phpmyadmin (from lab 5) and import "Schema.sql".

If imported correctly dolphin_crm should show on the database sidebar and only 1 user, the admin account, should be there. Next we head to localhost/info2180-project2/index.html which should load a login page. Email: admin@project2.com and Password: password123 should log you in to the site. If it shows database connection error check that the info  in config.php matches with your database config in xampp. Test the features available !

