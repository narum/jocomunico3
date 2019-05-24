Jocomunico version 2.1.

Jocomunico by Joan Pahisa is licensed under a Creative Commons Attribution-NonCommercial 4.0 International License.

The latest running version of Jocomunico can be found at:

https://jocomunico.com

For more information on Jocomunico:

https://jocomunico.com/#/cc
https://jocomunico.com/#/about

*Jocomunico is optimized for Google Chrome.

-----------------------
KNOWN BUGS
-----------------------

- Uploading a backup made on version 2.0 to a version 2.1 Jocomunico. To fix, please update your Jocomunico to version 2.1, redo de backup and upload it again.

-----------------------
README FOR INSTALLATION
-----------------------

Instructions for installation on WINDOWS and on MACOSX systems can be found here (English version available through 'Idioma' option on the top left menu):

https://jocomunico.com/#/download


A. LINUX SYSTEMS

General steps:

1. Install an Apache server with MySQL, such as, XAMPP. PHP and MySQL version requirements are detailed in the B section of this README file.

2. Extract all the contents of the master branch on GitHub (https://github.com/narum/jocomunico2/) to your desired folder.

3. Install the database found in /ddbb/jocomunicoapp-database.sql. 

4. Install a voice synthetizer engine, such as Festival, or use an existing online TTS service (below there are details on what should be changed in order to link it with the application).

Adjustments in the code:

1. File: /application/config/config.php - By default the project's base URL is localhost. Change it if you want it to be a different one.

2. File: .htaccess - In case the chosen base URL is not localhost, you also need to change it on the .htaccess file, as it controls the Apache server's URL redirections. 

3. File: /application/config/database.php - Specify the configuration details to connect to the previously created MySQL database.

4. (Optional) File: index.php - Switch the ENVIRONMENT variable into 'development' for easier debugging. Once you want to deploy your project, change it back to 'production'.

5. File: /application/libraries/MyAudio.php - This file controls the online voice synthetizers (on the online version of the application) and/or the system's voice synthetizer (on the local version). There is only code to access Windows and MacOSX system voices, so, for Linux, functions to call the voices installed in the chosen synthetizer and to save its output as files in the /mp3 folder need to be coded. Finally, another function in order to list the available system voices also need to be coded.

	a. The function to call the synthetizer and generate the audio files needs to be added as an option inside the 'switch' that begins on line 540 of the function 'sytnhesizeAudio'.
	
	b. The function to list the available voices needs to be added as an option inside the 'switch' that begins on line 94 of the function 'getLocalVoices'. For Windows and MacOSX versions, the chosen voice is stored as a string on the field 'cfgExpansionVoiceOffline' of the 'User' table in the database. Whether the voice is masculine or feminine is stored in 'cfgInterfaceVoiceMascFem' with values set to 'masc' or 'fem'. Depending on the chosen system's voice synthetizer, more fields may need to be added to the table and later retrieved in the code.
	
	c. If you want to use an online TTS API other than Vocalware, it can be configured overriding the 'synthesizeOnline' function using the API's call methodology. If you have a Vocalware API key, it can be set on lines 616 and 617.


B. SERVER AND DATABASE REQUIREMENTS

B.1. PHP VERSION

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

B.2. MYSQL VERSION

Tested with MySQL 5.5. 

*On MySQL 5.7+ sql_mode=only_full_group_by needs to be deactivated. For MAMP 5.2 in MacOSX the my.cnf file (that needs to be copied at the root folder of MAMP application) to overwrite sql_mode is in /ddbb folder.
