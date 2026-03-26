import { apiPost, apiDelete } from "./api.js";
import { showFormErrors } from "./validation.js";

const loginForm = document.querySelector("#formConnexion");

if (loginForm) {
  loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const existingAlert = document.querySelector(".alert-error");
    if (existingAlert) existingAlert.remove();

    const loginInput = document.querySelector("#login");
    const mdpInput = document.querySelector("#motdepasse");

    const errors = {};
    if (loginInput.value.trim() === "") {
      errors.login = "Le login est obligatoire.";
      loginInput.classList.add("input-error");
    } else {
      loginInput.classList.remove("input-error");
    }

    if (mdpInput.value.trim() === "") {
      errors.motdepasse = "Le mot de passe est obligatoire.";
      mdpInput.classList.add("input-error");
    } else {
      mdpInput.classList.remove("input-error");
    }

    showFormErrors(loginForm, errors);
    if (Object.keys(errors).length > 0) return;

    const payload = {
      login: loginInput.value.trim(),
      motdepasse: mdpInput.value.trim(),
    };

    try {
      const res = await apiPost("auth.php", payload);
      console.log("Logged in successfully!", res);

      window.location.href = (window.APP_BASE || "") + "/accueil.php";
    } catch (err) {
      console.error("Login failed:", err);
      showFormErrors(loginForm, { server: err.message });
    }
  });
}

async function logoutUser() {
  try {
    await apiDelete("auth.php");
    console.log("Logged out successfully");
    window.location.href = (window.APP_BASE || "") + "/connexion.php";
  } catch (err) {
    console.error("Logout failed:", err);
    alert("Erreur lors de la déconnexion.");
  }
}

const logoutButton = document.getElementById("btn-logout");
if (logoutButton) {
  logoutButton.addEventListener("click", async (e) => {
    e.preventDefault();
    await logoutUser();
  });
}
