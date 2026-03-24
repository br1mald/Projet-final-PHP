import { showFormErrors } from "./validation.js";
import { apiGet, apiPost, apiDelete } from "./api.js";

const addUserForm = document.getElementById("addUserForm");

console.log("utilisateurs-forms.js chargé");

function validateUserForm(formData) {
    const errors = {};
    
    // Validation du nom
    if (!formData.nom || formData.nom.trim().length < 2) {
        errors.nom = "Le nom doit contenir au moins 2 caractères";
    }
    
    // Validation du prénom
    if (!formData.prenom || formData.prenom.trim().length < 2) {
        errors.prenom = "Le prénom doit contenir au moins 2 caractères";
    }
    
    // Validation du login
    if (!formData.login || formData.login.trim().length < 3) {
        errors.login = "Le login doit contenir au moins 3 caractères";
    }
    
    // Validation du mot de passe
    if (!formData.mot_de_passe || formData.mot_de_passe.length < 6) {
        errors.mot_de_passe = "Le mot de passe doit contenir au moins 6 caractères";
    }
    
    // Validation de la confirmation du mot de passe
    if (formData.mot_de_passe !== formData.confirm_password) {
        errors.confirm_password = "Les mots de passe ne correspondent pas";
    }
    
    // Validation du rôle
    if (!formData.role) {
        errors.role = "Veuillez sélectionner un rôle";
    }
    
    return errors;
}

function showFormErrors(form, errors) {
    // Effacer les erreurs précédentes
    form.querySelectorAll('.error-message').forEach(el => {
        el.textContent = '';
        el.style.display = 'none';
    });
    
    // Afficher les nouvelles erreurs
    Object.keys(errors).forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            const errorElement = field.parentElement.querySelector('.error-message');
            if (errorElement) {
                errorElement.textContent = errors[fieldName];
                errorElement.style.display = 'block';
            }
        }
    });
    
    // Gérer les messages de succès
    if (errors.success) {
        showNotification(errors.success, 'success');
    }
    
    // Gérer les erreurs serveur
    if (errors.server) {
        showNotification(errors.server, 'error');
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

async function submitAddUserForm() {
    if (!addUserForm) return;
    
    addUserForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            nom: addUserForm.nom.value.trim(),
            prenom: addUserForm.prenom.value.trim(),
            login: addUserForm.login.value.trim(),
            mot_de_passe: addUserForm.mot_de_passe.value,
            confirm_password: addUserForm.confirm_password.value,
            role: addUserForm.role.value
        };
        
        const errors = validateUserForm(formData);
        showFormErrors(addUserForm, errors);
        
        if (Object.keys(errors).length > 0) return;
        
        try {
            // Préparer les données pour l'API (sans la confirmation du mot de passe)
            const apiData = {
                nom: formData.nom,
                prenom: formData.prenom,
                login: formData.login,
                mot_de_passe: formData.mot_de_passe,
                role: formData.role
            };
            
            const result = await apiPost("utilisateurs.php", apiData);
            console.log("Utilisateur créé:", result);
            
            showFormErrors(addUserForm, { success: "Utilisateur ajouté avec succès" });
            
            // Rediriger vers la liste après 1.5 secondes
            setTimeout(() => {
                window.location.href = "liste.php";
            }, 1500);
            
        } catch (error) {
            console.error('Erreur lors de la création:', error);
            showFormErrors(addUserForm, { server: error.message || "Erreur lors de la création de l'utilisateur" });
        }
    });
}

// Charger le formulaire au chargement de la page
if (window.location.pathname.includes("utilisateurs/ajouter.php")) {
    submitAddUserForm();
}
