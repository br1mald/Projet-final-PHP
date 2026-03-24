import { showFormErrors } from "./validation.js";
import { apiPost } from "./api.js";

const addCategoryForm = document.getElementById("addCategoryForm");

console.log("categories-forms.js chargé");

function validateCategoryForm(formData) {
    const errors = {};
    
    // Validation du nom
    if (!formData.nom || formData.nom.trim().length < 3) {
        errors.nom = "Le nom doit contenir au moins 3 caractères";
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

async function submitAddCategoryForm() {
    if (!addCategoryForm) return;
    
    addCategoryForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            nom: addCategoryForm.nom.value.trim()
        };
        
        const errors = validateCategoryForm(formData);
        showFormErrors(addCategoryForm, errors);
        
        if (Object.keys(errors).length > 0) return;
        
        try {
            const result = await apiPost("categories.php", formData);
            console.log("Catégorie créée:", result);
            
            showFormErrors(addCategoryForm, { success: "Catégorie ajoutée avec succès" });
            
            // Rediriger vers la liste après 1.5 secondes
            setTimeout(() => {
                window.location.href = "liste.php";
            }, 1500);
            
        } catch (error) {
            console.error('Erreur lors de la création:', error);
            showFormErrors(addCategoryForm, { server: error.message || "Erreur lors de la création de la catégorie" });
        }
    });
}

// Charger le formulaire au chargement de la page
if (window.location.pathname.includes("categories/ajouter.php")) {
    submitAddCategoryForm();
}
