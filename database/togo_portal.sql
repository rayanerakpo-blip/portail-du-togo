-- ============================================================
--  PORTAIL DU TOGO - Schéma de base de données
--  MySQL / MariaDB - utf8mb4
--  Importer via phpMyAdmin ou : mysql -u root < togo_portal.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS `togo_portal`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `togo_portal`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
--  Administrateurs (back-office)
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `username`      VARCHAR(60)  NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `nom_complet`   VARCHAR(120) DEFAULT NULL,
  `email`         VARCHAR(150) DEFAULT NULL,
  `derniere_connexion` DATETIME DEFAULT NULL,
  `cree_le`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mot de passe par défaut : Admin@Togo2026  (À CHANGER après la 1re connexion)
INSERT INTO `admins` (`username`, `password_hash`, `nom_complet`, `email`) VALUES
('admin', '$2y$10$L8acqx0FiV42jyRjtsxaQ.Id9Bob/HfMPdKjRWVFTW.xnmXPyHg1O', 'Administrateur', 'admin@togo.tg');

-- ------------------------------------------------------------
--  Catégories de démarches
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `nom`         VARCHAR(120) NOT NULL,
  `slug`        VARCHAR(140) NOT NULL UNIQUE,
  `icone`       VARCHAR(60)  DEFAULT 'folder',
  `description` TEXT,
  `ordre`       INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`,`nom`,`slug`,`icone`,`description`,`ordre`) VALUES
(1,'État civil','etat-civil','baby','Naissance, mariage, décès : vos actes officiels.',1),
(2,'Identité & Voyage','identite-voyage','id-card','Passeport, carte d''identité et documents de voyage.',2),
(3,'Véhicules & Permis','vehicules-permis','car','Permis de conduire, immatriculation, cartes grises.',3),
(4,'Fiscalité & Impôts','fiscalite-impots','receipt','Déclarations, paiement des taxes et impôts (OTR).',4),
(5,'Justice','justice','scale','Casier judiciaire, légalisations, certificats.',5),
(6,'Santé','sante','heart-pulse','Couverture sanitaire, vaccinations, certificats médicaux.',6),
(7,'Études & Diplômes','etudes-diplomes','graduation-cap','Inscriptions, équivalences et authentification des diplômes.',7),
(8,'Entreprise & Emploi','entreprise-emploi','briefcase','Création d''entreprise, RCCM, autorisations.',8);

-- ------------------------------------------------------------
--  Démarches & services
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `demarches`;
CREATE TABLE `demarches` (
  `id`                INT AUTO_INCREMENT PRIMARY KEY,
  `categorie_id`      INT DEFAULT NULL,
  `titre`             VARCHAR(180) NOT NULL,
  `slug`              VARCHAR(200) NOT NULL UNIQUE,
  `resume`            VARCHAR(300) DEFAULT NULL,
  `description`       TEXT,
  `documents_requis`  TEXT,
  `cout`              VARCHAR(120) DEFAULT NULL,
  `delai`             VARCHAR(120) DEFAULT NULL,
  `lieu`              VARCHAR(200) DEFAULT NULL,
  `lien_officiel`     VARCHAR(255) DEFAULT NULL,
  `en_ligne`          TINYINT(1) DEFAULT 0,
  `actif`             TINYINT(1) DEFAULT 1,
  `vues`              INT DEFAULT 0,
  `cree_le`           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_demarche_categorie` FOREIGN KEY (`categorie_id`)
    REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `demarches`
(`categorie_id`,`titre`,`slug`,`resume`,`description`,`documents_requis`,`cout`,`delai`,`lieu`,`lien_officiel`,`en_ligne`,`vues`) VALUES
(2,'Demande de passeport ordinaire','demande-passeport-ordinaire',
 'Obtenez ou renouvelez votre passeport togolais biométrique.',
 'Le passeport ordinaire est un document de voyage délivré aux citoyens togolais. La demande se fait auprès de la Direction Générale de la Documentation Nationale ou dans les centres habilités.',
 'Formulaire de demande rempli;\nActe de naissance original (moins de 3 mois);\nCertificat de nationalité togolaise;\n2 photos d''identité récentes (fond blanc);\nCopie de la carte nationale d''identité;\nQuittance de paiement des frais.',
 '35 000 FCFA (ordinaire)','10 à 15 jours ouvrables','Direction Générale de la Documentation Nationale, Lomé','https://passeport.tg',1,120),
