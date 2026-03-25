<?php
session_start();
require_once __DIR__ . '/../entete.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../menu.php';

// Vérifier si l'utilisateur a le droit de modifier une catégorie
$role = $_SESSION['role'] ?? 'visiteur';
if ($role !== 'administrateur') {
    header('Location: /Projet-final-PHP/accueil.php');
    exit;
}

$category_id = $_GET['id'] ?? null;

if (!$category_id || !is_numeric($category_id)) {
    header('Location: /Projet-final-PHP/categories/liste.php');
    exit;
}

// Récupérer la catégorie depuis la base de données
try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        header('Location: /Projet-final-PHP/categories/liste.php');
        exit;
    }
    
} catch (PDOException $e) {
    error_log("Erreur base de données: " . $e->getMessage());
    header('Location: /Projet-final-PHP/categories/liste.php');
    exit;
}

$pageTitle = "Modifier la catégorie : " . htmlspecialchars($category['nom']);
?>

<main class="admin-page">
  <div class="admin-header">
    <h1>Modifier la catégorie</h1>
    <div class="admin-actions">
      <a href="/Projet-final-PHP/categories/liste.php" class="btn btn-secondary">Retour à la liste</a>
      <a href="/Projet-final-PHP/accueil.php" class="btn btn-secondary">Retour à l'accueil</a>
    </div>
  </div>

  <div class="category-form-container">
    <form id="editCategoryForm" class="category-form">
      <input type="hidden" name="id" value="<?= $category_id ?>">
      
      <div class="form-section">
        <h3>Informations de la catégorie</h3>
        
        <div class="form-group">
          <label for="nom">Nom de la catégorie *</label>
          <input type="text" id="nom" name="nom" required minlength="3" maxlength="100" class="form-control" value="<?= htmlspecialchars($category['nom']) ?>" placeholder="Nom de la catégorie">
          <div class="form-help">Minimum 3 caractères, maximum 100</div>
          <div class="form-error" id="nom-error"></div>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          <span class="btn-text">Enregistrer les modifications</span>
          <span class="btn-loading" style="display: none;">Modification en cours...</span>
        </button>
        <button type="button" onclick="history.back()" class="btn btn-outline">Annuler</button>
      </div>
    </form>
  </div>
</main>

<style>
.category-form-container {
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
}

.category-form {
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
  .category-form {
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
document.getElementById('editCategoryForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  // Nettoyer les erreurs précédentes
  document.querySelectorAll('.form-error').forEach(el => el.style.display = 'none');
  document.querySelectorAll('.error-message, .success-message').forEach(el => el.remove());
  
  const formData = new FormData(this);
  const data = {
    id: formData.get('id'),
    nom: formData.get('nom')
  };
  
  // Validation client
  const errors = {};
  if (!data.nom || data.nom.length < 3) errors.nom = "Nom requis (min 3 caractères)";
  if (data.nom.length > 100) errors.nom = "Nom trop long (max 100 caractères)";
  
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
    const response = await fetch('/Projet-final-PHP/api/categories.php', {
      method: 'PUT',
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
      successDiv.textContent = 'Catégorie modifiée avec succès ! Redirection en cours...';
      this.insertBefore(successDiv, this.firstChild);
      
      // Rediriger vers la liste après 2 secondes
      setTimeout(() => {
        window.location.href = '/Projet-final-PHP/categories/liste.php';
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
        errorDiv.textContent = result.error || 'Une erreur est survenue lors de la modification.';
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