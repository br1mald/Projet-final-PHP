CREATE DATABASE actualites;

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    login VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('editeur', 'administrateur') NOT NULL
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    contenu TEXT NOT NULL,
    categorie_id INT NOT NULL,
    auteur_id INT NOT NULL,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id),
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id)
);

INSERT INTO categories (nom) VALUES
('Technologie'),
('Sport'),
('Politique'),
('Éducation'),
('Culture');

INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES
('Admin', 'Super', 'admin', '$2y$10$Y0Af.Yw4bWDpHxHYPmbZ1.Hbln8yLplMVPHIDo6SJElZnd2velx8K', 'administrateur');

INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES
('Fall', 'Aminata', 'aminata', '$2y$10$I3qVivYs19ZKGnZ/Dq32K.nRIykcB/Gb0MeA4NvSEpbxwtkzQwHmS', 'editeur');


INSERT INTO articles (titre, description, contenu, categorie_id, auteur_id) VALUES
(
    'L''intelligence artificielle en Afrique',
    'Les avancées de l''IA transforment le continent africain.',
    'L''intelligence artificielle connaît un essor remarquable en Afrique. De nombreuses startups développent des solutions adaptées aux réalités locales, notamment dans les domaines de la santé, de l''agriculture et de l''éducation. Les gouvernements commencent également à intégrer ces technologies dans leurs stratégies de développement.',
    1, 1
),
(
    'CAN 2026 : les favoris',
    'Tour d''horizon des équipes favorites pour la prochaine Coupe d''Afrique.',
    'La Coupe d''Afrique des Nations 2026 approche à grands pas. Le Sénégal, le Maroc et le Nigeria figurent parmi les favoris. Les sélections ont intensifié leur préparation avec des matchs amicaux et des stages de haut niveau.',
    2, 1
),
(
    'Réforme éducative au Sénégal',
    'Le ministère annonce de nouvelles mesures pour améliorer le système éducatif.',
    'Le Sénégal lance une réforme ambitieuse de son système éducatif. Parmi les mesures annoncées : la modernisation des programmes, l''intégration du numérique dans les classes et le renforcement de la formation des enseignants. Cette réforme vise à mieux préparer les élèves aux défis du 21e siècle.',
    4, 1
),
(
    'Startups africaines : un écosystème en pleine expansion',
    'Les jeunes entreprises tech du continent attirent de plus en plus les investisseurs.',
    'L''écosystème des startups africaines connaît une croissance exponentielle. Les levées de fonds se multiplient et les incubateurs se développent dans les grandes capitales. Dakar, Lagos et Nairobi émergent comme des hubs technologiques majeurs.',
    1, 1
),
(
    'Football : les Lions prêts pour la CAN',
    'L''équipe nationale du Sénégal finalise sa préparation pour la compétition continentale.',
    'Les Lions de la Teranga ont bouclé leur stage de préparation. Le sélectionneur reste confiant malgré les blessures de certains cadres. Les matchs amicaux ont permis de peaufiner le schéma tactique.',
    2, 1
),
(
    'Élections locales 2026 : les enjeux',
    'Les prochaines élections locales représentent un tournant majeur pour la démocratie sénégalaise.',
    'Les élections locales s''annoncent comme un scrutin déterminant. La recomposition du paysage politique et la participation citoyenne seront des facteurs clés. Les partis multiplient les meetings de campagne.',
    3, 1
),
(
    'Festival de théâtre de Dakar : la programmation dévoilée',
    'La 15e édition du festival réunira des troupes venues de toute l''Afrique.',
    'Le Festival international de théâtre de Dakar dévoile sa programmation. Des spectacles, des ateliers et des débats sont au menu. L''événement mettra à l''honneur les créations africaines contemporaines.',
    5, 1
),
(
    'Energies renouvelables : le Sénégal mise sur le solaire',
    'Les centrales solaires se multiplient à travers le pays.',
    'Le Sénégal accélère sa transition énergétique avec plusieurs projets de centrales solaires. L''objectif est d''augmenter la part des énergies renouvelables dans le mix énergétique national d''ici 2030.',
    1, 2
),
(
    'Basket-ball : l''AS Douanes championne',
    'L''équipe dakaroise remporte le championnat national pour la troisième fois consécutive.',
    'L''AS Douanes a conservé son titre de championne du Sénégal après une finale serrée. Le public a vibré lors des derniers moments du match. Le coach salue la combativité de ses joueuses.',
    2, 2
),
(
    'Budget 2026 : les priorités du gouvernement',
    'L''Assemblée nationale examine le projet de loi de finances.',
    'Le gouvernement présente ses priorités budgétaires pour l''année 2026. La santé, l''éducation et les infrastructures figurent en tête des dépenses. L''opposition demande des précisions sur certains postes.',
    3, 2
),
(
    'Formation des enseignants : un programme national',
    'Un nouveau dispositif vise à renforcer les compétences pédagogiques.',
    'Le ministère de l''Éducation lance un programme de formation continue pour les enseignants. Des modules sur le numérique et la pédagogie différenciée sont proposés. Plus de 50 000 enseignants sont concernés.',
    4, 2
),
(
    'Musique : le Mbalax célébré à Gorée',
    'Un hommage rendu à ce rythme emblématique du patrimoine sénégalais.',
    'L''île de Gorée accueille un festival dédié au Mbalax. Des artistes de renom et de jeunes talents se produisent sur scène. L''événement attire des visiteurs du monde entier.',
    5, 2
),
(
    'Télémédecine : une solution pour les zones rurales',
    'Des plateformes numériques permettent des consultations à distance.',
    'La télémédecine se développe au Sénégal pour pallier le manque de médecins en milieu rural. Des hôpitaux de référence sont connectés à des postes de santé éloignés. Les premiers retours sont encourageants.',
    1, 1
);

-- Ajouter l'url d'une image pour chaque article
ALTER TABLE articles ADD COLUMN image VARCHAR(255) DEFAULT NULL;
