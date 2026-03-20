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
);
