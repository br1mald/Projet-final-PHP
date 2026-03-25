<?php
session_start();
require_once __DIR__ . '/../entete.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../menu.php';

// Vérifier si l'utilisateur a le droit d'ajouter un utilisateur
$role = $_SESSION['role'] ?? 'visiteur';
if ($role !== 'administrateur') {
    header('Location: /Projet-final-PHP/accueil.php');
    exit;
}

$pageTitle = "Ajouter un nouvel utilisateur";
?>

<main class="admin-page">
  <div class="admin-header">
    <h1>Ajouter un nouvel utilisateur</h1>
    <div class="admin-actions">
      <a href="/Projet-final-PHP/utilisateurs/liste.php" class="btn btn-secondary">Retour à la liste</a>
      <a href="/Projet-final-PHP/accueil.php" class="btn btn-secondary">Retour à l'accueil</a>
    </div>
  </div>

  <div class="user-form-container">
    <form id="addUserForm" class="user-form">
      <div class="form-section">
        <h3>Informations personnelles</h3>
        
        <div class="form-row">
          <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" required minlength="2" maxlength="50" class="form-control" placeholder="Dupont">
            <div class="form-help">Minimum 2 caractères</div>
            <div class="form-error" id="nom-error"></div>
          </div>

          <div class="form-group">
            <label for="prenom">Prénom *</label>
            <input type="text" id="prenom" name="prenom" required minlength="2" maxlength="50" class="form-control" placeholder="Jean">
            <div class="form-help">Minimum 2 caractères</div>
            <div class="form-error" id="prenom-error"></div>
          </div>
        </div>

        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" name="date" class="form-control">
          <div class="form-help">Optionnel</div>
          <div class="form-error" id="date-error"></div>
        </div>
      </div>

      <div class="form-section">
        <h3>Informations du compte</h3>
        
        <div class="form-row">
          <div class="form-group">
            <label for="login">Login *</label>
            <input type="text" id="login" name="login" required minlength="3" maxlength="50" class="form-control" placeholder="jdupont">
            <div class="form-help">Minimum 3 caractères, unique</div>
            <div class="form-error" id="login-error"></div>
          </div>

          <div class="form-group">
            <label for="role">Rôle *</label>
            <select id="role" name="role" required class="form-control">
              <option value="">Sélectionner un rôle</option>
              <option value="administrateur">Administrateur</option>
              <option value="editeur">Éditeur</option>
              <option value="visiteur">Visiteur</option>
            </select>
            <div class="form-help">Définit les permissions de l'utilisateur</div>
            <div class="form-error" id="role-error"></div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="mot_de_passe">Mot de passe *</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required minlength="6" class="form-control" placeholder="•••••••">
            <div class="form-help">Minimum 6 caractères</div>
            <div class="form-error" id="mot_de_passe-error"></div>
          </div>

          <div class="form-group">
            <label for="confirm_password">Confirmer le mot de passe *</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="6" class="form-control" placeholder="•••••••">
            <div class="form-help">Doit être identique au mot de passe</div>
            <div class="form-error" id="confirm_password-error"></div>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          <span class="btn-text">Créer l'utilisateur</span>
          <span class="btn-loading" style="display: none;">Création en cours...</span>
        </button>
        <button type="button" onclick="history.back()" class="btn btn-outline">Annuler</button>
      </div>
    </form>
  </div>
</main>

<style>
.user-form-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

.user-form {
  background: #fff;
  padding: 2.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  border: 1px solid var(--border-light);
}

.form-section {
  margin-bottom: 2.5rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid var(--border-light);
}

.form-section:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.form-section h3 {
  color: var(--text);
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid var(--accent);
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: var(--text);
  font-size: 0.95rem;
}

.form-control {
  width: 100%;
  padding: 0.875rem;
  border: 2px solid var(--border-light);
  border-radius: 8px;
  font-size: 1rem;
  font-family: var(--font-body);
  transition: all 0.3s ease;
  background: #fff;
}

.form-control:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 3px rgba(187, 249, 252, 0.2);
}

.form-control:hover {
  border-color: #d0d0d0;
}

.form-help {
  font-size: 0.8rem;
  color: var(--muted);
  margin-top: 0.25rem;
  font-style: italic;
}

.form-error {
  color: var(--error);
  font-size: 0.875rem;
  margin-top: 0.5rem;
  display: none;
  font-weight: 500;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2.5rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-light);
}

.btn {
  padding: 0.875rem 1.75rem;
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
  position: relative;
}