(2,'Carte nationale d''identité (CNI)','carte-nationale-identite',
 'Établissement ou renouvellement de la carte nationale d''identité togolaise.',
 'La carte nationale d''identité atteste de l''identité et de la nationalité togolaise. Elle est délivrée par les services de la Documentation Nationale.',
 'Extrait d''acte de naissance;\nCertificat de nationalité;\n2 photos d''identité;\nAncienne carte (en cas de renouvellement);\nReçu de paiement.',
 '3 000 FCFA','5 à 10 jours ouvrables','Mairies et centres de documentation nationale','https://documentation-nationale.tg',0,95),
(1,'Extrait d''acte de naissance','extrait-acte-naissance',
 'Obtenez une copie ou un extrait de votre acte de naissance.',
 'L''extrait d''acte de naissance est délivré par la mairie ou le centre d''état civil du lieu de naissance.',
 'Pièce d''identité du demandeur;\nInformations sur le lieu et la date de naissance;\nLivret de famille (si disponible).',
 '500 à 1 000 FCFA','1 à 3 jours','Mairie ou centre d''état civil compétent',NULL,0,210),
(1,'Acte de mariage','acte-de-mariage',
 'Célébration civile du mariage et délivrance du livret de famille.',
 'Le mariage civil se célèbre à la mairie du lieu de résidence de l''un des époux, après publication des bans.',
 'Actes de naissance des deux époux;\nCertificats de résidence;\n2 témoins majeurs;\nPièces d''identité;\nCertificats médicaux prénuptiaux.',
 'Variable selon la commune','Publication des bans : 15 jours','Mairie compétente',NULL,0,60),
(3,'Permis de conduire','permis-de-conduire',
 'Passez votre permis de conduire ou renouvelez-le.',
 'Le permis de conduire est délivré après réussite aux examens théorique et pratique organisés par le service des transports.',
 'Acte de naissance;\nCertificat de nationalité;\nCertificat médical d''aptitude;\n2 photos d''identité;\nJustificatif de paiement des frais de formation et d''examen.',
 '25 000 à 60 000 FCFA selon la catégorie','4 à 8 semaines (selon sessions)','Direction des Transports Routiers et centres d''examen','https://transports.gouv.tg',0,142),
(3,'Immatriculation d''un véhicule','immatriculation-vehicule',
 'Obtenez une plaque d''immatriculation et la carte grise.',
 'Tout véhicule circulant au Togo doit être immatriculé. La démarche se fait auprès du service des transports.',
 'Facture d''achat ou acte de cession;\nCertificat de dédouanement (véhicule importé);\nPièce d''identité du propriétaire;\nAssurance en cours de validité;\nVisite technique.',
 'Variable selon le type de véhicule','3 à 7 jours','Direction des Transports Routiers',NULL,0,88),
(4,'Création d''entreprise / RCCM','creation-entreprise-rccm',
 'Immatriculez votre entreprise et obtenez votre numéro RCCM.',
 'Le Centre de Formalités des Entreprises (CFE) permet de créer une entreprise en guichet unique : RCCM, NIF, et déclarations sociales.',
 'Formulaire de demande;\nStatuts de la société (si société);\nPièce d''identité du promoteur;\nJustificatif d''adresse;\nPlan de localisation.',
 'Variable selon la forme juridique','48 à 72 heures (guichet unique)','Centre de Formalités des Entreprises (CFE), Lomé','https://cfe.tg',1,175),
(4,'Déclaration et paiement des impôts','declaration-impots-otr',
 'Déclarez et payez vos impôts auprès de l''Office Togolais des Recettes (OTR).',
 'Les contribuables déclarent leurs revenus et paient leurs taxes auprès de l''OTR, en ligne via le portail e-impôts ou en centre des impôts.',
 'Numéro d''Identification Fiscale (NIF);\nDéclaration remplie;\nJustificatifs de revenus;\nRelevés bancaires (si applicable).',
 'Selon le régime fiscal','Déclaration annuelle selon le calendrier fiscal','OTR - Office Togolais des Recettes','https://otr.tg',1,133),
