-- =============================================
-- Script d'initialisation - Base de données "actualites"
-- =============================================

CREATE DATABASE IF NOT EXISTS actualites;
USE actualites;

-- =============================================
-- TABLES
-- =============================================

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
    image VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (categorie_id) REFERENCES categories(id),
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id)
);

-- =============================================
-- CATEGORIES
-- =============================================

INSERT INTO categories (nom) VALUES
('Technologie'),
('Sport'),
('Politique'),
('Éducation'),
('Culture');

-- =============================================
-- UTILISATEURS
-- Mots de passe : admin -> "admin", aminata -> "aminata"
-- =============================================

INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES
('Admin', 'Super', 'admin', '$2y$10$Y0Af.Yw4bWDpHxHYPmbZ1.Hbln8yLplMVPHIDo6SJElZnd2velx8K', 'administrateur'),
('Fall', 'Aminata', 'aminata', '$2y$10$I3qVivYs19ZKGnZ/Dq32K.nRIykcB/Gb0MeA4NvSEpbxwtkzQwHmS', 'editeur');

-- =============================================
-- ARTICLES
-- =============================================

INSERT INTO articles (id, titre, description, contenu, categorie_id, auteur_id, date_publication, image) VALUES
(1,
 'L''intelligence artificielle en Afrique',
 'Les avancées de l''IA transforment le continent africain.',
 'L''intelligence artificielle connaît un essor remarquable en Afrique. De nombreuses startups développent des solutions adaptées aux réalités locales, notamment dans les domaines de la santé, de l''agriculture et de l''éducation. Les gouvernements commencent également à intégrer ces technologies dans leurs stratégies de développement.',
 1, 1, '2026-01-12 08:34:00', 'uploads/art_69c5b0b607861.jpeg'),

(2,
 'CAN 2026 : les favoris',
 'Tour d''horizon des équipes favorites pour la prochaine Coupe d''Afrique.',
 'La Coupe d''Afrique des Nations 2026 approche à grands pas. Le Sénégal, le Maroc et le Nigeria figurent parmi les favoris. Les sélections ont intensifié leur préparation avec des matchs amicaux et des stages de haut niveau.',
 2, 1, '2026-01-18 14:12:00', 'uploads/art_69c5b0d567ae0.jpeg'),

(3,
 'Réforme éducative au Sénégal',
 'Le ministère annonce de nouvelles mesures pour améliorer le système éducatif.',
 'Le Sénégal lance une réforme ambitieuse de son système éducatif. Parmi les mesures annoncées : la modernisation des programmes, l''intégration du numérique dans les classes et le renforcement de la formation des enseignants. Cette réforme vise à mieux préparer les élèves aux défis du 21e siècle.',
 4, 1, '2026-01-25 10:45:00', 'uploads/art_69c5b0e487e95.jpeg'),

(42,
 'La 5G arrive en Afrique de l''Ouest',
 'Plusieurs opérateurs lancent leurs premiers réseaux 5G dans la sous-région.',
 'L''Afrique de l''Ouest entre dans l''ère de la 5G. Au Sénégal, Orange et Free ont obtenu leurs licences et prévoient un déploiement dans les grandes villes dès le second semestre. Au Nigeria, MTN couvre déjà Lagos et Abuja.\n\nLes promesses sont considérables : débits jusqu''à 20 fois supérieurs à la 4G, latence réduite pour les applications industrielles, et nouvelles possibilités pour la télémédecine en zone rurale.\n\nCependant, des défis persistent. Le coût des infrastructures reste élevé, et la couverture des zones rurales prendra plusieurs années. Les associations de consommateurs appellent à une régulation stricte des tarifs pour éviter une fracture numérique.',
 1, 1, '2026-02-03 07:20:00', 'uploads/art_69c5b43064689.jpg'),

