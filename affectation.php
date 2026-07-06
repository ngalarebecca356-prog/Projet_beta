<?php
require 'includes/auth.php';
require 'config.php';

$recherche = trim((isset($_GET['q']) ? $_GET['q'] : ''));

$sql = 'SELECT a.Role_Affectation, a.Taux_Affectation,
               e.Nom, e.Prenom, p.Code_Projet, p.Intitule_Projet
        FROM affectation a
        JOIN employe e ON e.Matricule_Employer = a.Matricule_Employer
        JOIN projet p ON p.Code_Projet = a.Code_Projet';

if ($recherche !== '') {
    $sql .= ' WHERE e.Nom LIKE :q OR e.Prenom LIKE :q OR p.Intitule_Projet LIKE :q';
    $stmt = $pdo->prepare($sql . ' ORDER BY p.Code_Projet');
    $stmt->execute(array('q' => '%' . $recherche . '%'));
} else {
    $stmt = $pdo->query($sql . ' ORDER BY p.Code_Projet');
}
$affectations = $stmt->fetchAll();

$active_page = 'affectations';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>projet-MANAGER — Affectations</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-shell">
  <?php include 'includes/sidebar.php'; ?>

  <main class="main-content">
    <div class="page-header">
      <h1>Affectations</h1>
      <a href="affectation-nouvelle.php" class="btn btn-primary">Nouvelle affectation</a>
    </div>

    <form class="search-bar" method="get" action="affectation.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M20 20l-4.5-4.5"/></svg>
      <input type="text" name="q" value="<?php echo htmlspecialchars($recherche); ?>" placeholder="Rechercher une affectation...">
    </form>

    <?php if (count($affectations) === 0): ?>
      <div class="empty-state">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9.5 14.5 14.5 9.5"/><path d="M8 16 5.8 18.2a3 3 0 1 1-4.2-4.2L4 11.8"/><path d="M16 8l2.2-2.2a3 3 0 1 1 4.2 4.2L20 12.2"/></svg>
        <h3><?php echo $recherche !== '' ? 'Aucun résultat' : 'Aucune affectation enregistrée'; ?></h3>
        <p><?php echo $recherche !== ''
              ? "Aucune affectation ne correspond à « " . htmlspecialchars($recherche) . " »."
              : "Ajoutez une affectation pour lier un employé à un projet, avec son rôle et son taux d'affectation."; ?></p>
        <a href="affectation-nouvelle.php" class="btn btn-primary">Nouvelle affectation</a>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Employé</th>
              <th>Projet</th>
              <th>Rôle</th>
              <th>Taux</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($affectations as $a): ?>
              <tr>
                <td><?php echo htmlspecialchars($a['Prenom'] . ' ' . $a['Nom']); ?></td>
                <td><?php echo htmlspecialchars($a['Intitule_Projet']); ?> <span style="color:var(--ink-400);">(<?php echo htmlspecialchars($a['Code_Projet']); ?>)</span></td>
                <td><span class="badge"><?php echo htmlspecialchars((isset($a['Role_Affectation']) ? $a['Role_Affectation'] : '—')); ?></span></td>
                <td><?php echo htmlspecialchars((isset($a['Taux_Affectation']) ? $a['Taux_Affectation'] : '—')); ?>%</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </main>
</div>

</body>
</html>