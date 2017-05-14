README Linux :
	-Installer php7
	-Installer httpd
	-Installer postgresql
	-Installer le module postgresql pour php7
	-Installer le module gd pour php7

	-Créer une base de données postgresql avec l'utilisateur postgres. Le nom de la base doit être postgres (normalement par défaut). Executé le script BD.sql

	-Dans le fichier de configuration de httpd (/etc/httpd/conf/httpd.conf) ou de apache :
	-Vérifier le chemin du serveur web (ligne : DocumentRoot) et mettre en lien symbolique le dossier web à cette emplacement : ln -s web /srv/http si le chemin du serveur web de httpd est /srv/http

	-Dans le fichier de configuration de php (/etc/php/php.ini) :
		-Activer gd.so
		-Activer pgsql.so

	-Donner les droits d'executions à TOUS les fichiers
		-chmod +x -R <chemin vers le répository>
