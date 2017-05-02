/*Delete the schema and the table*/
DROP SCHEMA public CASCADE;
CREATE SCHEMA public;

/*Utilisateur*/
CREATE TABLE Eleve(
id SERIAL,
pseudo CHAR(50) UNIQUE,
nom CHAR(50),
prenom CHAR(50),
password CHAR(60),
nbGame1 INTEGER,
nbGame2 INTEGER,
PRIMARY KEY(id)
);

CREATE TABLE Classe(
nom CHAR(50),
mail CHAR(150),
password CHAR(60),
verifiedUser BOOLEAN,
code CHAR(15),
id SERIAL,
PRIMARY KEY(mail)
);

CREATE TABLE EleveClasse(
idEleve integer,
mailClasse CHAR(150),
FOREIGN KEY(idEleve) REFERENCES Eleve(id),
FOREIGN KEY(mailClasse) REFERENCES Classe(mail)
);

/*Phrases*/
CREATE TABLE Phrase(
id SERIAL,
PRIMARY KEY(id)
);

CREATE TABLE GroupeMots(
id SERIAL,
idPhrase integer,
texte CHAR(256),
PRIMARY KEY(id),
FOREIGN KEY(idPhrase) REFERENCES Phrase(id)
);

CREATE TABLE PairePhrases(
idPhrase1 integer,
idPhrase2 integer,
idPaire SERIAL,
PRIMARY KEY(idPaire),
FOREIGN KEY(idPhrase1) REFERENCES Phrase(id),
FOREIGN KEY(idPhrase2) REFERENCES Phrase(id)
);

CREATE TYPE OP AS ENUM ('synonyme', 'antonyme', 'specialisation', 'generalisation');

CREATE TABLE AssociationMots(
idGroupeMots1 integer,
idGroupeMots2 integer,
relation OP,
FOREIGN KEY(idGroupeMots1) REFERENCES GroupeMots(id),
FOREIGN KEY(idGroupeMots2) REFERENCES GroupeMots(id)
);

CREATE TABLE PackPaires(
id SERIAL,
nom CHAR(30),
mailClasse CHAR(150),
note integer,
telechargement integer,
PRIMARY KEY(id),
FOREIGN KEY(mailClasse) REFERENCES Classe(mail)
);

CREATE TABLE PackPairesPairePhrases(
idPack integer,
idPaire integer,
FOREIGN KEY(idPaire) REFERENCES PairePhrases(idPaire),
FOREIGN KEY(idPack)  REFERENCES PackPaires(id)
);

/*Statistiques */
CREATE TYPE GAME AS ENUM ('Game1', 'Game2');

CREATE TABLE Historique(
idHisto SERIAL,
idGame GAME, /*Game 1 or Game 2 ?*/
jour TIMESTAMP,
PRIMARY KEY(idHisto)
);

CREATE TABLE EleveHistoG1(
idGame1 SERIAL,
idEleve integer,
idHisto integer,
idPack integer,
idPairePhrase integer,
PRIMARY KEY(idGame1),
FOREIGN KEY (idEleve) REFERENCES Eleve(id),
FOREIGN KEY(idHisto) REFERENCES Historique(idHisto),
FOREIGN KEY(idPack)  REFERENCES PackPaires(id),
FOREIGN KEY(idPairePhrase) REFERENCES PairePhrases(idPaire)
);

CREATE TABLE ClasseHistoG2(
idGame2 SERIAL,
idHisto integer,
mailProf CHAR(50),
PRIMARY KEY(idGame2),
FOREIGN KEY(idHisto) REFERENCES Historique(idHisto),
FOREIGN KEY(mailProf) REFERENCES Classe(mail)
);

CREATE TABLE EleveResultG1(
idGame1 integer,
idWord1 integer, /*The id of the word sentence 1*/
idWord2 integer, /*The id of the word sentence 2*/
operation OP,    /*The operation between these words*/
FOREIGN KEY (idGame1) REFERENCES EleveHistoG1(idGame1),
FOREIGN KEY (idWord1) REFERENCES GroupeMots(id),
FOREIGN KEY (idWord2) REFERENCES GroupeMots(id)
);

CREATE TABLE PhraseInventee(
id SERIAL,
PRIMARY KEY(id)
);

CREATE TABLE PhrasePhraseInventee(
idPhrase integer,
idPhraseInventee integer,
FOREIGN KEY(idPhrase) REFERENCES Phrase(id),
FOREIGN KEY(idPhraseInventee) REFERENCES PhraseInventee(id)
);

/*On remplie la table pour pouvoir faire nos tests*/
INSERT INTO Eleve       VALUES (DEFAULT, 'User', 'UserName', 'UserFirstName', '', 0, 0);
INSERT INTO Classe      VALUES ('Teacher', 'prof@scolaire.fr', '');
INSERT INTO EleveClasse VALUES ('1', 'prof@scolaire.fr');

/*On créé notre pack*/
INSERT INTO PackPaires VALUES (DEFAULT, 'default pack', 'prof@scolaire.fr');
INSERT INTO PackPaires VALUES (DEFAULT, 'default pack2', 'prof@scolaire.fr');

/*Paire phrase 1, peut aussi servir d'exemple sur comment faire*/
INSERT INTO Phrase      VALUES (DEFAULT);
INSERT INTO GroupeMots  VALUES (DEFAULT, 1, 'Bonjour'); /*Psql peut nous permettre de trouver quelle est la valeur maximal courant des clé SERIAL*/
INSERT INTO GroupeMots  VALUES (DEFAULT, 1, 'ce matin');
INSERT INTO GroupeMots  VALUES (DEFAULT, 1, 'je déjeunais');
INSERT INTO Phrase      VALUES (DEFAULT);
INSERT INTO GroupeMots  VALUES (DEFAULT, 2, 'Coucou');
INSERT INTO GroupeMots  VALUES (DEFAULT, 2, 'à l''aube');
INSERT INTO GroupeMots  VALUES (DEFAULT, 2, 'je mangeais');
INSERT INTO AssociationMots VALUES (1, 4, 'synonyme'); 
INSERT INTO AssociationMots VALUES (2, 5, 'synonyme');
INSERT INTO AssociationMots VALUES (3, 6, 'synonyme');
INSERT INTO PairePhrases VALUES (1, 2);

/*Paire phrase 2*/
INSERT INTO Phrase      VALUES (DEFAULT);
INSERT INTO GroupeMots  VALUES (DEFAULT, 3, 'Le professeur');
INSERT INTO GroupeMots  VALUES (DEFAULT, 3, 'cassait');
INSERT INTO GroupeMots  VALUES (DEFAULT, 3, 'des briques');
INSERT INTO Phrase      VALUES (DEFAULT);
INSERT INTO GroupeMots  VALUES (DEFAULT, 4, 'Un solide');
INSERT INTO GroupeMots  VALUES (DEFAULT, 4, 'a été cassé');
INSERT INTO GroupeMots  VALUES (DEFAULT, 4, 'par le maître');
INSERT INTO AssociationMots VALUES (7, 10, 'synonyme');
INSERT INTO AssociationMots VALUES (8, 11, 'synonyme');
INSERT INTO AssociationMots VALUES (9, 12, 'synonyme');
INSERT INTO PairePhrases VALUES (3, 4);

/*On ajoute le paire de phrase dans le pack*/
INSERT INTO PackPairesPairePhrases VALUES (1, 1);
INSERT INTO PackPairesPairePhrases VALUES (1, 2);
