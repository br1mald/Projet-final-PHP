<?php
$pageTitle = "Modifier une catégorie";
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../includes/auth.php";

$role = get_role();
check_role($role, ["editeur", "administrateur"]);

// Redirect back to list if no valid ID is provided
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
  header("Location: liste.php");
  exit;
}
?>

<main class="container">
  <div class="page-header">
    <h1>Modifier la catégorie</h1>
    <div class="actions">
      <a href="liste.php" class="btn btn-secondary btn-sm">← Retour</a>
    </div>
  </div>

  <div class="form-container" style="max-width:500px;">
    <div id="alertMsg"></div>
    <form id="editCategoryForm">
      <div class="form-group">
        <label for="nom">Nom *</label>
        <input id="nom" name="nom" type="text" placeholder="Nom de la catégorie" required minlength="3">
        <small class="help-text">Minimum 3 caractères.</small>
      </div>
      <input type="hidden" id="categoryId" value="<?= $id ?>">
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="liste.php" class="btn btn-secondary">Annuler</a>
      </div>
    </form>
  </div>
</main>

<script type="module">
import { apiGet, apiPatch } from "../static/js/api.js";

const form = document.getElementById("editCategoryForm");
const nomInput = document.getElementById("nom");
const alertMsg = document.getElementById("alertMsg");
const categoryId = document.getElementById("categoryId").value;

// Show a coloured message above the form
function showAlert(message, isError) {
  alertMsg.textContent = message;
  alertMsg.className = isError ? "error-message" : "help-text";
  alertMsg.style.display = "block";
}

// Load the current category name and pre-fill the input
async function loadCategory() {
  try {
    const data = await apiGet(`categories.php?action=search&id=${categoryId}`);
    nomInput.value = data.nom || "";
  } catch (err) {
    showAlert("Impossible de charger la catégorie : " + err.message, true);
  }
}

// Submit: PATCH the category name
form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const nom = nomInput.value.trim();
  if (nom.length < 3) {
    showAlert("Le nom doit contenir au moins 3 caractères.", true);
    return;
  }
  try {
    await apiPatch("categories.php", { id: categoryId, value: nom });
    showAlert("Catégorie modifiée avec succès !", false);
    setTimeout(() => (window.location.href = "liste.php"), 1200);
  } catch (err) {
    showAlert("Erreur : " + err.message, true);
  }
});

loadCategory();
</script>

<?php require_once __DIR__ . "/../footer.php"; ?>