(43,
 'Les fintechs africaines battent des records de levées de fonds',
 'En 2025, les startups financières du continent ont levé plus de 3 milliards de dollars.',
 'L''écosystème fintech africain ne cesse de croître. Wave, basée à Dakar, a franchi le cap des 50 millions d''utilisateurs actifs. Au Kenya, M-Pesa poursuit son expansion vers l''Afrique de l''Ouest.\n\nLes investisseurs internationaux sont de plus en plus attirés par le potentiel du continent, où plus de 60% de la population n''a toujours pas accès aux services bancaires traditionnels.\n\nLes domaines les plus financés incluent le paiement mobile, le micro-crédit, l''assurance paramétrique et les solutions d''épargne. Les régulateurs travaillent en parallèle à harmoniser les cadres juridiques pour accompagner cette croissance.',
 1, 2, '2026-02-08 16:55:00', 'uploads/art_69c5b45705d2b.jpeg'),

(44,
 'Dakar, nouveau hub technologique du continent',
 'La capitale sénégalaise attire de plus en plus de talents et d''investisseurs tech.',
 'Dakar s''impose comme un pôle technologique majeur en Afrique francophone. Le Parc des Technologies Numériques de Diamniadio accueille désormais plus de 80 entreprises et startups.\n\nPlusieurs incubateurs comme CTIC Dakar et Jokkolabs forment la prochaine génération d''entrepreneurs. Les universités locales adaptent leurs cursus pour répondre aux besoins du secteur.\n\nLe gouvernement a également lancé le programme « Sénégal Numérique 2030 », qui vise à créer 100 000 emplois dans le secteur du numérique et à faire du pays un leader régional de l''innovation.',
 1, 1, '2026-02-14 09:30:00', 'uploads/art_69c5b47a2211d.jpeg'),

(47,
 'Le basketball africain en plein essor',
 'La Basketball Africa League attire de plus en plus de talents et de sponsors.',
 'La Basketball Africa League (BAL), lancée par la NBA et la FIBA, connaît un succès grandissant. La saison 2026 voit la participation de 16 équipes issues de 12 pays.\n\nL''AS Douanes de Dakar et le Zamalek du Caire font figure de favoris cette année. Les matchs sont diffusés dans plus de 200 pays, offrant une visibilité sans précédent au basketball africain.\n\nPlusieurs joueurs issus de la BAL ont été repérés par des franchises NBA, confirmant le rôle de la ligue comme tremplin vers le plus haut niveau mondial.',
 2, 1, '2026-03-02 08:15:00', 'uploads/art_69c5b48a36dd8.jpeg'),

(48,
 'Sommet de l''Union Africaine : vers une monnaie commune ?',
 'Les chefs d''État discutent d''un projet de monnaie unique pour le continent.',
 'Lors du dernier sommet de l''Union Africaine à Addis-Abeba, la question d''une monnaie commune a dominé les débats. Le projet, discuté depuis des décennies, semble prendre une nouvelle dimension.\n\nLa Zone de libre-échange continentale africaine (ZLECAf) a renforcé les échanges commerciaux entre pays membres, rendant la question monétaire plus pressante. Plusieurs économistes africains soutiennent qu''une monnaie unique réduirait les coûts de transaction et stimulerait le commerce intra-africain.\n\nLes obstacles restent nombreux : disparités économiques entre pays, souveraineté monétaire, et le cas particulier du franc CFA en Afrique de l''Ouest et centrale.',
 3, 2, '2026-03-05 17:33:00', 'uploads/art_69c5b49841b86.jpeg'),

(49,
 'Élections locales au Sénégal : forte participation des jeunes',
 'Le taux de participation des 18-25 ans atteint un record historique.',
 'Les élections locales sénégalaises ont été marquées par une mobilisation sans précédent des jeunes électeurs. Le taux de participation des 18-25 ans a atteint 62%, contre 38% lors du précédent scrutin.\n\nLes réseaux sociaux ont joué un rôle déterminant dans cette mobilisation. Plusieurs collectifs citoyens ont mené des campagnes de sensibilisation en wolof et en français sur TikTok et Instagram.\n\nLes analystes politiques voient dans cette tendance un changement profond du paysage politique sénégalais, avec une jeunesse de plus en plus engagée dans les processus démocratiques.',
 3, 1, '2026-03-09 10:20:00', 'uploads/art_69c5b4aa7ac23.jpeg'),

