<?php
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
?>

<main class="categories-container">
    <div class="page-header">
        <h1>Supprimer une Catégorie</h1>
        <a href="liste.php" class="btn btn-secondary">
            <i class="icon-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="delete-container">
        <div class="warning-message">
            <i class="icon-warning"></i>
            <h3>Attention</h3>
            <p>La suppression d'une catégorie est irréversible. Les articles associés devront être reclassés.</p>
        </div>

        <div class="categories-list" id="deleteCategoriesList">
            <!-- Les catégories seront chargées ici via JavaScript -->
        </div>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Chargement des catégories...</p>
        </div>

        <div class="error-message" id="errorMessage" style="display: none;">
            <p>Erreur lors du chargement des catégories</p>
        </div>
    </div>
</main>

<script type="module" src="../static/js/categories-delete.js"></script>

<?php require_once __DIR__ . "/../footer.php";
?>
