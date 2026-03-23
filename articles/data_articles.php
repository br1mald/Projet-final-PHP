<?php
/**
 * Données articles — lecture depuis la base de données
 * Adapté pour Projet-final-PHP (colonne image)
 */
require_once __DIR__ . '/../includes/db.php';

/**
 * Récupère un article par son ID
 * @return array|null { id, titre, description, contenu, categorie, auteur, date_publication, image }
 */
function getArticleById($id)
{
    global $pdo;
    $id = (int) $id;
    if ($id <= 0) return null;

    $stmt = $pdo->prepare("
        SELECT a.id, a.titre, a.description, a.contenu,
               a.image AS image,
               a.date_publication,
               c.nom AS categorie,
               CONCAT(u.prenom, ' ', u.nom) AS auteur
        FROM articles a
        JOIN categories c ON a.categorie_id = c.id
        JOIN utilisateurs u ON a.auteur_id = u.id
        WHERE a.id = ?
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return null;

    $row['categorie'] = strtolower($row['categorie']);
    $row['date_publication'] = dateFormatFr($row['date_publication']);
    return $row;
}

/**
 * Récupère les articles, optionnellement filtrés par catégorie
 * @param string $categorie 'toutes' ou nom de catégorie (technologie, sport, etc.)
 * @return array
 */
function getArticles($categorie = 'toutes')
{
    global $pdo;

    $sql = "
        SELECT a.id, a.titre, a.description, a.contenu,
               a.image AS image,
               a.date_publication,
               c.nom AS categorie,
               CONCAT(u.prenom, ' ', u.nom) AS auteur
        FROM articles a
        JOIN categories c ON a.categorie_id = c.id
        JOIN utilisateurs u ON a.auteur_id = u.id
    ";
    $params = [];

    if ($categorie !== 'toutes') {
        $catMap = [
            'education' => 'Éducation', 'technologie' => 'Technologie', 'sport' => 'Sport',
            'politique' => 'Politique', 'culture' => 'Culture', 'international' => 'International',
            'europe' => 'Europe', 'afrique' => 'Afrique', 'amériques' => 'Amériques', 'ameriques' => 'Amériques'
        ];
        $catNom = $catMap[strtolower($categorie)] ?? $categorie;
        $sql .= " WHERE c.nom = ?";
        $params[] = $catNom;
    }

    $sql .= " ORDER BY a.date_publication DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as &$row) {
        $row['categorie'] = strtolower($row['categorie']);
        $row['date_publication'] = dateFormatFr($row['date_publication']);
    }
    return $rows;
}

/**
 * Formate une date en français (ex: 15 mars 2026)
 */
function dateFormatFr($dateStr)
{
    if (empty($dateStr)) return '';
    $mois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $ts = strtotime($dateStr);
    return date('j', $ts) . ' ' . $mois[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
}
