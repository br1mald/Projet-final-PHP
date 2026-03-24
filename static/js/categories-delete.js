import { apiGet, apiDelete } from "./api.js";

const deleteCategoriesList = document.getElementById("deleteCategoriesList");
const loading = document.getElementById("loading");
const errorMessage = document.getElementById("errorMessage");

console.log("categories-delete.js chargé");

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

function getCategoryColor(index) {
    const colors = [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7',
        '#DDA0DD', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2'
    ];
    return colors[index % colors.length];
}

function createDeleteCategoryCard(category, index) {
    const card = document.createElement("div");
    card.className = "delete-category-card";
    card.innerHTML = `
        <div class="category-info">
            <div class="category-icon" style="background-color: ${getCategoryColor(index)};">
                ${getCategoryIcon(category.nom)}
            </div>
            <div class="category-details">
                <h4 class="category-name">${escapeHTML(category.nom)}</h4>
                <div class="category-stats">
                    <span class="stat-item">
                        <i class="icon-doc"></i>
                        <span class="stat-number">${category.articles_count || 0}</span>
                        articles
                    </span>
                </div>
            </div>
        </div>
        <div class="delete-actions">
            <button class="btn btn-danger" onclick="confirmDeleteCategory(${category.id}, '${escapeHTML(category.nom)}')">
                <i class="icon-trash"></i> Supprimer
            </button>
        </div>
    `;
    return card;
}

async function loadCategoriesForDeletion() {
    try {
        loading.style.display = 'block';
        errorMessage.style.display = 'none';
        deleteCategoriesList.innerHTML = '';
        
        const data = await apiGet("categories.php?action=all");
        loading.style.display = 'none';
        
        if (!data || data.length === 0) {
            deleteCategoriesList.innerHTML = '<p class="no-results">Aucune catégorie trouvée</p>';
            return;
        }
        
        data.forEach((category, index) => {
            deleteCategoriesList.appendChild(createDeleteCategoryCard(category, index));
        });
        
        console.log(`Chargé ${data.length} catégories pour suppression`);
    } catch (error) {
        console.error('Erreur:', error);
        loading.style.display = 'none';
        errorMessage.style.display = 'block';
        errorMessage.querySelector('p').textContent = `Erreur: ${error.message}`;
    }
}

async function confirmDeleteCategory(categoryId, categoryName) {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${categoryName}" ?\n\nCette action est irréversible et les articles associés devront être reclassés.`)) {
        return;
    }
    
    try {
        const result = await apiDelete("categories.php", { categoryId });
        console.log("Catégorie supprimée:", result);
        
        // Notification de succès
        showNotification(`Catégorie "${categoryName}" supprimée avec succès`, 'success');
        
        // Recharger la liste
        setTimeout(() => {
            loadCategoriesForDeletion();
        }, 1000);
        
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
if (window.location.pathname.includes("categories/supprimer.php")) {
    loadCategoriesForDeletion();
}

// Rendre la fonction confirmDeleteCategory globale pour les boutons
window.confirmDeleteCategory = confirmDeleteCategory;
