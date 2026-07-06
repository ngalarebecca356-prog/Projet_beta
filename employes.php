<?php
require 'includes/auth.php';
require 'config.php';

$recherche = trim((isset($_GET['q']) ? $_GET['q'] : ''));

$sql = "SELECT Matricule_Employer, Nom, Prenom, Poste, Departement, Email, Statut_Contrat FROM employe";

if ($recherche !== '') {
    $sql .= ' WHERE Nom LIKE :q OR Prenom LIKE :q OR Poste LIKE :q';
    $stmt = $pdo->prepare($sql . ' ORDER BY Nom');
    $stmt->execute(array('q' => '%' . $recherche . '%'));
} else {
    $stmt = $pdo->query($sql . ' ORDER BY Nom');
}
$employes = $stmt->fetchAll();

$active_page = 'employes';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PROJET-MANAGER — Employés</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-shell">
  <?php include 'includes/sidebar.php'; ?>

  <main class="main-content">
    <div class="page-header">
      <h1>Employés</h1>
    </div>

    <form class="search-bar" method="get" action="employes.php">
      <input type="text" name="q" value="<?php echo htmlspecialchars($recherche); ?>" placeholder="Rechercher un employé...">
    </form>

    <?php if (count($employes) === 0): ?>
      <div class="empty-state">
        <h3><?php echo $recherche !== '' ? 'Aucun résultat' : 'Aucun employé enregistré'; ?></h3>
        <p>Les employés créés dans ta base (ou via l'inscription) apparaîtront ici.</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr><th>Matricule</th><th>Nom</th><th>Poste</th><th>Département</th><th>Email</th><th>Contrat</th></tr>
          </thead>
          <tbody>
            <?php foreach ($employes as $e): ?>
              <tr>
                <td><?php echo htmlspecialchars($e['Matricule_Employer']); ?></td>
                <td><?php echo htmlspecialchars($e['Prenom'] . ' ' . $e['Nom']); ?></td>
                <td><?php echo htmlspecialchars((isset($e['Poste']) ? $e['Poste'] : '—')); ?></td>
                <td><?php echo htmlspecialchars((isset($e['Departement']) ? $e['Departement'] : '—')); ?></td>
                <td><?php echo htmlspecialchars((isset($e['Email']) ? $e['Email'] : '—')); ?></td>
                <td><span class="badge"><?php echo htmlspecialchars((isset($e['Statut_Contrat']) ? $e['Statut_Contrat'] : '—')); ?></span></td>
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