.btn-primary {
  background: var(--accent);
  color: var(--primary);
}

.btn-primary:hover {
  background: var(--accent-dark);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(187, 249, 252, 0.3);
}

.btn-outline {
  background: transparent;
  color: var(--muted);
  border: 2px solid var(--border-light);
}

.btn-outline:hover {
  background: var(--border-light);
  color: var(--text);
}

.loading .btn-text {
  display: none;
}

.loading {
  opacity: 0.7;
  pointer-events: none;
}

.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-radius: 12px;
  border: 1px solid var(--border-light);
}

.admin-header h1 {
  color: var(--text);
  font-size: 1.8rem;
  font-weight: 700;
}

.admin-actions {
  display: flex;
  gap: 0.75rem;
}

.success-message {
  background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
  color: #155724;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  border: 1px solid #c3e6cb;
  font-weight: 500;
}

.error-message {
  background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
  color: #721c24;
  padding: 1rem 1.5rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  border: 1px solid #f5c6cb;
  font-weight: 500;
}

@media (max-width: 768px) {
  .form-row {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .user-form {
    padding: 1.5rem;
  }
  
  .admin-header {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .form-actions {
    flex-direction: column;
  }
  
  .btn {
    width: 100%;
    justify-content: center;
  }
}
</style>

<script>
document.getElementById('addUserForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  // Nettoyer les erreurs précédentes
  document.querySelectorAll('.form-error').forEach(el => el.style.display = 'none');
  document.querySelectorAll('.error-message, .success-message').forEach(el => el.remove());
  
  const formData = new FormData(this);
  const data = {
    nom: formData.get('nom'),
    prenom: formData.get('prenom'),
    date: formData.get('date'),
    login: formData.get('login'),
    mot_de_passe: formData.get('mot_de_passe'),
    confirm_password: formData.get('confirm_password'),
    role: formData.get('role')
  };
  
  // Validation client
  const errors = {};
  if (!data.nom || data.nom.length < 2) errors.nom = "Nom requis (min 2 caractères)";
  if (!data.prenom || data.prenom.length < 2) errors.prenom = "Prénom requis (min 2 caractères)";
  if (!data.login || data.login.length < 3) errors.login = "Login requis (min 3 caractères)";
  if (!data.mot_de_passe || data.mot_de_passe.length < 6) errors.mot_de_passe = "Mot de passe requis (min 6 caractères)";
  if (data.mot_de_passe !== data.confirm_password) errors.confirm_password = "Les mots de passe ne correspondent pas";
  if (!data.role) errors.role = "Rôle requis";
  
  if (Object.keys(errors).length > 0) {
    Object.keys(errors).forEach(field => {
      const errorElement = document.getElementById(field + '-error');
      if (errorElement) {
        errorElement.textContent = errors[field];
        errorElement.style.display = 'block';
      }
    });
    return;
  }
  
  // Ajouter une classe de chargement
  this.classList.add('loading');
  document.querySelector('.btn-text').style.display = 'none';
  document.querySelector('.btn-loading').style.display = 'inline';
  
  try {
    const response = await fetch('/Projet-final-PHP/api/utilisateurs.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (response.ok && result.ok) {
      // Succès
      const successDiv = document.createElement('div');
      successDiv.className = 'success-message';
      successDiv.textContent = 'Utilisateur créé avec succès ! Redirection en cours...';
      this.insertBefore(successDiv, this.firstChild);
      
      // Rediriger vers la liste après 2 secondes
      setTimeout(() => {
        window.location.href = '/Projet-final-PHP/utilisateurs/liste.php';
      }, 2000);
    } else {
      // Erreurs de validation
      if (result.errors) {
        Object.keys(result.errors).forEach(field => {
          const errorElement = document.getElementById(field + '-error');
          if (errorElement) {
            errorElement.textContent = result.errors[field];
            errorElement.style.display = 'block';
          }
        });
      } else {
        // Erreur générale
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = result.error || 'Une erreur est survenue lors de la création.';
        this.insertBefore(errorDiv, this.firstChild);
      }
    }
  } catch (error) {
    console.error('Erreur:', error);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = 'Erreur de connexion. Veuillez réessayer.';
    this.insertBefore(errorDiv, this.firstChild);
  } finally {
    this.classList.remove('loading');
    document.querySelector('.btn-text').style.display = 'inline';
    document.querySelector('.btn-loading').style.display = 'none';
  }
});
</script>

<?php require_once __DIR__ . '/../footer.php'; ?>
