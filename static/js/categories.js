// Fonction API simplifiée pour éviter les imports
async function apiGet(endpoint) {
  try {
    const response = await fetch(`/Projet-final-PHP/api/${endpoint}`);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Erreur API:', error);
    throw error;
  }
}

async function apiDelete(endpoint, data) {
  try {
    const response = await fetch(`/Projet-final-PHP/api/${endpoint}`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data)
    });
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return await response.json();
  } catch (error) {
    console.error('Erreur API:', error);
    throw error;
  }
}

const categoriesContainer = document.querySelector(".categories-container");
const categoriesGrid = document.getElementById("categoriesGrid");
const loading = document.getElementById("loading");
const errorMessage = document.getElementById("errorMessage");

console.log("categories.js chargé");

function escapeHTML(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function getCategoryIcon(categoryName) {
    const icons = {
        'technologie': '💻',
        'sport': '⚽',
        'politique': '🏛️',
        'éducation': '📚',
        'culture': '🎭'
    };
    
    // Chercher une correspondance insensible à la casse
    const name = categoryName.toLowerCase();
    for (const [key, icon] of Object.entries(icons)) {
        if (name.includes(key)) {
            return icon;
        }
    }
    
    return '📁'; // Icône par défaut
}

function createCategoryCard(category) {
    const card = document.createElement("div");
    card.className = "category-card";
    card.innerHTML = `
        <div class="category-header">
            <div class="category-icon">${getCategoryIcon(category.nom)}</div>
            <h3 class="category-name">${escapeHTML(category.nom)}</h3>
        </div>
        <div class="category-stats">
            <div class="stat-item">
                <span class="stat-number">${category.articles_count || 0}</span>
                <span class="stat-label">articles</span>
            </div>
        </div>
        <div class="category-actions">
            <a href="modifier.php?id=${category.id}" class="btn btn-sm btn-secondary">
                <i class="icon-edit"></i> Modifier
            </a>
            <button class="btn btn-sm btn-danger" onclick="deleteCategory(${category.id}, '${escapeHTML(category.nom)}')">
                <i class="icon-trash"></i> Supprimer
            </button>
        </div>
    `;
    return card;
}

async function getAllCategories() {
    try {
        loading.style.display = 'block';
        errorMessage.style.display = 'none';
        categoriesGrid.innerHTML = '';
        
        const data = await apiGet("categories.php?action=all");
        loading.style.display = 'none';
        
        if (!data || data.length === 0) {
            categoriesGrid.innerHTML = '<p class="no-results">Aucune catégorie trouvée</p>';
            return;
        }
        
        data.forEach(category => {
            categoriesGrid.appendChild(createCategoryCard(category));
        });
        
        console.log(`Chargé ${data.length} catégories`);
    } catch (error) {
        console.error('Erreur:', error);
        loading.style.display = 'none';
        errorMessage.style.display = 'block';
        errorMessage.querySelector('p').textContent = `Erreur: ${error.message}`;
    }
}

async function deleteCategory(categoryId, categoryName) {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${categoryName}" ?`)) {
        return;
    }
    
    try {
        const result = await apiDelete("categories.php", { categoryId });
        console.log("Catégorie supprimée:", result);
        
        // Recharger la liste
        getAllCategories();
        
        // Notification de succès
        showNotification(`Catégorie "${categoryName}" supprimée avec succès`, 'success');
    } catch (error) {
        console.error('Erreur lors de la suppression:', error);
        showNotification('Erreur lors de la suppression: ' + error.message, 'error');
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Charger les catégories au chargement de la page
if (window.location.pathname.includes("categories/liste.php")) {
    getAllCategories();
}

// Rendre la fonction deleteCategory globale pour les boutons
window.deleteCategory = deleteCategory;
