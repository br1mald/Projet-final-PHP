<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['role'])) {
    header('Location: ../connexion.php');
    exit;
}

// Vérifier si l'utilisateur est administrateur
$userRole = $_SESSION['role'] ?? 'visiteur';
if (!in_array($userRole, ['administrateur'])) {
    header('Location: ../accueil.php');
    exit;
}

// Récupérer l'ID de l'utilisateur
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: liste.php');
    exit;
}

$pageTitle = "Modifier l'utilisateur";
require_once __DIR__ . '/../entete.php';
?>

<main class="container">
  <div class="page-header">
    <h1>Modifier l'utilisateur</h1>
    <div class="breadcrumb">
      <a href="../accueil.php">Accueil</a> &gt; 
      <a href="liste.php">Gestion des utilisateurs</a> &gt; 
      <span>Modifier</span>
    </div>
  </div>

  <div class="admin-form-container">
    <form id="editUserForm" class="form-card">
      <div class="form-header">
        <h2>Modifier l'utilisateur</h2>
        <p>Modifiez les informations de l'utilisateur ci-dessous.</p>
      </div>

      <div class="form-body">
        <div class="form-row">
          <div class="form-group">
            <label for="nom" class="form-label">Nom *</label>
            <input 
              type="text" 
              id="nom" 
              name="nom" 
              class="form-input" 
              required
              minlength="2"
              maxlength="50"
              placeholder="Nom de famille"
            >
          </div>

          <div class="form-group">
            <label for="prenom" class="form-label">Prénom *</label>
            <input 
              type="text" 
              id="prenom" 
              name="prenom" 
              class="form-input" 
              required
              minlength="2"
              maxlength="50"
              placeholder="Prénom"
            >
          </div>
        </div>

        <div class="form-group">
          <label for="login" class="form-label">Login *</label>
          <input 
            type="text" 
            id="login" 
            name="login" 
            class="form-input" 
            required
            minlength="3"
            maxlength="50"
            placeholder="Identifiant de connexion"
          >
          <small class="form-help">L'identifiant unique pour la connexion.</small>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="mot_de_passe" class="form-label">Mot de passe</label>
            <input 
              type="password" 
              id="mot_de_passe" 
              name="mot_de_passe" 
              class="form-input"
              minlength="6"
              maxlength="255"
              placeholder="Laisser vide pour ne pas modifier"
            >
            <small class="form-help">Laissez vide pour conserver le mot de passe actuel.</small>
          </div>

          <div class="form-group">
            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
            <input 
              type="password" 
              id="confirm_password" 
              name="confirm_password" 
              class="form-input"
              placeholder="Confirmer le nouveau mot de passe"
            >
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="role" class="form-label">Rôle *</label>
            <select id="role" name="role" class="form-select" required>
              <option value="visiteur">Visiteur</option>
              <option value="editeur">Éditeur</option>
              <option value="administrateur">Administrateur</option>
            </select>
            <small class="form-help">Définit les permissions de l'utilisateur.</small>
          </div>

          <div class="form-group">
            <label for="date" class="form-label">Date de naissance</label>
            <input 
              type="date" 
              id="date" 
              name="date" 
              class="form-input"
              max="<?php echo date('Y-m-d'); ?>"
            >
            <small class="form-help">Optionnel : Date de naissance.</small>
          </div>
        </div>

        <input type="hidden" id="userId" name="id" value="<?= $id ?>">
      </div>

      <div class="form-footer">
        <button type="submit" class="btn btn-primary">
          <i class="icon-save"></i> Enregistrer les modifications
        </button>
        <a href="liste.php" class="btn btn-secondary">
          <i class="icon-cancel"></i> Annuler
        </a>
      </div>
    </form>
  </div>
</main>

<style>
.admin-form-container {
  max-width: 800px;
  margin: 2rem auto;
}

.form-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  overflow: hidden;
}

.form-header {
  padding: 2rem;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-bottom: 1px solid var(--border-light);
}

.form-header h2 {
  color: var(--text);
  margin-bottom: 0.5rem;
}

.form-header p {
  color: var(--muted);
  margin: 0;
}

.form-body {
  padding: 2rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  color: var(--text);
  font-weight: 600;
  margin-bottom: 0.5rem;
}

.form-input, .form-select {
  width: 100%;
  padding: 0.875rem;
  border: 2px solid var(--border-light);
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #fff;
}

.form-input:focus, .form-select:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(187, 249, 252, 0.2);
}

.form-input.error, .form-select.error {
  border-color: #dc3545;
}

.form-help {
  display: block;
  color: var(--muted);
  font-size: 0.85rem;
  margin-top: 0.5rem;
}

