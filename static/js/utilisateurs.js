import { apiGet, apiPost, apiDelete, apiPatch } from "./api.js";
import { showFormErrors, escapeHTML } from "./validation.js";

const usersContainer = document.querySelector(".utilisateurs-container");
const patchFormContainer = document.querySelector(".patch-form-container");
const postForm = document.querySelector(
  "form[action='../api/utilisateurs.php']",
);

// async function getUser(id) { pas utilisé
//   const data = await apiGet(`utilisateurs.php?action=search&id=${id}`);
//   return data;
// }

async function getAllUsers() {
  const data = await apiGet(`utilisateurs.php?action=all`);

  const grid = document.createElement("div");
  grid.className = "users-grid";

  data.forEach((user) => {
    const initials =
      (user.prenom ? user.prenom[0].toUpperCase() : "U") +
      (user.nom ? user.nom[0].toUpperCase() : "");

    const badgeClass =
      user.role === "administrateur" ? "badge-admin" : "badge-editor";
    const roleLabel =
      user.role === "administrateur" ? "Administrateur" : "Éditeur";

    const card = document.createElement("div");
    card.className = "user-card";
    card.innerHTML = `
      <div class="user-avatar">
        <div class="avatar-placeholder">${initials}</div>
      </div>
      <div class="user-info">
        <h3 class="user-name">${escapeHTML(user.prenom)} ${escapeHTML(user.nom)}</h3>
        <p class="user-login">@${escapeHTML(user.login)}</p>
        <div class="user-role"><span class="badge ${badgeClass}">${roleLabel}</span></div>
      </div>
      <div class="card-actions">
        <a href="${window.APP_BASE || ""}/utilisateurs/modifier.php?id=${user.id}" class="btn btn-secondary btn-sm">Modifier</a>
      </div>
    `;
    grid.appendChild(card);
  });

  usersContainer.appendChild(grid);
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
  } else if (method === "delete") {
    if (isNaN(payload["userId"]))
      errors["userId"] = "Veuillez choisir un article valide";
  } else if (method === "patch") {
    if (payload.attribute && payload.value && payload.id) {
      if (payload.attribute === "id") errors.id = "Attribut non autorisé";
      else if (payload.attribute === "nom" && payload.value.trim().length < 3)
        errors.nom = "Le nom doit contenir au moins 3 caractères";
      else if (
        payload.attribute === "prenom" &&
        payload.value.trim().length < 3
      )
        errors.prenom = "Le prénom doit contenir au moins 3 caractères.";
      else if (payload.attribute === "login" && payload.value.trim().length < 5)
        errors.contenu = "Le login doit contenir au moins 5 caractères.";
      else if (
        payload.attribute === "password" &&
        payload.value.trim().length < 8
      )
        errors.userId = "Le mot de passe doit contenir au moins 8 caractères.";
      else if (
        payload.attribute === "role" &&
        payload.value != "editeur" &&
        payload.value != "administrateur"
      )
        errors.role = "Rôle invalide";
    } else errors.attributeValue = "Veuillez choisir un attribut";
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
        window.location.href =
          (window.APP_BASE || "") + "/utilisateurs/liste.php";
      }, 1000);
    } catch (err) {
      console.error(err);
      showFormErrors(postForm, { server: err.message || "Erreur survenue" });
    }
  });
}

async function populatePatchForm() {
  const data = await apiGet("utilisateurs.php?action=all");
  data.forEach((utilisateur) => {
    const attributes = Object.entries(utilisateur);

    const allowedAttributes = ["nom", "prenom", "login", "role"];

    attributes.forEach((attribute) => {
      if (!allowedAttributes.includes(attribute[0])) return;
      if (attribute[0] === "role") {
        const form = document.createElement("form");
        form.className = "patch-form";

        const idInput = document.createElement("input");
        idInput.type = "hidden";
        idInput.value = utilisateur.id;
        idInput.name = "userId";

        const nameInput = document.createElement("input");
        nameInput.type = "hidden";
        nameInput.value = "role";
        nameInput.name = "attributeName";

        const select = document.createElement("select");
        select.name = "attributeValue";

        const editeurOption = document.createElement("option");
        editeurOption.value = "editeur";
        editeurOption.textContent = "Éditeur";

        const adminOption = document.createElement("option");
        adminOption.value = "administrateur";
        adminOption.textContent = "Administrateur";

        if (utilisateur.role === "editeur") {
          select.appendChild(editeurOption);
          select.appendChild(adminOption);
        } else {
          select.appendChild(adminOption);
          select.appendChild(editeurOption);
        }

        form.appendChild(idInput);
        form.appendChild(nameInput);
        form.appendChild(select);

        patchFormContainer.appendChild(form);
      } else {
        const form = document.createElement("form");
        form.className = "patch-form";

        const idInput = document.createElement("input");
        idInput.type = "hidden";
        idInput.value = utilisateur.id;
        idInput.name = "userId";

        const nameInput = document.createElement("input");
        nameInput.type = "hidden";
        nameInput.value = attribute[0];
        nameInput.name = "attributeName";

        const valueInput = document.createElement("input");
        valueInput.type = "text";
        valueInput.value = attribute[1];
        valueInput.name = "attributeValue";

        form.appendChild(idInput);
        form.appendChild(nameInput);
        form.appendChild(valueInput);

        patchFormContainer.appendChild(form);
      }
    });
    const br = document.createElement("br");
    patchFormContainer.appendChild(br);
  });

  submitPatchForm();
}

