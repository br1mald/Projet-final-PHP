<?php
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
?>

<main class="categories-container">
    <div class="page-header">
        <h1>Gestion des Catégories</h1>
        <div class="actions">
            <a href="ajouter.php" class="btn btn-primary">
                <i class="icon-plus"></i> Ajouter une catégorie
            </a>
            <a href="supprimer.php" class="btn btn-danger">
                <i class="icon-trash"></i> Supprimer
            </a>
        </div>
    </div>

    <div class="categories-grid" id="categoriesGrid">
        <!-- Les catégories seront chargées ici via JavaScript -->
    </div>

    <div class="loading" id="loading">
        <div class="spinner"></div>
        <p>Chargement des catégories...</p>
    </div>

    <div class="error-message" id="errorMessage" style="display: none;">
        <p>Erreur lors du chargement des catégories</p>
    </div>
</main>

<script type="module" src="../static/js/categories.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>
