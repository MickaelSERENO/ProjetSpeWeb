/*Utilisateur*/
CREATE TABLE Eleve(
id CHAR(50),
id password CHAR(128),
PRIMARY KEY(id)
);

CREATE TABLE Classe(
nom CHAR(50),
mail CHAR(150),
password CHAR(128),
PRIMARY KEY(mail)
);

CREATE TABLE EleveClasse(
idEleve : CHAR(50),
mailClasse : CHAR(150),
FOREIGN KEY(idEleve) REFERENCES Eleve(id),
FOREIGN KEY(mailClasse) REFERENCES Classe(mail)
);

/*Phrases*/
CREATE TABLE GroupeMots(
id SERIAL,
texte CHAR(256),
PRIMARY KEY(id)
);

CREATE TABLE Phrase(
id SERIAL,
PRIMARY KEY(id)
);

CREATE TABLE GroupeMotsPhrase(
idMot integer,
idPhrase integer,
FOREIGN KEY(idMot) REFERENCES GroupeMots(id),
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

CREATE TYPE OP AS ENUM ('synonyme', 'antonyme', 'relOrdre');

CREATE TABLE AssociationMots(
idGroupeMots1 integer,
idGroupeMots2 integer,
relation OP,
FOREIGN KEY(idGroupeMots1) REFERENCES GroupeMots(id),
FOREIGN KEY(idGroupeMots2) REFERENCES GroupeMots(id)
);

CREATE TABLE PackPaires(
id SERIAL,
nom CHAR[50],
mailClasse CHAR[150],
note integer,
telechargement integer,
PRIMARY KEY(id),
FOREIGN KEY(mailClasse) REFERENCES Classe(mail)
);

CREATE TABLE PackPairesPairePhrases(
idPaire integer,
idPack integer,
FOREIGN KEY(idPaire) REFERENCES PairePhrases(idPaire),
FOREIGN KEY(idPack) REFERENCES PairePhrases(id)
);

/*Statistiques */
CREATE TABLE EleveReussite(
idEleve CHAR(50),
idPhrase integer,
reussite boolean,
FOREIGN KEY (idEleve) REFERENCES Eleve(id),
FOREIGN KEY (idPhrase) REFERENCES Phrase(id)
);

CREATE TABLE PhraseInventee(
id SERIAL,
PRIMARY KEY(id)
);

CREATE TABLE PhrasePhraseInventee(
idPhrase integer,
idPhraseInventee,
FOREIGN KEY(idPhrase) REFERENCES Phrase(id),
FOREIGN KEY(idPhraseInventee) REFERENCES PhraseInventee(id)
);