/*Delete the schema and the table*/
DROP SCHEMA public CASCADE;
CREATE SCHEMA public;

/*Utilisateur*/
CREATE TABLE Eleve(
id CHAR(50),
password CHAR(128),
nbGame1 INTEGER,
nbGame2 INTEGER,
PRIMARY KEY(id)
);

CREATE TABLE Classe(
nom CHAR(50),
mail CHAR(150),
password CHAR(128),
PRIMARY KEY(mail)
);

CREATE TABLE EleveClasse(
idEleve CHAR(50),
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
nom CHAR(50),
mailClasse CHAR(150),
note integer,
telechargement integer,
PRIMARY KEY(id),
FOREIGN KEY(mailClasse) REFERENCES Classe(mail)
);

CREATE TABLE PackPairesPairePhrases(
idPaire integer,
idPack integer,
FOREIGN KEY(idPaire) REFERENCES PairePhrases(idPaire),
FOREIGN KEY(idPack)  REFERENCES PackPaires(id)
);

/*Statistiques */
CREATE TYPE GAME AS ENUM ('Game1', 'Game2');

CREATE TABLE Historique(
idHisto SERIAL,
idEleve CHAR(50),
idGame GAME, /*Game 1 or Game 2 ?*/
jour DATE,
PRIMARY KEY(idHisto),
FOREIGN KEY (idEleve) REFERENCES Eleve(id)
);

CREATE TABLE EleveHistoG1(
idGame1 integer,
idHisto integer,
idPack integer,
idPairePhrase integer,
PRIMARY KEY(idGame1),
FOREIGN KEY(idHisto) REFERENCES Historique(idHisto),
FOREIGN KEY(idPack)  REFERENCES PackPaires(id),
FOREIGN KEY(idPairePhrase) REFERENCES PairePhrases(idPaire)
);

CREATE TABLE EleveResultG1(
idGame1 integer,
idWord1 integer, /*The id of the word sentence 1*/
idWord2 integer, /*The id of the word sentence 2*/
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

/*Fill the table for examples*/
INSERT INTO Eleve       VALUES ('User', '', 0, 0);
INSERT INTO Classe      VALUES ('Teacher', 'prof@scolaire.fr', '');
INSERT INTO EleveClasse VALUES ('User', 'prof@scolaire.fr');
INSERT INTO Phrase      VALUES (DEFAULT);
INSERT INTO GroupeMots  VALUES (DEFAULT, 1, 'Bonjour');
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
INSERT INTO PackPaires VALUES (DEFAULT, 'default pack', 'prof@scolaire.fr');
INSERT INTO PackPairesPairePhrases VALUES (1, 1);
