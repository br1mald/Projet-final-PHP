<?php
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
?>

<main class="utilisateurs-container">
    <div class="page-header">
        <h1>Supprimer un Utilisateur</h1>
        <a href="liste.php" class="btn btn-secondary">
            <i class="icon-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="delete-container">
        <div class="warning-message">
            <i class="icon-warning"></i>
            <h3>Attention</h3>
            <p>La suppression d'un utilisateur est irréversible. Toutes les données associées seront perdues.</p>
        </div>

        <div class="users-list" id="deleteUsersList">
            <!-- Les utilisateurs seront chargés ici via JavaScript -->
        </div>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p>Chargement des utilisateurs...</p>
        </div>

        <div class="error-message" id="errorMessage" style="display: none;">
            <p>Erreur lors du chargement des utilisateurs</p>
        </div>
    </div>
</main>

<script type="module" src="../static/js/utilisateurs-delete.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>