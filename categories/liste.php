<?php
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";

// Vérifier si l'utilisateur est administrateur
$userRole = $_SESSION['role'] ?? 'visiteur';
if (!in_array($userRole, ['administrateur'])) {
    header('Location: ../accueil.php');
    exit;
}

// Récupérer toutes les catégories avec le nombre d'articles
require_once __DIR__ . '/../includes/db.php';

try {
    $stmt = $pdo->query("SELECT c.*, COUNT(a.id) as articles_count 
                           FROM categories c 
                           LEFT JOIN articles a ON c.id = a.categorie_id 
                           GROUP BY c.id 
                           ORDER BY c.nom");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $categories = [];
    $error = $e->getMessage();
}
?>

<main class="categories-container">
    <div class="page-header">
        <h1>Gestion des Catégories</h1>
        <div class="actions">
            <a href="ajouter.php" class="btn btn-primary">
                <i class="icon-plus"></i> Ajouter une catégorie
            </a>
            <a href="supprimer.php" class="btn btn-danger">
                <i class="icon-trash"></i> Supprimer
            </a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            Erreur lors du chargement des catégories: <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif (empty($categories)): ?>
        <div class="alert alert-info">
            Aucune catégorie trouvée. 
            <a href="ajouter.php" class="btn btn-primary">Ajouter une catégorie</a>
        </div>
    <?php else: ?>
        <div class="categories-stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($categories); ?></div>
                <div class="stat-label">Catégories</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo array_sum(array_column($categories, 'articles_count')); ?></div>
                <div class="stat-label">Articles total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo max(array_column($categories, 'articles_count')); ?></div>
                <div class="stat-label">Max articles</div>
            </div>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $category): ?>
                <div class="category-card" style="border-left: 4px solid <?php echo getCategoryColor($category['nom']); ?>;">
                    <div class="category-header">
                        <div class="category-icon" style="background: <?php echo getCategoryColor($category['nom']); ?>;">
                            <?php echo getCategoryIcon($category['nom']); ?>
                        </div>
                        <div class="category-info">
                            <h3 class="category-name"><?php echo htmlspecialchars($category['nom']); ?></h3>
                            <div class="category-meta">
                                <span class="category-id">ID: <?php echo $category['id']; ?></span>
                            <span class="category-date">Créée le <?php echo date('d/m/Y', strtotime($category['created_at'] ?? 'now')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="category-content">
                        <div class="category-stats">
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $category['articles_count']; ?></div>
                                <div class="stat-label">articles</div>
                            </div>
                            <?php if ($category['articles_count'] > 0): ?>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo min(100, ($category['articles_count'] / 10) * 100); ?>%;"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="category-description">
                            <?php if ($category['articles_count'] > 0): ?>
                                <p>Cette catégorie contient <?php echo $category['articles_count']; ?> article(s).</p>
                            <?php else: ?>
                                <p>Cette catégorie ne contient aucun article pour le moment.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="category-actions">
                        <a href="modifier.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-primary">
                            <i class="icon-edit"></i> Modifier
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['nom']); ?>')" <?php echo $category['articles_count'] > 0 ? 'disabled title="Cette catégorie contient des articles"' : ''; ?>>
                            <i class="icon-trash"></i> Supprimer
                        </button>
                        <a href="../articles/liste_categorie.php?categorie=<?php echo urlencode(strtolower(str_replace([' ', 'é', 'è', 'ê'], ['', 'e', 'e'], $category['nom']))); ?>" class="btn btn-sm btn-secondary">
                            <i class="icon-eye"></i> Voir les articles
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script>
function getCategoryIcon(categoryName) {
    const icons = {
        'technologie': '💻',
        'sport': '⚽',
        'politique': '🏛️',
        'éducation': '📚',
        'culture': '🎭'
    };
    
    const name = categoryName.toLowerCase();
    for (const [key, icon] of Object.entries(icons)) {
        if (name.includes(key)) {
            return icon;
        }
    }
    
    return '📁';
}

function confirmDelete(categoryId, categoryName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer la catégorie "${categoryName}" ?`)) {
        // Créer un formulaire caché pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'supprimer.php';
        form.style.display = 'none';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'categoryId';
        input.value = categoryId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.categories-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-light);
}

.page-header h1 {
    color: var(--text);
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
}

.categories-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    text-align: center;
    border: 1px solid var(--border-light);
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.stat-card .stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--accent);
    margin-bottom: 0.5rem;
}

.stat-card .stat-label {
    font-size: 0.875rem;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.category-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
    transition: all 0.3s ease;
}

.category-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.category-header {
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
}

.category-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
}

.category-info {
    flex: 1;
}

.category-name {
    color: var(--text);
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.category-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.75rem;
    color: var(--muted);
}

.category-id {
    background: var(--border-light);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.category-date {
    color: var(--muted);
}

.category-content {
    padding: 1.5rem;
}

.category-stats {
    margin-bottom: 1.5rem;
}

.category-stats .stat-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.category-stats .stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
}

.category-stats .stat-label {
    font-size: 0.875rem;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.progress-bar {
    width: 100%;
    height: 6px;
    background: var(--border-light);
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--accent), #17a2b8);
    border-radius: 3px;
    transition: width 0.3s ease;
}

.category-description {
    margin-bottom: 1.5rem;
}

.category-description p {
    color: var(--muted);
    line-height: 1.6;
    margin: 0;
}

.category-actions {
    padding: 1.5rem;
    background: #f8f9fa;
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.actions {
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-primary {
    background: var(--accent);
    color: var(--primary);
}

.btn-primary:hover {
    background: var(--accent-dark);
    transform: translateY(-1px);
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
    transform: translateY(-1px);
}

.btn-danger:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
}

.btn-secondary {
    background: var(--border-light);
    color: var(--text);
}

.btn-secondary:hover {
    background: #e0e0e0;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.alert-info {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .categories-container {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .page-header h1 {
        font-size: 1.5rem;
    }
    
    .actions {
        justify-content: center;
    }
    
    .categories-stats {
        grid-template-columns: 1fr;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
    }
    
    .category-actions {
        justify-content: center;
    }
    
    .btn-sm {
        flex: 1;
        justify-content: center;
    }
}
</style>

<?php 
// Fonction pour obtenir la couleur d'une catégorie
function getCategoryColor($categoryName) {
    $colors = [
        'technologie' => '#007bff',
        'sport' => '#28a745',
        'politique' => '#dc3545',
        'éducation' => '#fd7e14',
        'culture' => '#6f42c1',
        'international' => '#20c997',
        'europe' => '#343a40',
        'afrique' => '#ffc107',
        'amériques' => '#e83e8c'
    ];
    
    $name = strtolower($categoryName);
    foreach ($colors as $key => $color) {
        if (strpos($name, $key) !== false) {
            return $color;
        }
    }
    
    return '#6c757d'; // Couleur par défaut
}

function getCategoryIcon($categoryName) {
    $icons = [
        'technologie' => '💻',
        'sport' => '⚽',
        'politique' => '🏛️',
        'éducation' => '📚',
        'culture' => '🎭',
        'international' => '🌍',
        'europe' => '🏰',
        'afrique' => '🦁',
        'amériques' => '🌎'
    ];
    
    $name = strtolower($categoryName);
    foreach ($icons as $key => $icon) {
        if (strpos($name, $key) !== false) {
            return $icon;
        }
    }
    
    return '📁'; // Icône par défaut
}

require_once __DIR__ . "/../footer.php"; 
?>