function submitPatchForm() {
  const patchForms = document.querySelectorAll(".patch-form");
  patchForms.forEach((patchForm) => {
    let event = "submit";
    if (patchForm.attributeName.value === "role") event = "change";
    patchForm.addEventListener(event, async (e) => {
      e.preventDefault();

      const payload = {
        id: patchForm.userId.value,
        attribute: patchForm.attributeName.value,
        value: patchForm.attributeValue.value,
      };

      const errors = validatePayload(payload, "patch");
      showFormErrors(patchForm, errors);

      if (Object.keys(errors).length > 0) return;

      try {
        const res = await apiPatch("utilisateurs.php", payload);
        console.log("Success", res);
        showFormErrors(patchForm, {
          success: "Utilisateur modifié avec succès.",
        });
      } catch (err) {
        console.error(err);
        showFormErrors(patchForm, { server: err.message || "Erreur serveur." });
      }
    });
  });
}

async function populateDeleteForm() {
  const list = document.getElementById("deleteUsersList");
  const loading = document.getElementById("loadingUsers");
  const errorEl = document.getElementById("errorUsers");

  try {
    const data = await apiGet("utilisateurs.php?action=all");
    if (loading) loading.style.display = "none";

    data.forEach((utilisateur) => {
      const card = document.createElement("div");
      card.className = "delete-user-card";

      // Left: avatar + name/login
      const info = document.createElement("div");
      info.className = "user-info";

      const initials =
        (utilisateur.prenom ? utilisateur.prenom[0].toUpperCase() : "U") +
        (utilisateur.nom ? utilisateur.nom[0].toUpperCase() : "");
      const avatar = document.createElement("div");
      avatar.className = "user-avatar";
      avatar.innerHTML = `<div class="avatar-placeholder">${escapeHTML(initials)}</div>`;

      const details = document.createElement("div");
      details.innerHTML = `<h4 class="user-name">${escapeHTML(utilisateur.prenom || "")} ${escapeHTML(utilisateur.nom || "")}</h4>
        <p class="user-login">@${escapeHTML(utilisateur.login || "")}</p>`;

      info.appendChild(avatar);
      info.appendChild(details);

      const btn = document.createElement("button");
      btn.className = "btn btn-danger btn-sm";
      btn.textContent = "Supprimer";
      btn.addEventListener("click", async () => {
        const name =
          `${utilisateur.prenom || ""} ${utilisateur.nom || ""}`.trim();
        if (!confirm(`Supprimer l'utilisateur "${name}" ?`)) return;
        try {
          await apiDelete("utilisateurs.php", { userId: utilisateur.id });
          card.remove();
        } catch (err) {
          alert("Erreur : " + err.message);
        }
      });

      card.appendChild(info);
      card.appendChild(btn);
      list.appendChild(card);
    });
  } catch (err) {
    if (loading) loading.style.display = "none";
    if (errorEl) errorEl.style.display = "block";
  }
}

if (window.location.pathname.includes("utilisateurs/liste.php")) getAllUsers();
// if (window.location.pathname.includes("utilisateurs/liste.php"))
//   filterUsers("editeur"); test

if (window.location.pathname.includes("/utilisateurs/ajouter.php"))
  submitPostForm();
if (window.location.pathname.includes("/utilisateurs/supprimer.php"))
  populateDeleteForm();
if (window.location.pathname.includes("/utilisateurs/modifier.php"))
  populatePatchForm();
