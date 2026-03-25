<?php
session_start();
require_once __DIR__ . '/../entete.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../menu.php';

$article_id = $_GET['id'] ?? null;

if (!$article_id || !is_numeric($article_id)) {
    header('Location: /Projet-final-PHP/accueil.php');
    exit;
}

// Récupérer l'article depuis la base de données
try {
    $stmt = $pdo->prepare("SELECT a.*, c.nom as categorie_nom FROM articles a LEFT JOIN categories c ON a.categorie_id = c.id WHERE a.id = ?");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$article) {
        header('Location: /Projet-final-PHP/accueil.php');
        exit;
    }
    
    // Récupérer toutes les catégories
    $stmt_categories = $pdo->query("SELECT * FROM categories ORDER BY nom");
    $categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    error_log("Erreur base de données: " . $e->getMessage());
    header('Location: /Projet-final-PHP/accueil.php');
    exit;
}

$pageTitle = "Modifier l'article : " . htmlspecialchars($article['titre']);
?>

<main class="admin-page">
  <div class="admin-header">
    <h1>Modifier l'article</h1>
    <div class="admin-actions">
      <a href="/Projet-final-PHP/articles/detail.php?id=<?= $article_id ?>" class="btn btn-secondary">Voir l'article</a>
      <a href="/Projet-final-PHP/accueil.php" class="btn btn-secondary">Retour à l'accueil</a>
    </div>
  </div>

  <div class="article-form-container">
    <form id="editArticleForm" class="article-form">
      <input type="hidden" name="id" value="<?= $article_id ?>">
      
      <div class="form-group">
        <label for="titre">Titre de l'article *</label>
        <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($article['titre']) ?>" required minlength="3" class="form-control">
        <div class="form-error" id="titre-error"></div>
      </div>

      <div class="form-group">
        <label for="categorie_id">Catégorie *</label>
        <select id="categorie_id" name="categorie_id" required class="form-control">
          <option value="">Sélectionner une catégorie</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>" <?= $article['categorie_id'] == $category['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($category['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="form-error" id="categorie_id-error"></div>
      </div>

      <div class="form-group">
        <label for="description">Description courte *</label>
        <textarea id="description" name="description" required minlength="10" class="form-control" rows="3"><?= htmlspecialchars($article['description']) ?></textarea>
        <div class="form-error" id="description-error"></div>
      </div>

      <div class="form-group">
        <label for="contenu">Contenu complet *</label>
        <textarea id="contenu" name="contenu" required minlength="20" class="form-control" rows="15"><?= htmlspecialchars($article['contenu']) ?></textarea>
        <div class="form-error" id="contenu-error"></div>
      </div>

      <div class="form-group">
        <label for="date_publication">Date de publication</label>
        <input type="datetime-local" id="date_publication" name="date_publication" value="<?= date('Y-m-d\TH:i', strtotime($article['date_publication'])) ?>" class="form-control">
        <div class="form-error" id="date_publication-error"></div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <button type="button" onclick="history.back()" class="btn btn-secondary">Annuler</button>
      </div>
    </form>
  </div>
</main>

<style>
.article-form-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

.article-form {
  background: #fff;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.form-control:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.form-error {
  color: #dc3545;
  font-size: 0.875rem;
  margin-top: 0.25rem;
  display: none;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  text-decoration: none;
  display: inline-block;
  transition: background-color 0.3s ease;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #545b62;
}

.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.admin-actions {
  display: flex;
  gap: 1rem;
}

.loading {
  opacity: 0.6;
  pointer-events: none;
}

.success-message {
  background: #d4edda;
  color: #155724;
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
  border: 1px solid #c3e6cb;
}

.error-message {
  background: #f8d7da;
  color: #721c24;
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
  border: 1px solid #f5c6cb;
}
</style>

<script>
document.getElementById('editArticleForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  // Nettoyer les erreurs précédentes
  document.querySelectorAll('.form-error').forEach(el => el.style.display = 'none');
  document.querySelectorAll('.error-message, .success-message').forEach(el => el.remove());
  
  const formData = new FormData(this);
  const data = {
    id: formData.get('id'),
    titre: formData.get('titre'),
    categorie_id: formData.get('categorie_id'),
    description: formData.get('description'),
    contenu: formData.get('contenu'),
    date_publication: formData.get('date_publication')
  };
  
  // Ajouter une classe de chargement
  this.classList.add('loading');
  
  try {
    const response = await fetch('/Projet-final-PHP/api/articles.php', {
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
      successDiv.textContent = 'Article modifié avec succès ! Redirection en cours...';
      this.insertBefore(successDiv, this.firstChild);
      
      // Rediriger vers la page de détails après 2 secondes
      setTimeout(() => {
        window.location.href = '/Projet-final-PHP/articles/detail.php?id=' + data.id;
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
  }
});
</script>

<?php require_once __DIR__ . '/../footer.php'; ?>