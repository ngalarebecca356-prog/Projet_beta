<?php
require 'includes/auth.php';
require 'config.php';

$recherche = trim((isset($_GET['q']) ? $_GET['q'] : ''));

$sql = "SELECT p.Code_Projet, p.Intitule_Projet, p.Statut_Projet, p.Date_Debut, p.Date_Fin, p.Budjet_Prevue, c.Nom_Client
        FROM projet p
        LEFT JOIN client c ON c.Siret = p.Siret";

if ($recherche !== '') {
    $sql .= ' WHERE p.Intitule_Projet LIKE :q OR p.Code_Projet LIKE :q';
    $stmt = $pdo->prepare($sql . ' ORDER BY p.Date_Debut DESC');
    $stmt->execute(array('q' => '%' . $recherche . '%'));
} else {
    $stmt = $pdo->query($sql . ' ORDER BY p.Date_Debut DESC');
}
$projets = $stmt->fetchAll();

$active_page = 'projets';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PROJET-MANAGER — Projets</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-shell">
  <?php include 'includes/sidebar.php'; ?>

  <main class="main-content">
    <div class="page-header">
      <h1>Projets</h1>
    </div>

    <form class="search-bar" method="get" action="projets.php">
      <input type="text" name="q" value="<?php echo htmlspecialchars($recherche); ?>" placeholder="Rechercher un projet...">
    </form>

    <?php if (count($projets) === 0): ?>
      <div class="empty-state">
        <h3><?php echo $recherche !== '' ? 'Aucun résultat' : 'Aucun projet enregistré'; ?></h3>
        <p>Les projets créés dans ta base apparaîtront ici.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Code</th><th>Intitulé</th><th>Client</th><th>Statut</th><th>Début</th><th>Fin</th><th>Budget</th></tr>
          </thead>
          <tbody>
            <?php foreach ($projets as $p): ?>
              <tr>
                <td><?php echo htmlspecialchars($p['Code_Projet']); ?></td>
                <td><?php echo htmlspecialchars((isset($p['Intitule_Projet']) ? $p['Intitule_Projet'] : '—')); ?></td>
                <td><?php echo htmlspecialchars((isset($p['Nom_Client']) ? $p['Nom_Client'] : '—')); ?></td>
                <td><span class="badge"><?php echo htmlspecialchars((isset($p['Statut_Projet']) ? $p['Statut_Projet'] : '—')); ?></span></td>
                <td><?php echo htmlspecialchars((isset($p['Date_Debut']) ? $p['Date_Debut'] : '—')); ?></td>
                <td><?php echo htmlspecialchars((isset($p['Date_Fin']) ? $p['Date_Fin'] : '—')); ?></td>
                <td><?php echo $p['Budjet_Prevue'] !== null ? htmlspecialchars($p['Budjet_Prevue']) . ' €' : '—'; ?></td>
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
