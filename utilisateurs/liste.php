<?php
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
?>

<main class="utilisateurs-container">
    <div class="page-header">
        <h1>Gestion des Utilisateurs</h1>
        <div class="actions">
            <a href="ajouter.php" class="btn btn-primary">
                <i class="icon-plus"></i> Ajouter un utilisateur
            </a>
            <a href="supprimer.php" class="btn btn-danger">
                <i class="icon-trash"></i> Supprimer
            </a>
        </div>
    </div>

    <div class="filters">
        <select id="roleFilter" class="filter-select">
            <option value="">Tous les rôles</option>
            <option value="administrateur">Administrateurs</option>
            <option value="editeur">Éditeurs</option>
        </select>
    </div>

    <div class="users-grid" id="usersGrid">
        <!-- Les utilisateurs seront chargés ici via JavaScript -->
    </div>

    <div class="loading" id="loading">
        <div class="spinner"></div>
        <p>Chargement des utilisateurs...</p>
    </div>

    <div class="error-message" id="errorMessage" style="display: none;">
        <p>Erreur lors du chargement des utilisateurs</p>
    </div>
</main>

<script type="module" src="../static/js/utilisateurs.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>
