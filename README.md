Jocomunico version 2.1.

README FOR INSTALLATION

SERVER AND DATABASE REQUIREMENTS

PHP VERSION

Tested from PHP 5.6 up to 7.3.

*Depending on the server settings, on PHP 7.x+ there may be an issue with 
CodeIgniter's Session Library. To see if this problem exists, run the application,
create a user, log in to it, go to 'Mis paneles' on the menu and try to switch
On and Off the toggle next to 'Historial'. If Folders 'Hoy', 'Semana pasada' and
'Mes pasado' don't appear and disappear, the problem is there.

If so, to solve it follow these steps:

1. Replace index.php from root directory with tne index.php file in /alternative folder.

2. Replace Sessions.php from /system/libraries/Session with tne Session.php file in /alternative folder.

3. Resfresh the browser where the application is running (Ctrl+R).

MYSQL VERSION

Tested with MySQL 5.5. 

*On MySQL 5.7+ sql_mode=only_full_group_by needs to be deactivated. For MAMP 5.2 in MacOSX the my.cnf file (that needs to be copied at the root folder of MAMP application) to overwrite sql_mode is in /ddbb folder.