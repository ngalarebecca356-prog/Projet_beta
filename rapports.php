<?php
require 'includes/auth.php';
require 'config.php';

$rapports = $pdo->query(
    "SELECT c.Code_Projet, p.Intitule_Projet, c.Mois_Calcul, c.Annee, c.Depense, c.Cout_Total_Calcule
     FROM cout_mensuel_projet c
     LEFT JOIN projet p ON p.Code_Projet = c.Code_Projet
     ORDER BY c.Annee DESC, c.Mois_Calcul DESC"
)->fetchAll();

$active_page = 'rapports';
$mois_fr = array(1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PROJET-MANAGER — Rapports</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-shell">
  <?php include 'includes/sidebar.php'; ?>

  <main class="main-content">
    <div class="page-header">
      <h1>Rapports — Coûts mensuels</h1>
    </div>

    <?php if (count($rapports) === 0): ?>
      <div class="empty-state">
        <h3>Aucune donnée de coût enregistrée</h3>
        <p>Les coûts mensuels calculés pour chaque projet apparaîtront ici.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Projet</th><th>Mois</th><th>Année</th><th>Dépense</th><th>Coût total calculé</th></tr>
          </thead>
          <tbody>
            <?php foreach ($rapports as $r): ?>
              <tr>
                <td><?php echo htmlspecialchars((isset($r['Intitule_Projet']) ? $r['Intitule_Projet'] : $r['Code_Projet'])); ?></td>
                <td><?php echo htmlspecialchars(isset($mois_fr[(int)$r['Mois_Calcul']]) ? $mois_fr[(int)$r['Mois_Calcul']] : $r['Mois_Calcul']); ?></td>
                <td><?php echo htmlspecialchars($r['Annee']); ?></td>
                <td><?php echo $r['Depense'] !== null ? htmlspecialchars($r['Depense']) . ' €' : '—'; ?></td>
                <td><?php echo $r['Cout_Total_Calcule'] !== null ? htmlspecialchars($r['Cout_Total_Calcule']) . ' €' : '—'; ?></td>
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
