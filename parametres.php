<?php
require 'includes/auth.php';
require 'config.php';

$matricule = $_SESSION['matricule_employe'];

$stmt = $pdo->prepare(
    'SELECT e.Matricule_Employer, e.Nom, e.Prenom, e.Email, u.Role, u.Statut_Compte, u.Date_Creation_Compte
     FROM employe e JOIN utilisateur u ON u.Matricule_Employer = e.Matricule_Employer
     WHERE e.Matricule_Employer = :matricule'
);
$stmt->execute(array('matricule' => $matricule));
$compte = $stmt->fetch();

$active_page = 'parametres';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PROJET-MANAGER — Paramètres</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-shell">
  <?php include 'includes/sidebar.php'; ?>

  <main class="main-content">
    <div class="page-header">
      <h1>Paramètres</h1>
    </div>

    <div class="card">
      <h2>Informations du compte</h2>
      <p><strong>Matricule :</strong> <?php echo htmlspecialchars($compte['Matricule_Employer']); ?></p>
      <p><strong>Nom :</strong> <?php echo htmlspecialchars($compte['Prenom'] . ' ' . $compte['Nom']); ?></p>
      <p><strong>Email :</strong> <?php echo htmlspecialchars((isset($compte['Email']) ? $compte['Email'] : '—')); ?></p>
      <p><strong>Rôle :</strong> <span class="badge"><?php echo htmlspecialchars((isset($compte['Role']) ? $compte['Role'] : '—')); ?></span></p>
      <p><strong>Statut du compte :</strong> <?php echo htmlspecialchars((isset($compte['Statut_Compte']) ? $compte['Statut_Compte'] : '—')); ?></p>
      <p><strong>Compte créé le :</strong> <?php echo htmlspecialchars((isset($compte['Date_Creation_Compte']) ? $compte['Date_Creation_Compte'] : '—')); ?></p>
      <p style="margin-top:1rem;"><a href="mon-projet.php" class="btn btn-primary btn-sm">Modifier mon profil</a></p>
    </div>
  </main>
</div>

</body>
</html>
