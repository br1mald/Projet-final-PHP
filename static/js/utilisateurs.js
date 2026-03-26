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

const usersContainer = document.querySelector(".utilisateurs-container");
const usersGrid = document.getElementById("usersGrid");
const loading = document.getElementById("loading");
const errorMessage = document.getElementById("errorMessage");
const roleFilter = document.getElementById("roleFilter");

console.log("utilisateurs.js chargé");

function escapeHTML(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return 'Date inconnue';
    const d = new Date(dateStr);
    return d.toLocaleDateString('fr-FR', { 
        day: '2-digit', 
        month: 'long', 
        year: 'numeric' 
    });
}

function getRoleBadge(role) {
    const badges = {
        'administrateur': '<span class="badge badge-admin">Administrateur</span>',
        'editeur': '<span class="badge badge-editor">Éditeur</span>'
    };
    return badges[role] || '<span class="badge badge-default">Visiteur</span>';
}

function createUserCard(user) {
    const card = document.createElement("div");
    card.className = "user-card";
    card.innerHTML = `
        <div class="user-avatar">
            <div class="avatar-placeholder">
                ${user.prenom ? user.prenom.charAt(0).toUpperCase() : 'U'}${user.nom ? user.nom.charAt(0).toUpperCase() : ''}
            </div>
        </div>
        <div class="user-info">
            <h3 class="user-name">${escapeHTML(user.prenom || '')} ${escapeHTML(user.nom || '')}</h3>
            <p class="user-login">@${escapeHTML(user.login || '')}</p>
            <div class="user-role">${getRoleBadge(user.role)}</div>
        </div>
        <div class="user-actions">
            <a href="modifier.php?id=${user.id}" class="btn btn-sm btn-secondary">
                <i class="icon-edit"></i> Modifier
            </a>
            <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">
                <i class="icon-trash"></i> Supprimer
            </button>
        </div>
    `;
    return card;
}

async function getAllUsers() {
    try {
        loading.style.display = 'block';
        errorMessage.style.display = 'none';
        usersGrid.innerHTML = '';
        
        const data = await apiGet("utilisateurs.php?action=all");
        loading.style.display = 'none';
        
        if (!data || data.length === 0) {
            usersGrid.innerHTML = '<p class="no-results">Aucun utilisateur trouvé</p>';
            return;
        }
        
        data.forEach(user => {
            usersGrid.appendChild(createUserCard(user));
        });
        
        console.log(`Chargé ${data.length} utilisateurs`);
    } catch (error) {
        console.error('Erreur:', error);
        loading.style.display = 'none';
        errorMessage.style.display = 'block';
        errorMessage.querySelector('p').textContent = `Erreur: ${error.message}`;
    }
}

async function filterUsers(role) {
    try {
        loading.style.display = 'block';
        errorMessage.style.display = 'none';
        usersGrid.innerHTML = '';
        
        const endpoint = role ? `utilisateurs.php?action=role-filter&role=${role}` : "utilisateurs.php?action=all";
        const data = await apiGet(endpoint);
        loading.style.display = 'none';
        
        if (!data || data.length === 0) {
            usersGrid.innerHTML = '<p class="no-results">Aucun utilisateur trouvé pour ce filtre</p>';
            return;
        }
        
        data.forEach(user => {
            usersGrid.appendChild(createUserCard(user));
        });
        
        console.log(`Chargé ${data.length} utilisateurs pour le rôle: ${role || 'tous'}`);
    } catch (error) {
        console.error('Erreur:', error);
        loading.style.display = 'none';
        errorMessage.style.display = 'block';
        errorMessage.querySelector('p').textContent = `Erreur: ${error.message}`;
    }
}

async function deleteUser(userId) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
        return;
    }
    
    try {
        const result = await apiDelete("utilisateurs.php", { userId });
        console.log("Utilisateur supprimé:", result);
        
        // Recharger la liste
        const currentFilter = roleFilter.value;
        if (currentFilter) {
            filterUsers(currentFilter);
        } else {
            getAllUsers();
        }
        
        // Notification de succès
        showNotification('Utilisateur supprimé avec succès', 'success');
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

// Gestion du filtre
if (roleFilter) {
    roleFilter.addEventListener('change', (e) => {
        const role = e.target.value;
        if (role) {
            filterUsers(role);
        } else {
            getAllUsers();
        }
    });
}

// Charger les utilisateurs au chargement de la page
if (window.location.pathname.includes("utilisateurs/liste.php")) {
    getAllUsers();
}

// Rendre la fonction deleteUser globale pour les boutons
window.deleteUser = deleteUser;