.form-footer {
  padding: 2rem;
  background: #f8f9fa;
  border-top: 1px solid var(--border-light);
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.btn {
  padding: 0.875rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
}

.btn-primary {
  background: var(--accent);
  color: var(--primary);
}

.btn-primary:hover {
  background: var(--accent-dark);
  transform: translateY(-1px);
}

.btn-secondary {
  background: var(--border-light);
  color: var(--text);
}

.btn-secondary:hover {
  background: #e0e0e0;
}

.alert {
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.alert-success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.alert-error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.loading {
  opacity: 0.6;
  pointer-events: none;
}

.page-header {
  margin-bottom: 2rem;
}

.page-header h1 {
  color: var(--text);
  margin-bottom: 0.5rem;
}

.breadcrumb {
  color: var(--muted);
  font-size: 0.9rem;
}

.breadcrumb a {
  color: var(--accent);
  text-decoration: none;
}

.breadcrumb a:hover {
  text-decoration: underline;
}

@media (max-width: 768px) {
  .admin-form-container {
    margin: 1rem;
  }
  
  .form-row {
    grid-template-columns: 1fr;
    gap: 0;
  }
  
  .form-footer {
    flex-direction: column;
  }
  
  .btn {
    width: 100%;
    justify-content: center;
  }
}
</style>

<script>
// Fonction API simplifiée
async function apiRequest(method, endpoint, data = null) {
  try {
    const options = {
      method: method,
      headers: {
        'Content-Type': 'application/json',
      }
    };

    if (data) {
      options.body = JSON.stringify(data);
    }

    const response = await fetch(`/Projet-final-PHP/api/${endpoint}`, options);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return await response.json();
  } catch (error) {
    console.error('Erreur API:', error);
    throw error;
  }
}

// Charger les données de l'utilisateur
async function loadUser() {
  try {
    const userId = document.getElementById('userId').value;
    const data = await apiRequest('GET', `utilisateurs.php?action=search&id=${userId}`);
    
    if (data.error) {
      throw new Error(data.error);
    }
    
    // Remplir le formulaire
    document.getElementById('nom').value = data.nom || '';
    document.getElementById('prenom').value = data.prenom || '';
    document.getElementById('login').value = data.login || '';
    document.getElementById('role').value = data.role || 'visiteur';
    document.getElementById('date').value = data.date || '';
    
  } catch (error) {
    console.error('Erreur lors du chargement:', error);
    showAlert('Erreur lors du chargement de l\'utilisateur: ' + error.message, 'error');
  }
}

// Afficher une alerte
function showAlert(message, type = 'success') {
  const existingAlert = document.querySelector('.alert');
  if (existingAlert) {
    existingAlert.remove();
  }

  const alert = document.createElement('div');
  alert.className = `alert alert-${type}`;
  alert.textContent = message;
  
  const form = document.getElementById('editUserForm');
  form.parentNode.insertBefore(alert, form);
  
  // Auto-suppression après 5 secondes
  setTimeout(() => {
    if (alert.parentNode) {
      alert.remove();
    }
  }, 5000);
}

// Valider le formulaire
function validateForm() {
  const nom = document.getElementById('nom').value.trim();
  const prenom = document.getElementById('prenom').value.trim();
  const login = document.getElementById('login').value.trim();
  const motDePasse = document.getElementById('mot_de_passe').value;
  const confirmPassword = document.getElementById('confirm_password').value;
  const role = document.getElementById('role').value;
  const errors = [];

  // Validation du nom
  if (!nom) {
    errors.push('Le nom est obligatoire');
  } else if (nom.length < 2) {
    errors.push('Le nom doit contenir au moins 2 caractères');
  } else if (nom.length > 50) {
    errors.push('Le nom ne peut pas dépasser 50 caractères');
  }

  // Validation du prénom
  if (!prenom) {
    errors.push('Le prénom est obligatoire');
  } else if (prenom.length < 2) {
    errors.push('Le prénom doit contenir au moins 2 caractères');
  } else if (prenom.length > 50) {
    errors.push('Le prénom ne peut pas dépasser 50 caractères');
  }

  // Validation du login
  if (!login) {
    errors.push('Le login est obligatoire');
  } else if (login.length < 3) {
    errors.push('Le login doit contenir au moins 3 caractères');
  } else if (login.length > 50) {
    errors.push('Le login ne peut pas dépasser 50 caractères');
  }

  // Validation du mot de passe (seulement si fourni)
  if (motDePasse) {
    if (motDePasse.length < 6) {
      errors.push('Le mot de passe doit contenir au moins 6 caractères');
    } else if (motDePasse !== confirmPassword) {
      errors.push('Les mots de passe ne correspondent pas');
    }
  }

  // Afficher les erreurs
  if (errors.length > 0) {
    showAlert(errors.join(', '), 'error');
    return false;
  }

  return true;
}

// Soumettre le formulaire
document.getElementById('editUserForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  if (!validateForm()) {
    return;
  }

  const formData = {
    id: document.getElementById('userId').value,
    nom: document.getElementById('nom').value.trim(),
    prenom: document.getElementById('prenom').value.trim(),
    login: document.getElementById('login').value.trim(),
    role: document.getElementById('role').value,
    date: document.getElementById('date').value
  };

  // Ajouter le mot de passe seulement s'il est fourni
  const motDePasse = document.getElementById('mot_de_passe').value;
  if (motDePasse) {
    formData.mot_de_passe = motDePasse;
    formData.confirm_password = document.getElementById('confirm_password').value;
  }

  try {
    // Ajouter l'indicateur de chargement
    const form = document.getElementById('editUserForm');
    form.classList.add('loading');

    const result = await apiRequest('PUT', 'utilisateurs.php', formData);

    if (result.error) {
      throw new Error(result.error);
    }

    if (result.ok) {
      showAlert('Utilisateur modifié avec succès !', 'success');
      
      // Rediriger après 2 secondes
      setTimeout(() => {
        window.location.href = 'liste.php';
      }, 2000);
    } else {
      throw new Error('Réponse inattendue du serveur');
    }

  } catch (error) {
    console.error('Erreur lors de la modification:', error);
    showAlert('Erreur lors de la modification: ' + error.message, 'error');
  } finally {
    // Retirer l'indicateur de chargement
    form.classList.remove('loading');
  }
});

// Charger les données au chargement de la page
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', loadUser);
} else {
  loadUser();
}
</script>

<?php require_once __DIR__ . '/../footer.php'; ?>
