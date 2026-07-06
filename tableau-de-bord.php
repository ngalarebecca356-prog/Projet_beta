<?php
require 'includes/auth.php';
require 'config.php';

$nbProjets   = (int) $pdo->query("SELECT COUNT(*) FROM projet")->fetchColumn();
$nbProjetsEnCours = (int) $pdo->query("SELECT COUNT(*) FROM projet WHERE Statut_Projet = 'En cours'")->fetchColumn();
$nbEmployes  = (int) $pdo->query("SELECT COUNT(*) FROM employe")->fetchColumn();
$nbTaches    = (int) $pdo->query("SELECT COUNT(*) FROM tache")->fetchColumn();
$nbTachesEnCours = (int) $pdo->query("SELECT COUNT(*) FROM tache WHERE Statut_Tache != 'Terminee' AND Statut_Tache != 'Terminée'")->fetchColumn();

$derniersProjets = $pdo->query(
    "SELECT Code_Projet, Intitule_Projet, Statut_Projet, Date_Fin
     FROM projet ORDER BY Date_Debut DESC LIMIT 5"
)->fetchAll();

$active_page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PROJET-MANAGER — Tableau de bord</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-shell">
  <?php include 'includes/sidebar.php'; ?>

  <main class="main-content">
    <div class="page-header">
      <h1>Tableau de bord</h1>
    </div>

    <div class="profile-grid">
      <div class="card">
        <h2>Projets</h2>
        <p style="font-size:2rem; font-weight:700; margin:.25rem 0;"><?php echo $nbProjets; ?></p>
        <p style="color:var(--ink-400);"><?php echo $nbProjetsEnCours; ?> en cours</p>
      </div>
      <div class="card">
        <h2>Employés</h2>
        <p style="font-size:2rem; font-weight:700; margin:.25rem 0;"><?php echo $nbEmployes; ?></p>
      </div>
      <div class="card">
        <h2>Tâches</h2>
        <p style="font-size:2rem; font-weight:700; margin:.25rem 0;"><?php echo $nbTaches; ?></p>
        <p style="color:var(--ink-400);"><?php echo $nbTachesEnCours; ?> non terminées</p>
      </div>
    </div>

    <div class="page-header" style="margin-top:2rem;">
      <h1 style="font-size:1.2rem;">Projets récents</h1>
      <a href="projets.php" class="btn btn-outline btn-sm">Voir tous les projets</a>
    </div>

    <?php if (count($derniersProjets) === 0): ?>
      <div class="empty-state">
        <h3>Aucun projet enregistré</h3>
        <p>Les projets créés dans ta base apparaîtront ici.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Code</th><th>Intitulé</th><th>Statut</th><th>Date de fin</th></tr>
          </thead>
          <tbody>
            <?php foreach ($derniersProjets as $p): ?>
              <tr>
                <td><?php echo htmlspecialchars($p['Code_Projet']); ?></td>
                <td><?php echo htmlspecialchars((isset($p['Intitule_Projet']) ? $p['Intitule_Projet'] : '—')); ?></td>
                <td><span class="badge"><?php echo htmlspecialchars((isset($p['Statut_Projet']) ? $p['Statut_Projet'] : '—')); ?></span></td>
                <td><?php echo htmlspecialchars((isset($p['Date_Fin']) ? $p['Date_Fin'] : '—')); ?></td>
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
