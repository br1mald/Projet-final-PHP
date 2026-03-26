<?php
require_once __DIR__ . "/../entete.php";
require_once __DIR__ . "/../menu.php";

// Vérifier si l'utilisateur est administrateur
$userRole = $_SESSION['role'] ?? 'visiteur';
if (!in_array($userRole, ['administrateur'])) {
    header('Location: ../accueil.php');
    exit;
}

// Récupérer tous les utilisateurs
require_once __DIR__ . '/../includes/db.php';

try {
    $stmt = $pdo->query("SELECT id, nom, prenom, login, role, date FROM utilisateurs ORDER BY nom, prenom");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $users = [];
    $error = $e->getMessage();
}
?>

<main class="utilisateurs-container">
    <div class="page-header">
        <h1>Gestion des Utilisateurs</h1>
        <div class="actions">
            <a href="ajouter.php" class="btn btn-primary">
                <i class="icon-plus"></i> Ajouter un utilisateur
            </a>
            <a href="supprimer.php" class="btn btn-danger">
                <i class="icon-trash"></i> Supprimer
            </a>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-error">
            Erreur lors du chargement des utilisateurs: <?php echo htmlspecialchars($error); ?>
        </div>
    <?php elseif (empty($users)): ?>
        <div class="alert alert-info">
            Aucun utilisateur trouvé. 
            <a href="ajouter.php" class="btn btn-primary">Ajouter un utilisateur</a>
        </div>
    <?php else: ?>
        <div class="users-grid">
            <?php foreach ($users as $user): ?>
                <div class="user-card">
                    <div class="user-avatar">
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?>
                        </div>
                    </div>
                    <div class="user-info">
                        <h3 class="user-name"><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></h3>
                        <p class="user-login">@<?php echo htmlspecialchars($user['login']); ?></p>
                        <div class="user-role"><?php echo getRoleBadge($user['role']); ?></div>
                        <?php if ($user['date']): ?>
                            <p class="user-date">📅 <?php echo formatDate($user['date']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="user-actions">
                        <a href="modifier.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-secondary">
                            <i class="icon-edit"></i> Modifier
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?>')">
                            <i class="icon-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script>
function getRoleBadge(role) {
    const badges = {
        'administrateur': '<span class="badge badge-admin">Administrateur</span>',
        'editeur': '<span class="badge badge-editor">Éditeur</span>',
        'visiteur': '<span class="badge badge-default">Visiteur</span>'
    };
    return badges[role] || '<span class="badge badge-default">Inconnu</span>';
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('fr-FR', { 
        day: '2-digit', 
        month: 'long', 
        year: 'numeric' 
    });
}

function confirmDelete(userId, userName) {
    if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ?`)) {
        // Créer un formulaire caché pour la suppression
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'supprimer.php';
        form.style.display = 'none';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'userId';
        input.value = userId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.utilisateurs-container {
    max-width: 1200px;
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

.users-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1.5rem;
}

.user-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    padding: 1.5rem;
    transition: transform 0.3s ease;
}

.user-card:hover {
    transform: translateY(-4px);
}

.user-avatar {
    margin-bottom: 1rem;
}

.avatar-placeholder {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--accent);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0 auto;
}

.user-info {
    margin-bottom: 1.5rem;
}

.user-name {
    color: var(--text);
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
}

.user-login {
    color: var(--muted);
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
}

.user-role {
    margin: 0 0 0.5rem 0;
}

.user-date {
    color: var(--muted);
    margin: 0;
    font-size: 0.8rem;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.badge-admin {
    background: #dc3545;
    color: white;
}

.badge-editor {
    background: #28a745;
    color: white;
}

.badge-default {
    background: #6c757d;
    color: white;
}

.user-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
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
    .utilisateurs-container {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .actions {
        justify-content: center;
    }
    
    .users-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php 
function getRoleBadge($role) {
    $badges = [
        'administrateur' => '<span class="badge badge-admin">Administrateur</span>',
        'editeur' => '<span class="badge badge-editor">Éditeur</span>',
        'visiteur' => '<span class="badge badge-default">Visiteur</span>'
    ];
    return $badges[$role] ?? '<span class="badge badge-default">Inconnu</span>';
}

function formatDate($dateStr) {
    if (!$dateStr) return '';
    $d = new DateTime($dateStr);
    return $d->format('d F Y');
}

require_once __DIR__ . "/../footer.php"; 
?>
