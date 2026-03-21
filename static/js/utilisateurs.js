const usersContainer = document.querySelector(".utilisateurs-container");

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
    li.textContent = `${user.nom}`;

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

if (window.location.pathname.includes("utilisateurs/liste.php")) getAllUsers();
// if (window.location.pathname.includes("utilisateurs/liste.php"))
//   filterUsers("editeur"); test
