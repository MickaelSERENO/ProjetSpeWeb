README Windows :

-Pour lancer le site :
	- Installer wamp : https://sourceforge.net/projects/wampserver/
	- Lancer wamp et démarrer les services. Si apache ne veut pas se lancer, il y a peut-être un problème de ports.
	- Cliquer sur Wamp, puis dans la liste des virtualHosts, cliquer sur localhost.
	- Créer un virtualHost en indiquant le chemin vers le dossier "web" du projet.
	- Redémarrer Wamp, et cliquer sur le virtualHost nouvellement créé.
	- Une page s'ouvre. Ajouter à son url "/Accueil/Accueil.php" pour accéder au site.


- Pour lancer le premier jeu :
	- Installer postgres SQL : https://sourceforge.net/projects/wampserver/
	-Créer une base de données postgresql avec l'utilisateur postgres. Le nom de la base doit être postgres (normalement par défaut). Executé le script BD.sql

	
- Pour lancer le deuxième jeu :
	Il faudra peut-être vider le fichier web/ClientQuery/parties.txt, qui contient la liste des parties en cours. Malheureusement, à l'heure actuelle, ce fichier se remplie mais ne se vide pas quand une partie se termine ou quand un joueur quitte le jeu.