<?php
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";
?>

<main class="utilisateurs-container">
    <div class="page-header">
        <h1>Ajouter un Utilisateur</h1>
        <a href="liste.php" class="btn btn-secondary">
            <i class="icon-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="form-container">
        <form action="../api/utilisateurs.php" method="post" class="user-form" id="addUserForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="nom">Nom *</label>
                    <input type="text" id="nom" name="nom" required>
                    <span class="error-message"></span>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom *</label>
                    <input type="text" id="prenom" name="prenom" required>
                    <span class="error-message"></span>
                </div>
            </div>

            <div class="form-group">
                <label for="login">Login *</label>
                <input type="text" id="login" name="login" required>
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe *</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <span class="error-message"></span>
            </div>

            <div class="form-group">
                <label for="role">Rôle *</label>
                <select id="role" name="role" required>
                    <option value="">Sélectionner un rôle</option>
                    <option value="administrateur">Administrateur</option>
                    <option value="editeur">Éditeur</option>
                </select>
                <span class="error-message"></span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="icon-plus"></i> Ajouter l'utilisateur
                </button>
                <a href="liste.php" class="btn btn-secondary">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</main>

<script type="module" src="../static/js/utilisateurs-forms.js"></script>

<?php require_once __DIR__ . "/../footer.php"; ?>