(5,'Casier judiciaire (bulletin n°3)','casier-judiciaire',
 'Demandez votre extrait de casier judiciaire, souvent exigé à l''embauche.',
 'Le bulletin n°3 du casier judiciaire est délivré par le service du casier judiciaire, auprès des tribunaux ou en ligne.',
 'Acte de naissance;\nCertificat de nationalité;\nPièce d''identité;\nTimbre fiscal.',
 '1 000 à 2 000 FCFA','2 à 5 jours','Tribunal compétent / service du casier judiciaire','https://casier.tg',1,156),
(5,'Certificat de nationalité togolaise','certificat-nationalite',
 'Attestation officielle de la nationalité togolaise.',
 'Le certificat de nationalité est délivré par les autorités judiciaires sur présentation de preuves de la filiation togolaise.',
 'Acte de naissance;\nActe de naissance du parent togolais;\nPièces d''identité des parents;\nTémoignages si nécessaire;\nTimbre fiscal.',
 '2 000 FCFA','1 à 3 semaines','Tribunal de première instance compétent',NULL,0,74),
(5,'Légalisation et authentification de documents','legalisation-documents',
 'Faites légaliser vos diplômes, actes et documents officiels.',
 'La légalisation certifie la conformité de la signature ou de la copie d''un document officiel. Elle est effectuée par le ministère concerné puis les Affaires Étrangères pour l''étranger.',
 'Document original;\nCopie du document;\nPièce d''identité;\nTimbre fiscal.',
 'Variable selon le document','1 à 5 jours par étape','Ministère concerné puis Ministère des Affaires Étrangères',NULL,0,67),
(6,'Couverture sanitaire universelle (AMU)','couverture-sanitaire-amu',
 'Inscrivez-vous à l''Assurance Maladie Universelle.',
 'L''AMU (ex-AMU) permet l''accès aux soins de santé à coût réduit pour les résidents. L''inscription se fait auprès des caisses de sécurité sociale.',
 'Acte de naissance;\nPièce d''identité;\nJustificatif de résidence;\nPhoto d''identité;\nJustificatif d''activité ou de revenus.',
 'Contribution selon le statut','Sur place selon le guichet','Caisse Nationale de Sécurité Sociale (CNSS)','https://cnss.tg',0,52),
(7,'Équivalence et authentification de diplômes','equivalence-diplomes',
 'Faites authentifier ou reconnaître vos diplômes au Togo.',
 'L''authentification est faite par le ministère de l''éducation concerné. L''équivalence des diplômes étrangers est délivrée par la commission nationale compétente.',
 'Diplôme original et copies;\nRelevés de notes;\nPièce d''identité;\nFormulaire de demande;\nFrais de dossier.',
 'Variable selon le niveau','2 à 6 semaines','Ministère de l''Éducation / Direction des examens',NULL,0,49),
(7,'Inscription universitaire','inscription-universitaire',
 'Inscrivez-vous à l''Université de Lomé ou dans un établissement supérieur public.',
 'Les inscriptions universitaires se font en ligne via la plateforme des œuvres universitaires puis en présentiel selon les établissements.',
 'Baccalauréat ou attestation;\nRelevé de notes;\nActe de naissance;\nPhoto d''identité;\nFiche d''inscription.',
 'Frais d''inscription selon l''établissement','Selon le calendrier académique','Université de Lomé et établissements publics','https://univ-lome.tg',1,101),
(8,'Autorisation de travail pour étranger','autorisation-travail-etranger',
 'Obtenez un permis de travail au Togo en tant que ressortissant étranger.',
 'L''emploi d''un travailleur étranger est soumis à autorisation délivrée par le ministère chargé du travail.',
 'Contrat de travail;\nPasseport valide;\nVisa et titre de séjour;\nDiplômes et qualifications;\nDemande de l''employeur.',
 'Variable selon la durée','2 à 4 semaines','Ministère chargé du Travail',NULL,0,38),
