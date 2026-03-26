<?php
/**
 * Données articles — lecture depuis la base de données
 *
 * FICHIER OBSOLÈTE - Plus utilisé depuis la migration vers JavaScript
 * 
 * Toutes les fonctionnalités ont été migrées vers JavaScript :
 * - accueil.php           : utilise apiGet() et fonctions JavaScript
 * - liste_categorie.php   : utilise apiGet() et fonctions JavaScript  
 * - recherche.php         : utilise apiGet() et fonctions JavaScript
 * - detail.php           : utilise apiGet() et fonctions JavaScript
 *
 * Pour les nouvelles fonctionnalités, utiliser directement l'API :
 * - GET /api/articles.php?action=all          : tous les articles
 * - GET /api/articles.php?action=search&id=X  : article par ID
 * - GET /api/articles.php?action=latest       : derniers articles
 * - GET /api/articles.php?action=category&id=X : articles par catégorie
 */

require_once __DIR__ . '/../includes/db.php';

/**
 * Formate une date en français (ex: 15 mars 2026)
 * @deprecated Utiliser la fonction JavaScript formatDateFr() à la place
 */
function dateFormatFr($dateStr)
{
    if (empty($dateStr)) return '';
    $mois = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $ts = strtotime($dateStr);
    return date('j', $ts) . ' ' . $mois[(int)date('n', $ts) - 1] . ' ' . date('Y', $ts);
}
?>
