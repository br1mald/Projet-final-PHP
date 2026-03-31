<?php
$pageTitle = "Modifier un utilisateur";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["administrateur"]);

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
if ($id <= 0) {
    header("Location: liste.php");
    exit();
}
?>

<main class="container">
  <div class="page-header">
    <h1>Modifier l'utilisateur</h1>
    <div class="actions">
      <a href="liste.php" class="btn btn-secondary btn-sm">← Retour</a>
    </div>
  </div>

  <div class="form-container" style="max-width:600px;">
    <div id="alertMsg"></div>
    <form id="editUserForm">
      <div class="form-row">
        <div class="form-group">
          <label for="nom">Nom *</label>
          <input id="nom" name="nom" type="text" required minlength="3">
        </div>
        <div class="form-group">
          <label for="prenom">Prénom *</label>
          <input id="prenom" name="prenom" type="text" required minlength="3">
        </div>
      </div>
      <div class="form-group">
        <label for="login">Login *</label>
        <input id="login" name="login" type="text" required minlength="5">
      </div>
      <div class="form-group">
        <label for="mot_de_passe">Nouveau mot de passe</label>
        <input id="mot_de_passe" name="mot_de_passe" type="password" minlength="8" placeholder="Laisser vide pour ne pas changer">
        <small class="help-text">Minimum 8 caractères. Laissez vide pour conserver l'actuel.</small>
      </div>
      <div class="form-group">
        <label for="role">Rôle *</label>
        <select id="role" name="role" required>
          <option value="editeur">Éditeur</option>
          <option value="administrateur">Administrateur</option>
        </select>
      </div>
      <input type="hidden" id="userId" value="<?= $id ?>">
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="liste.php" class="btn btn-secondary">Annuler</a>
      </div>
    </form>
  </div>
</main>

<script type="module">
import { apiGet, apiPatch } from "../static/js/api.js";

const form = document.getElementById("editUserForm");
const alertMsg = document.getElementById("alertMsg");
const userId = document.getElementById("userId").value;

function showAlert(message, isError) {
  alertMsg.textContent = message;
  alertMsg.className = isError ? "error-message" : "help-text";
  alertMsg.style.display = "block";
}

async function loadUser() {
  try {
    const data = await apiGet(`utilisateurs.php?action=search&id=${userId}`);
    document.getElementById("nom").value = data.nom || "";
    document.getElementById("prenom").value = data.prenom || "";
    document.getElementById("login").value = data.login || "";
    document.getElementById("role").value = data.role || "editeur";
  } catch (err) {
    showAlert("Impossible de charger l'utilisateur : " + err.message, true);
  }
}

form.addEventListener("submit", async (e) => {
  e.preventDefault();

  const fields = [
    { attribute: "nom",          value: document.getElementById("nom").value.trim() },
    { attribute: "prenom",       value: document.getElementById("prenom").value.trim() },
    { attribute: "login",        value: document.getElementById("login").value.trim() },
    { attribute: "role",         value: document.getElementById("role").value },
  ];

  const password = document.getElementById("mot_de_passe").value;
  if (password) {
    fields.push({ attribute: "mot_de_passe", value: password });
  }

  try {
    for (const field of fields) {
      await apiPatch("utilisateurs.php", { id: userId, attribute: field.attribute, value: field.value });
    }
    showAlert("Utilisateur modifié avec succès !", false);
    setTimeout(() => (window.location.href = "liste.php"), 1200);
  } catch (err) {
    showAlert("Erreur : " + err.message, true);
  }
});

loadUser();
</script>

<?php require_once __DIR__ . "/../footer.php"; ?>
