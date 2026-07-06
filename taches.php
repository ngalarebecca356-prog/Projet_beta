<?php
require 'includes/auth.php';
require 'config.php';

$recherche = trim((isset($_GET['q']) ? $_GET['q'] : ''));

$sql = "SELECT t.Num_Tache, t.Libelle_Tache, t.Statut_Tache, t.Avancement_Pourcentage,
               t.Date_Fin_Tache, p.Intitule_Projet, e.Nom, e.Prenom
        FROM tache t
        LEFT JOIN projet p ON p.Code_Projet = t.Code_Projet
        LEFT JOIN employe e ON e.Matricule_Employer = t.Matricule_Employer";

if ($recherche !== '') {
    $sql .= ' WHERE t.Libelle_Tache LIKE :q OR p.Intitule_Projet LIKE :q';
    $stmt = $pdo->prepare($sql . ' ORDER BY t.Date_Fin_Tache');
    $stmt->execute(array('q' => '%' . $recherche . '%'));
} else {
    $stmt = $pdo->query($sql . ' ORDER BY t.Date_Fin_Tache');
}
$taches = $stmt->fetchAll();

$active_page = 'taches';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PROJET-MANAGER — Tâches</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-shell">
  <?php include 'includes/sidebar.php'; ?>

  <main class="main-content">
    <div class="page-header">
      <h1>Tâches</h1>
    </div>

    <form class="search-bar" method="get" action="taches.php">
      <input type="text" name="q" value="<?php echo htmlspecialchars($recherche); ?>" placeholder="Rechercher une tâche...">
    </form>

    <?php if (count($taches) === 0): ?>
      <div class="empty-state">
        <h3><?php echo $recherche !== '' ? 'Aucun résultat' : 'Aucune tâche enregistrée'; ?></h3>
        <p>Les tâches créées dans ta base apparaîtront ici.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Tâche</th><th>Projet</th><th>Assigné à</th><th>Statut</th><th>Avancement</th><th>Échéance</th></tr>
          </thead>
          <tbody>
            <?php foreach ($taches as $t): ?>
              <tr>
                <td><?php echo htmlspecialchars((isset($t['Libelle_Tache']) ? $t['Libelle_Tache'] : $t['Num_Tache'])); ?></td>
                <td><?php echo htmlspecialchars((isset($t['Intitule_Projet']) ? $t['Intitule_Projet'] : '—')); ?></td>
                <td><?php echo $t['Nom'] ? htmlspecialchars($t['Prenom'] . ' ' . $t['Nom']) : '—'; ?></td>
                <td><span class="badge"><?php echo htmlspecialchars((isset($t['Statut_Tache']) ? $t['Statut_Tache'] : '—')); ?></span></td>
                <td><?php echo $t['Avancement_Pourcentage'] !== null ? htmlspecialchars($t['Avancement_Pourcentage']) . ' %' : '—'; ?></td>
                <td><?php echo htmlspecialchars((isset($t['Date_Fin_Tache']) ? $t['Date_Fin_Tache'] : '—')); ?></td>
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
