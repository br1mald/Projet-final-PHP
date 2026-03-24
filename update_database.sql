-- Script de mise à jour pour base de données existante
-- Ce script ajoute les dates de publication et les images aux articles existants

-- Vérifier si la colonne image existe déjà, sinon l'ajouter
SET @columnExists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = 'actualites'
    AND TABLE_NAME = 'articles'
    AND COLUMN_NAME = 'image'
);

SET @sql = IF(@columnExists = 0, 
    'ALTER TABLE articles ADD COLUMN image VARCHAR(255) DEFAULT NULL',
    'SELECT "La colonne image existe déjà" AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Mettre à jour les articles avec des dates de publication et des images
UPDATE articles SET 
    date_publication = '2024-01-15 09:30:00',
    image = 'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=800'
WHERE titre = 'L''intelligence artificielle en Afrique';

UPDATE articles SET 
    date_publication = '2024-01-20 14:15:00',
    image = 'https://media.ouest-france.fr/v1/pictures/MjAyNDAxZGEzMmFkY2Y3MDg3MjMxN2MwNWM4NzMzYzUyOThhYTI?width=1260&height=708&focuspoint=50%2C25&cropresize=1&client_id=bpeditorial&sign=27f531a40f92e471abb9c0e246b3fe36fbbe312e9a8e93632ca9efce775fe9f4'
WHERE titre = 'CAN 2026 : les favoris';

UPDATE articles SET 
    date_publication = '2024-01-25 11:00:00',
    image = 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=800'
WHERE titre = 'Réforme éducative au Sénégal';

UPDATE articles SET 
    date_publication = '2024-02-01 16:45:00',
    image = 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=800'
WHERE titre = 'Startups africaines : un écosystème en pleine expansion';

UPDATE articles SET 
    date_publication = '2024-02-05 10:30:00',
    image = 'https://images.unsplash.com/photo-1517466787929-bc90951d0974?w=800'
WHERE titre = 'Football : les Lions prêts pour la CAN';

UPDATE articles SET 
    date_publication = '2024-02-10 13:20:00',
    image = 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800'
WHERE titre = 'Élections locales 2026 : les enjeux';

UPDATE articles SET 
    date_publication = '2024-02-15 18:00:00',
    image = 'https://images.unsplash.com/photo-1503095396548-807759245b35?w=800'
WHERE titre = 'Festival de théâtre de Dakar : la programmation dévoilée';

UPDATE articles SET 
    date_publication = '2024-02-20 09:15:00',
    image = 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=800'
WHERE titre = 'Energies renouvelables : le Sénégal mise sur le solaire';

UPDATE articles SET 
    date_publication = '2024-02-25 20:30:00',
    image = 'https://images.unsplash.com/photo-1546519638-68e109498ffc?w=800'
WHERE titre = 'Basket-ball : l''AS Douanes championne';

UPDATE articles SET 
    date_publication = '2024-03-01 12:00:00',
    image = 'https://images.unsplash.com/photo-1586953208448-b95a79798f07?w=800'
WHERE titre = 'Budget 2026 : les priorités du gouvernement';

UPDATE articles SET 
    date_publication = '2024-03-05 15:45:00',
    image = 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=800'
WHERE titre = 'Formation des enseignants : un programme national';

UPDATE articles SET 
    date_publication = '2024-03-10 19:30:00',
    image = 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?w=800'
WHERE titre = 'Musique : le Mbalax célébré à Gorée';

UPDATE articles SET 
    date_publication = '2024-03-15 08:00:00',
    image = 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800'
WHERE titre = 'Télémédecine : une solution pour les zones rurales';

-- Pour les articles qui n'ont pas de date de publication, mettre une date par défaut
UPDATE articles SET date_publication = NOW() WHERE date_publication IS NULL;

-- Afficher un résumé des mises à jour
SELECT 
    COUNT(*) as total_articles,
    COUNT(CASE WHEN image IS NOT NULL THEN 1 END) as articles_avec_image,
    COUNT(CASE WHEN date_publication IS NOT NULL THEN 1 END) as articles_avec_date
FROM articles;