(6,'Certificat médical d''aptitude','certificat-medical-aptitude',
 'Certificat médical exigé pour le permis, l''emploi ou le sport.',
 'Le certificat médical est délivré par un médecin agréé attestant de l''aptitude physique du demandeur.',
 'Pièce d''identité;\nPhotos (selon usage);\nJustificatif du motif.',
 '2 000 à 5 000 FCFA','Le jour même','Centre de santé ou cabinet agréé',NULL,0,44),
(1,'Certificat de résidence','certificat-de-residence',
 'Atteste de votre domicile dans une commune.',
 'Le certificat de résidence est délivré par la mairie ou par le chef de quartier, confirmant votre lieu de résidence.',
 'Pièce d''identité;\nJustificatif de domicile;\nTémoignage du chef de quartier.',
 '500 à 2 000 FCFA','1 à 2 jours','Mairie / chefferie du quartier',NULL,0,58);

-- ------------------------------------------------------------
--  Ministères & institutions
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `ministeres`;
CREATE TABLE `ministeres` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `nom`         VARCHAR(200) NOT NULL,
  `slug`        VARCHAR(220) NOT NULL UNIQUE,
  `sigle`       VARCHAR(60) DEFAULT NULL,
  `ministre`    VARCHAR(150) DEFAULT NULL,
  `description` TEXT,
  `missions`    TEXT,
  `site_web`    VARCHAR(255) DEFAULT NULL,
  `email`       VARCHAR(150) DEFAULT NULL,
  `telephone`   VARCHAR(60) DEFAULT NULL,
  `adresse`     VARCHAR(255) DEFAULT NULL,
  `ordre`       INT DEFAULT 0,
  `actif`       TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `ministeres`
(`nom`,`slug`,`sigle`,`description`,`missions`,`site_web`,`adresse`,`ordre`) VALUES
('Ministère de l''Économie et des Finances','ministere-economie-finances','MEF',
 'Chargé de la politique économique, budgétaire et financière de l''État.',
 'Élaboration du budget national;\nGestion des finances publiques;\nPolitique fiscale et douanière;\nTutelle de l''Office Togolais des Recettes (OTR).',
 'https://finances.gouv.tg','Lomé, Quartier administratif',1),
('Ministère de la Santé, de l''Hygiène Publique et de l''Accès Universel aux Soins','ministere-sante','MSHPAUS',
 'Garantit l''accès aux soins de santé pour toute la population.',
 'Politique sanitaire nationale;\nGestion des hôpitaux publics;\nCampagnes de vaccination;\nCouverture Sanitaire Universelle.',
 'https://sante.gouv.tg','Lomé',2),
('Ministère de l''Éducation Nationale','ministere-education-nationale','MEN',
 'En charge de l''éducation de la maternelle au supérieur.',
 'Programmes scolaires;\nGestion des établissements publics;\nExamens nationaux (CEPD, BEPC, BAC);\nRecrutement des enseignants.',
 'https://education.gouv.tg','Lomé',3),
('Ministère de l''Administration Territoriale, de la Décentralisation et du Développement des Territoires','ministere-administration-territoriale','MATDDT',
 'Organise l''administration du territoire et les collectivités locales.',
 'Découpage administratif;\nTutelle des collectivités;\nOrganisation des élections;\nÉtat civil et cartes d''identité.',
 'https://territoire.gouv.tg','Lomé',4),
('Ministère de la Sécurité et de la Protection Civile','ministere-securite-protection-civile','MSPC',
 'Assure la sécurité des personnes et des biens.',
 'Police et gendarmerie nationales;\nProtection civile et secours;\nSécurité routière;\nGestion des frontières.',
 'https://securite.gouv.tg','Lomé',5),
('Ministère de la Justice et des Relations avec les Institutions de la République','ministere-justice','MJ',
 'Garantit l''application de la loi et l''indépendance de la justice.',
 'Administration pénitentiaire;\nCasier judiciaire;\nNotariat et état civil;\nRéforme judiciaire.',
 'https://justice.gouv.tg','Lomé',6),
