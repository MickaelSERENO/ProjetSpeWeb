README Windows :

-Pour lancer le site :
	- Installer wamp : https://sourceforge.net/projects/wampserver/
	- Au préalable, vérifier que la connexion au serveur local est établie sur le bon port : (notre projet a été testé avec le port 8080).
	  Vérifier dans le fichier de configuration de httpd (C:\wamp\bin\apache\apache2.4.23\conf\httpd.conf) que le port écouté est correct :
	  Listen 127.0.0.1:8080
	  Listen [::1]:8080 #Pour la compatibilité avec le protocole d'IPv6
	  NameVirtualHost 127.0.0.1:8080 #Pour établir la connexion sur les virtualhosts si l'option n'est pas activitée par défaut.
	  Donner tous les accès aux répertoires (s'il est impossible d'y accéder, en dernier recours) :
	  <Directory "${INSTALL_DIR}/cgi-bin">
	   AllowOverride None
       Options None
       Require all granted
	  </Directory>
	  Décommenter la ligne suivante pour lire les virtualhHosts :
	  Include conf/extra/httpd-vhosts.conf
	  Dans le fichier de configuration des virtualHosts (C:\wamp6\bin\apache\apache2.4.23\conf\extra\httpd-vhosts.conf), rediriger l'hôte souhaité sur le bon port:
	  <VirtualHost *:8080>
	  ServerName nomDuVirtualHost
	  DocumentRoot c:/wamp/www
	  <Directory  "c:/wamp/www/">
		Options +Indexes +Includes +FollowSymLinks +MultiViews
		AllowOverride All
		Require local
	  </Directory>
      </VirtualHost>
	  Vérifier de même que le virtualHost est configuré parmi les hôtes du système dans C:\wamp64\bin\apache\apache2.4.23\conf\extra\hosts
	  Sinon y ajouter les lignes suivantes :
	  127.0.0.1 localhost
	  ::1 localhost # protocole IPv6
	- Lancer wamp et démarrer les services. Si apache ne veut pas se lancer, il y a peut-être un problème de ports.
	- Cliquer sur Wamp, puis dans la liste des virtualHosts, cliquer sur localhost.
	- Créer un virtualHost en indiquant le chemin vers le dossier "web" du projet.
	- Redémarrer Wamp, et cliquer sur le virtualHost nouvellement créé.
	- Si l'option n'est pas activée : actualiser la configuration du DNS (clic-droit sur Wamp -> Outils -> Redémarrage DNS ou bien dans l'invite de commandes en Administrateur -> net stop dnscache puis net start dnscache).
	- Une page s'ouvre. Ajouter à son url "/Accueil/Accueil.php" pour accéder au site.


- Pour lancer le premier jeu :
	- Installer postgres SQL : https://sourceforge.net/projects/wampserver/
	-Créer une base de données postgresql avec l'utilisateur postgres. Le nom de la base doit être postgres (normalement par défaut). Executé le script BD.sql

	
- Pour lancer le deuxième jeu :
	Il faudra peut-être vider le fichier web/ClientQuery/parties.txt, qui contient la liste des parties en cours. Malheureusement, à l'heure actuelle, ce fichier se remplie mais ne se vide pas quand une partie se termine ou quand un joueur quitte le jeu.