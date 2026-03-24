import { apiGet, apiDelete } from "./api.js";

const deleteUsersList = document.getElementById("deleteUsersList");
const loading = document.getElementById("loading");
const errorMessage = document.getElementById("errorMessage");

console.log("utilisateurs-delete.js chargé");

function escapeHTML(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function getRoleBadge(role) {
    const badges = {
        'administrateur': '<span class="badge badge-admin">Administrateur</span>',
        'editeur': '<span class="badge badge-editor">Éditeur</span>'
    };
    return badges[role] || '<span class="badge badge-default">Inconnu</span>';
}

function createDeleteUserCard(user) {
    const card = document.createElement("div");
    card.className = "delete-user-card";
    card.innerHTML = `
        <div class="user-info">
            <div class="user-avatar">
                <div class="avatar-placeholder">
                    ${user.prenom ? user.prenom.charAt(0).toUpperCase() : 'U'}${user.nom ? user.nom.charAt(0).toUpperCase() : ''}
                </div>
            </div>
            <div class="user-details">
                <h4 class="user-name">${escapeHTML(user.prenom || '')} ${escapeHTML(user.nom || '')}</h4>
                <p class="user-login">@${escapeHTML(user.login || '')}</p>
                <div class="user-role">${getRoleBadge(user.role)}</div>
            </div>
        </div>
        <div class="delete-actions">
            <button class="btn btn-danger" onclick="confirmDeleteUser(${user.id}, '${escapeHTML(user.prenom || '')} ${escapeHTML(user.nom || '')}')">
                <i class="icon-trash"></i> Supprimer
            </button>
        </div>
    `;
    return card;
}

async function loadUsersForDeletion() {
    try {
        loading.style.display = 'block';
        errorMessage.style.display = 'none';
        deleteUsersList.innerHTML = '';
        
        const data = await apiGet("utilisateurs.php?action=all");
        loading.style.display = 'none';
        
        if (!data || data.length === 0) {
            deleteUsersList.innerHTML = '<p class="no-results">Aucun utilisateur trouvé</p>';
            return;
        }
        
        data.forEach(user => {
            deleteUsersList.appendChild(createDeleteUserCard(user));
        });
        
        console.log(`Chargé ${data.length} utilisateurs pour suppression`);
    } catch (error) {
        console.error('Erreur:', error);
        loading.style.display = 'none';
        errorMessage.style.display = 'block';
        errorMessage.querySelector('p').textContent = `Erreur: ${error.message}`;
    }
}

async function confirmDeleteUser(userId, userName) {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ?\n\nCette action est irréversible et toutes les données associées seront perdues.`)) {
        return;
    }
    
    try {
        const result = await apiDelete("utilisateurs.php", { userId });
        console.log("Utilisateur supprimé:", result);
        
        // Notification de succès
        showNotification(`L'utilisateur "${userName}" a été supprimé avec succès`, 'success');
        
        // Recharger la liste
        setTimeout(() => {
            loadUsersForDeletion();
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

// Charger les utilisateurs au chargement de la page
if (window.location.pathname.includes("utilisateurs/supprimer.php")) {
    loadUsersForDeletion();
}

// Rendre la fonction confirmDeleteUser globale pour les boutons
window.confirmDeleteUser = confirmDeleteUser;