('Ministère des Affaires Étrangères, de l''Intégration Régionale et des Togolais de l''Extérieur','ministere-affaires-etrangeres','MAEIRTE',
 'Conduit la diplomatie togolaise et protège les Togolais de l''étranger.',
 'Relations diplomatiques;\nCoopération internationale;\nPasseports et visas;\nDiaspora togolaise.',
 'https://diplomatie.gouv.tg','Lomé',7),
('Ministère des Travaux Publics, des Mines et des Carrières','ministere-travaux-publics','MTPMC',
 'Construit et entretient les infrastructures du pays.',
 'Routes et ponts;\nBâtiments publics;\nExploitation minière;\nTransports et travaux.',
 'https://travauxpublics.gouv.tg','Lomé',8),
('Ministère de l''Agriculture, de l''Élevage et du Développement Rural','ministere-agriculture','MAEDR',
 'Développe le secteur agricole et rural.',
 'Production vivrière;\nAppui aux agriculteurs;\nÉlevage et pêche;\nSécurité alimentaire.',
 'https://agriculture.gouv.tg','Lomé',9),
('Ministère du Numérique et de la Transformation Digitale','ministere-numerique','MNTD',
 'Pilote la transformation numérique de l''administration.',
 'Dématérialisation des services;\nInfrastructures numériques;\nCybersécurité;\nEconomie numérique.',
 'https://numerique.gouv.tg','Lomé',10),
('Ministère de la Communication, des Médias et de la Culture','ministere-communication-culture','MCMC',
 'Développe la communication publique et la culture.',
 'Médias publics;\nPatrimoine culturel;\nIndustries créatives;\nCommunication gouvernementale.',
 'https://culture.gouv.tg','Lomé',11);

-- ------------------------------------------------------------
--  Actualités / communiqués
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `actualites`;
CREATE TABLE `actualites` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `titre`      VARCHAR(200) NOT NULL,
  `slug`       VARCHAR(220) NOT NULL UNIQUE,
  `extrait`    VARCHAR(400) DEFAULT NULL,
  `contenu`    TEXT,
  `categorie`  VARCHAR(80) DEFAULT 'Communiqué',
  `source`     VARCHAR(150) DEFAULT NULL,
  `source_url` VARCHAR(255) DEFAULT NULL,
  `auteur`     VARCHAR(120) DEFAULT NULL,
  `image`      VARCHAR(255) DEFAULT NULL,
  `date_pub`   DATETIME DEFAULT CURRENT_TIMESTAMP,
  `actif`      TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `actualites`
(`titre`,`slug`,`extrait`,`contenu`,`categorie`,`source`,`source_url`,`date_pub`) VALUES
('Lancement de la plateforme de dématérialisation des démarches administratives',
 'lancement-plateforme-dematerialisation',
 'Le gouvernement togolais accélère la transformation numérique de l''administration avec une nouvelle plateforme de services en ligne.',
 'Dans le cadre de la modernisation de l''administration publique, le Ministère du Numérique et de la Transformation Digitale annonce le lancement progressif d''une plateforme de dématérialisation des démarches administratives.\n\nLes usagers pourront désormais initier et suivre plusieurs démarches en ligne : demande de documents d''état civil, immatriculation d''entreprise, et paiement de taxes.\n\nCette initiative vise à réduire les délais, limiter les déplacements et améliorer la transparence du service public.',
 'Numérique','Portail du Togo',NULL,'2026-09-15 09:00:00'),
('Campagne nationale de vaccination et de sensibilisation sanitaire',
 'campagne-nationale-vaccination',
 'Le Ministère de la Santé lance une vaste campagne de vaccination sur l''ensemble du territoire.',
 'Le Ministère de la Santé, de l''Hygiène Publique et de l''Accès Universel aux Soins organise une campagne nationale de vaccination gratuite dans les centres de santé publics.\n\nLes équipes mobiles sillonneront les préfectures pour atteindre les zones rurales. La population est invitée à se présenter munie d''une pièce d''identité.\n\nCette campagne s''inscrit dans le renforcement de la Couverture Sanitaire Universelle.',
 'Santé','Ministère de la Santé',NULL,'2026-09-28 08:30:00'),
