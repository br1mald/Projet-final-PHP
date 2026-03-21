const categoriesContainer = document.querySelector(".categories-container");

console.log("Hello"); // debugging

async function getCategory(id) {
  const data = await apiGet(`categories.php?action=search&id=${id}`);
  return data;
}

async function getAllCategories() {
  const data = await apiGet(`categories.php?action=all`);
  data.forEach((category) => {
    const li = document.createElement("li");

    li.className = "category-name";
    li.textContent = `${category.nom}`;

    categoriesContainer.appendChild(li);
    console.log(`added category: ${category.nom}`);
  });
}

if (window.location.pathname.includes("categories/liste.php"))
  getAllCategories();