(50,
 'La CEDEAO renforce sa politique migratoire',
 'Les pays membres adoptent un nouveau protocole sur la libre circulation.',
 'La Communauté économique des États de l''Afrique de l''Ouest a adopté un protocole révisé sur la libre circulation des personnes. Ce texte simplifie les procédures de séjour et de travail pour les ressortissants de la zone.\n\nLe nouveau protocole introduit une carte d''identité biométrique CEDEAO, valable dans les 15 pays membres. Les travailleurs qualifiés pourront obtenir un permis de travail régional en moins de 30 jours.\n\nLes organisations de la société civile saluent cette avancée, tout en appelant à des mesures concrètes pour lutter contre les discriminations que subissent certains migrants dans la région.',
 3, 2, '2026-03-12 15:50:00', 'uploads/art_69c5b4c1e0209.jpeg'),

(51,
 'L''université virtuelle du Sénégal dépasse les 100 000 étudiants',
 'La plateforme d''enseignement à distance connaît une croissance exponentielle.',
 'L''Université Virtuelle du Sénégal (UVS) a franchi le cap des 100 000 étudiants inscrits, faisant d''elle l''une des plus grandes universités numériques d''Afrique.\n\nCréée en 2013, l''UVS propose désormais plus de 30 programmes de licence et master dans des domaines variés : informatique, droit, économie, sciences de l''éducation.\n\nLe modèle sénégalais inspire d''autres pays de la région. La Côte d''Ivoire, le Mali et la Guinée ont entamé des discussions pour créer des partenariats avec l''UVS et développer leurs propres plateformes.',
 4, 1, '2026-03-15 07:45:00', 'uploads/art_69c5b4d1efc71.jpeg'),

(52,
 'Le wolof bientôt enseigné dans toutes les écoles primaires',
 'Le ministère de l''Éducation annonce l''introduction des langues nationales dans le programme officiel.',
 'Le Sénégal franchit un pas historique en intégrant le wolof et cinq autres langues nationales dans le programme scolaire officiel. Dès la rentrée 2027, les élèves du primaire recevront un enseignement bilingue français-langue nationale.\n\nCette réforme, saluée par les linguistes et les pédagogues, vise à réduire l''échec scolaire. Les études montrent que les enfants apprennent mieux lorsqu''ils commencent leur scolarité dans leur langue maternelle.\n\nLe défi principal reste la formation des enseignants. Le ministère prévoit de former 15 000 instituteurs à la pédagogie bilingue d''ici 2027.',
 4, 2, '2026-03-18 12:10:00', 'uploads/art_69c5b4df144a8.jpeg'),

(53,
 'Bourse d''excellence : 500 étudiants sénégalais partiront à l''étranger',
 'Le programme national de bourses finance des études dans les meilleures universités mondiales.',
 'Le gouvernement sénégalais a annoncé l''attribution de 500 bourses d''excellence pour des études de master et doctorat à l''étranger. Les destinations incluent la France, le Canada, le Japon, la Chine et les États-Unis.\n\nLes domaines prioritaires sont l''intelligence artificielle, les énergies renouvelables, la médecine et l''agronomie. Les boursiers s''engagent à revenir travailler au Sénégal pendant au moins trois ans après leurs études.\n\nLe programme, financé à hauteur de 10 milliards de francs CFA, vise à constituer une masse critique de chercheurs et d''experts capables de porter les projets de développement du pays.',
 4, 1, '2026-03-20 09:28:00', 'uploads/art_69c5b5c7e6d34.jpg'),

(55,
 'Le cinéma sénégalais primé à Cannes',
 'La réalisatrice Mati Diop remporte un nouveau prix sur la Croisette.',
 'Le cinéma sénégalais continue de briller sur la scène internationale. Après son Grand Prix en 2019 pour « Atlantique », Mati Diop a de nouveau marqué le Festival de Cannes avec son dernier long-métrage.\n\nLe film, tourné entre Dakar et Saint-Louis, explore les thèmes de la mémoire collective et du patrimoine culturel africain. La critique internationale a salué une œuvre « visuellement époustouflante et intellectuellement stimulante ».\n\nCette reconnaissance confirme le dynamisme du cinéma ouest-africain, porté par une nouvelle génération de réalisateurs formés entre l''Afrique et l''Europe.',
 5, 1, '2026-03-24 11:37:00', 'uploads/art_69c5b5ba711a9.png');