('Ouverture de la plateforme unique de création d''entreprise',
 'ouverture-plateforme-creation-entreprise',
 'Créer son entreprise au Togo en 72 heures devient une réalité grâce au guichet unique du CFE.',
 'Le Centre de Formalités des Entreprises (CFE) renforce son guichet unique permettant aux entrepreneurs de créer leur entreprise en moins de 72 heures.\n\nToutes les formalités — immatriculation au RCCM, obtention du NIF et déclarations sociales — sont désormais regroupées en un seul point de service, en présentiel et bientôt en ligne.\n\nLe gouvernement encourage les jeunes et les femmes à entreprendre.',
 'Économie','CFE',NULL,'2026-10-02 11:15:00'),
('Rentrée scolaire : rappel des inscriptions et des examens nationaux',
 'rentree-scolaire-inscriptions-examens',
 'Le Ministère de l''Éducation Nationale publie le calendrier de la rentrée et des examens.',
 'Le Ministère de l''Éducation Nationale rappelle aux parents et élèves les modalités d''inscription dans les établissements publics pour la nouvelle année scolaire.\n\nLe calendrier des examens nationaux (CEPD, BEPC, Baccalauréat) sera communiqué dans les prochaines semaines. Les inscriptions se font auprès des directions régionales de l''éducation.\n\nLes frais de scolarité dans le public restent pris en charge par l''État au niveau du primaire.',
 'Éducation','Ministère de l''Éducation Nationale',NULL,'2026-10-08 07:45:00'),
('Sécurité routière : renforcement des contrôles et de la sensibilisation',
 'securite-routiere-controles',
 'Le Ministère de la Sécurité renforce les contrôles routiers et lance une campagne de prévention.',
 'Afin de réduire les accidents de la route, le Ministère de la Sécurité et de la Protection Civile renforce les contrôles sur les grands axes et multiplie les actions de sensibilisation.\n\nLes usagers sont invités à respecter le code de la route, à porter le casque et la ceinture, et à ne pas conduire sous l''emprise d''alcool.\n\nLe numéro d''urgence des sapeurs-pompiers est le 118.',
 'Sécurité','Ministère de la Sécurité',NULL,'2026-10-10 16:20:00');

-- ------------------------------------------------------------
--  Numéros utiles & urgences
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `urgences`;
CREATE TABLE `urgences` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `nom`         VARCHAR(150) NOT NULL,
  `numero`      VARCHAR(40)  NOT NULL,
  `categorie`   VARCHAR(80)  DEFAULT 'Urgence',
  `description` VARCHAR(300) DEFAULT NULL,
  `icone`       VARCHAR(60)  DEFAULT 'phone',
  `ordre`       INT DEFAULT 0,
  `actif`       TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `urgences` (`nom`,`numero`,`categorie`,`description`,`icone`,`ordre`) VALUES
('Police Secours','117','Sécurité','Police nationale - intervention immédiate.','shield',1),
('Sapeurs-Pompiers','118','Secours','Incendies, accidents et secours d''urgence.','fire',2),
('SAMU / Urgences médicales','8200','Santé','Service d''aide médicale urgente.','plus',3),
('Gendarmerie Nationale','113','Sécurité','Interventions en zone rurale et routière.','shield',4),
('Enfance en danger','1011','Assistance','Signalement de maltraitance et protection de l''enfance.','child',5),
('Violences basées sur le genre','8014','Assistance','Écoute et prise en charge des victimes de VBG.','heart',6),
('Protection Civile','170','Secours','Coordination des secours et catastrophes.','alert',7),
('Urgences Électricité (CEET)','8020','Services','Dépannage du réseau électrique national.','bolt',8);

-- ------------------------------------------------------------
--  Messages du formulaire de contact
-- ------------------------------------------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id`        INT AUTO_INCREMENT PRIMARY KEY,
  `nom`       VARCHAR(120) NOT NULL,
  `email`     VARCHAR(150) NOT NULL,
  `sujet`     VARCHAR(180) DEFAULT NULL,
  `message`   TEXT NOT NULL,
  `lu`        TINYINT(1) DEFAULT 0,
  `cree_le`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
--  Fin du script
-- ============================================================
