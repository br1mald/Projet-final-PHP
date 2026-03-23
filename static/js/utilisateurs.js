import { apiGet, apiPost, apiDelete } from "./api.js";
import { showFormErrors } from "./validation.js";

const usersContainer = document.querySelector(".utilisateurs-container");
const deleteFormContainer = document.querySelector(".delete-form-container");
const postForm = document.querySelector(
  "form[action='../api/utilisateurs.php']",
);

console.log("Hello"); // debugging

async function getUser(id) {
  const data = await apiGet(`utilisateurs.php?action=search&id=${id}`);
  return data;
}

async function getAllUsers() {
  const data = await apiGet(`utilisateurs.php?action=all`);
  data.forEach((user) => {
    const li = document.createElement("li");

    li.className = "user-name";
    li.textContent = `${user.nom} ${user.prenom} ${user.login}`;

    usersContainer.appendChild(li);
    console.log(`found user: ${user.nom}`);
  });
}

async function filterUsers(role) {
  const data = await apiGet(`utilisateurs.php?action=role-filter&role=${role}`);
  data.forEach((user) => {
    const li = document.createElement("li");

    li.className = "user-name";
    li.textContent = `${user.nom}`;

    usersContainer.appendChild(li);
    console.log(`found user: ${user.nom}`);
  });
}

function validatePayload(payload, method) {
  const errors = {};
  if (method === "post") {
    if (!payload["nom"] || payload["nom"].trim().length < 3) {
      errors["nom"] = "Le nom doit contenir au moins 3 caractères";
    }
    if (!payload["prenom"] || payload["prenom"].trim().length < 3) {
      errors["prenom"] = "Le prénom doit contenir au moins 3 caractères";
    }
    if (!payload["login"] || payload["login"].trim().length < 3) {
      errors["login"] = "Le login doit contenir au moins 5 caractères";
    }
    if (!payload["password"] || payload["password"].trim().length < 8) {
      errors["password"] =
        "Le mot de passe doit contenir au moins 8 caractères";
    }
  }

  return errors;
}

function submitPostForm() {
  postForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const payload = {
      nom: postForm.nom.value,
      prenom: postForm.prenom.value,
      login: postForm.login.value,
      password: postForm.password.value,
      role: postForm.userRole.value,
    };
    console.log(payload.role);

    const errors = validatePayload(payload, "post");
    showFormErrors(postForm, errors);

    if (Object.keys(errors).length > 0) return;

    try {
      const res = await apiPost("utilisateurs.php", payload);
      console.log("success", res);
      showFormErrors(postForm, { success: "Utilisateur ajouté avec succès" });
      setTimeout(() => {
        window.location.href = "/final_project/utilisateurs/liste.php";
      }, 1000);
    } catch (err) {
      console.error(err);
      showFormErrors(postForm, { server: err.message || "Erreur survenue" });
    }
  });
}

async function populateDeleteForm() {
  const data = await apiGet("utilisateurs.php?action=all");
  data.forEach((utilisateur) => {
    const form = document.createElement("form");
    form.className = "delete-form";

    const input = document.createElement("input");
    input.type = "hidden";
    input.value = utilisateur.id;
    input.name = "user";

    const submitButton = document.createElement("button");
    submitButton.type = "submit";
    submitButton.textContent = utilisateur.nom;

    form.appendChild(input);
    form.appendChild(submitButton);

    deleteFormContainer.appendChild(form);
  });

  submitDeleteForm();
}

function submitDeleteForm() {
  const deleteForms = document.querySelectorAll(".delete-form");
  deleteForms.forEach((deleteForm) => {
    deleteForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const payload = {
        userId: deleteForm.user.value,
      };

      const errors = validatePayload(deleteForm, "delete");
      showFormErrors(deleteForm, errors);

      if (Object.keys(errors).length > 0) return;

      try {
        const res = apiDelete("utilisateurs.php", payload);
        console.log("success", res);
        showFormErrors(deleteForm, {
          success: "Utilisateur supprimé avec succès",
        });
        setTimeout(() => {
          deleteForm.remove();
        }, 1000);
      } catch (err) {
        console.error(err);
        showFormErrors(deleteForm, { server: err.message || "Erreur serveur" });
      }
    });
  });
}

if (window.location.pathname.includes("utilisateurs/liste.php")) getAllUsers();
// if (window.location.pathname.includes("utilisateurs/liste.php"))
//   filterUsers("editeur"); test

if (window.location.pathname.includes("/utilisateurs/ajouter.php"))
  submitPostForm();
if (window.location.pathname.includes("/utilisateurs/supprimer.php"))
  populateDeleteForm();
