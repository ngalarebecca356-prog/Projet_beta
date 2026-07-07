
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE EMPLOYE (
  Matricule_Employer VARCHAR(10)  NOT NULL,
  Nom                VARCHAR(50),
  Prenom              VARCHAR(50),
  Date_Embauche       DATE,
  Salaire             INT,
  Email               VARCHAR(100),
  Sexe                VARCHAR(10),
  DateNaissance       DATE,
  Poste               VARCHAR(50),
  Contrat             VARCHAR(30),
  Statut_Contrat      VARCHAR(25),
  Tel                 VARCHAR(15),
  Taux_Journalier     INT,
  Departement         VARCHAR(100),
  PRIMARY KEY (Matricule_Employer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE CLIENT (
  Siret            VARCHAR(15) NOT NULL,
  Nom_Client       VARCHAR(50),
  Secteur_Activite VARCHAR(50),
  Adresse          VARCHAR(50),
  Tel              VARCHAR(100),
  Email_Client     VARCHAR(20),
  PRIMARY KEY (Siret)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE PROJET (
  Code_Projet         VARCHAR(100) NOT NULL,
  Intitule_Projet     VARCHAR(50),
  Date_Debut          DATE,
  Date_Fin            DATE,
  Descriptif          VARCHAR(100),
  Siret               VARCHAR(15),
  Statut_Projet       VARCHAR(50),
  Budjet_Prevue       INT,
  Type_Contrat_Projet VARCHAR(50),
  Date_Fin_Reelle     DATE,
  PRIMARY KEY (Code_Projet),
  FOREIGN KEY (Siret) REFERENCES CLIENT(Siret)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE TACHE (
  Num_Tache              VARCHAR(15) NOT NULL,
  Libelle_Tache          VARCHAR(50),
  Date_Debut_Tache       DATE,
  Date_Fin_Tache         DATE,
  Cout_Previsionnel      INT,
  Code_Projet            VARCHAR(100),
  Matricule_Employer     VARCHAR(10),
  Statut_Tache           VARCHAR(10),
  Avancement_Pourcentage INT,
  PRIMARY KEY (Num_Tache),
  FOREIGN KEY (Code_Projet) REFERENCES PROJET(Code_Projet),
  FOREIGN KEY (Matricule_Employer) REFERENCES EMPLOYE(Matricule_Employer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE AFFECTATION (
  Matricule_Employer VARCHAR(10)  NOT NULL,
  Code_Projet        VARCHAR(100) NOT NULL,
  Role_Affectation   VARCHAR(50),
  Taux_Affectation   VARCHAR(5),
  PRIMARY KEY (Matricule_Employer, Code_Projet),
  FOREIGN KEY (Matricule_Employer) REFERENCES EMPLOYE(Matricule_Employer),
  FOREIGN KEY (Code_Projet) REFERENCES PROJET(Code_Projet)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE COUT_MENSUEL_PROJET (
  Code_Projet        VARCHAR(100) NOT NULL,
  Mois_Calcul        INT NOT NULL,
  Annee              INT NOT NULL,
  Depense            INT,
  Cout_Total_Calcule INT,
  PRIMARY KEY (Code_Projet, Mois_Calcul, Annee),
  FOREIGN KEY (Code_Projet) REFERENCES PROJET(Code_Projet)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE SAISIE_TEMPS (
  Num_Tache          VARCHAR(15) NOT NULL,
  Matricule_Employer VARCHAR(10) NOT NULL,
  Date_Saisie        DATE NOT NULL,
  Nombre_Heures      INT,
  PRIMARY KEY (Num_Tache, Matricule_Employer, Date_Saisie),
  FOREIGN KEY (Num_Tache) REFERENCES TACHE(Num_Tache),
  FOREIGN KEY (Matricule_Employer) REFERENCES EMPLOYE(Matricule_Employer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table isolée pour l'instant, comme dans ta feuille (aucun lien vers EMPLOYE ou TACHE).
CREATE TABLE COMPETENCE (
  Code_Competence      VARCHAR(10) NOT NULL,
  Libelle_Competence   VARCHAR(10),
  Categorie_Competence VARCHAR(25),
  PRIMARY KEY (Code_Competence)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE UTILISATEUR (
  Matricule_Employer   VARCHAR(10) NOT NULL,
  mdp                  VARCHAR(255),
  Role                 VARCHAR(20),
  Date_Creation_Compte DATETIME,
  Statut_Compte        VARCHAR(6),
  PRIMARY KEY (Matricule_Employer),
  FOREIGN KEY (Matricule_Employer) REFERENCES EMPLOYE(Matricule_Employer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE MANAGER (
  Num_Mandat         VARCHAR(10) NOT NULL,
  Code_Projet        VARCHAR(100),
  Matricule_Employer VARCHAR(10),
  Date_Debut_Mandat  DATE,
  Date_Fin_Mandat    DATE,
  PRIMARY KEY (Num_Mandat),
  FOREIGN KEY (Code_Projet) REFERENCES PROJET(Code_Projet),
  FOREIGN KEY (Matricule_Employer) REFERENCES EMPLOYE(Matricule_Employer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
