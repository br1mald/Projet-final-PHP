<?php
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
?>

<main>
    <div class="page-header">
        <h1>Créer une catégorie</h1>
        <a href="liste.php" class="btn btn-secondary">
            <i class="icon-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="form-container">
        <form action="../api/categories.php" method="post" class="category-form" id="addCategoryForm">
            <div class="form-group">
                <label for="nom">Nom de la catégorie *</label>
                <input type="text" id="nom" name="nom" placeholder="Ex: Technologie, Sport, Politique..." required>
                <span class="error-message"></span>
                <small class="help-text">Le nom doit contenir au moins 3 caractères</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="icon-plus"></i> Créer la catégorie
                </button>
                <a href="liste.php" class="btn btn-secondary">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</main>

<script type="module" src="../static/js/categories-forms.js"></script>

<?php require_once __DIR__ . "/../footer.php";
?>
