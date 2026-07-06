<?php
require 'includes/auth.php';
require 'config.php';

$erreur = '';
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = trim((isset($_POST['matricule']) ? $_POST['matricule'] : ''));
    $codeProjet = trim((isset($_POST['code_projet']) ? $_POST['code_projet'] : ''));
    $role = trim((isset($_POST['role']) ? $_POST['role'] : ''));
    $taux = trim((isset($_POST['taux']) ? $_POST['taux'] : ''));

    if ($matricule === '' || $codeProjet === '') {
        $erreur = "L'employé et le projet sont obligatoires.";
    } else {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO affectation (Matricule_Employer, Code_Projet, Role_Affectation, Taux_Affectation)
                 VALUES (:matricule, :code_projet, :role, :taux)'
            );
            $stmt->execute(array(
                'matricule' => $matricule,
                'code_projet' => $codeProjet,
                'role' => $role ?: null,
                'taux' => $taux ?: null,
            ));
            $succes = true;
        } catch (Exception $e) {
            $erreur = "Impossible d'enregistrer : vérifie que le matricule et le code projet existent bien.";
        }
    }
}

$employes = $pdo->query("SELECT Matricule_Employer, Nom, Prenom FROM employe ORDER BY Nom")->fetchAll();
$projets  = $pdo->query("SELECT Code_Projet, Intitule_Projet FROM projet ORDER BY Intitule_Projet")->fetchAll();

$active_page = 'affectations';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PROJET-MANAGER — Nouvelle affectation</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-shell">
  <?php include 'includes/sidebar.php'; ?>

  <main class="main-content">
    <div class="page-header">
      <h1>Nouvelle affectation</h1>
    </div>

    <div class="card">
      <?php if ($erreur): ?><div class="alert alert-error"><?php echo htmlspecialchars($erreur); ?></div><?php endif; ?>
      <?php if ($succes): ?>
        <div class="alert alert-success">Affectation créée. <a href="affectation.php" class="btn-link">Voir la liste</a></div>
      <?php else: ?>
      <form method="post" action="affectation-nouvelle.php">
        <div class="field">
          <label for="matricule">Employé</label>
          <select id="matricule" name="matricule" required>
            <option value="">— Choisir —</option>
            <?php foreach ($employes as $e): ?>
              <option value="<?php echo htmlspecialchars($e['Matricule_Employer']); ?>"><?php echo htmlspecialchars($e['Prenom'] . ' ' . $e['Nom']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="code_projet">Projet</label>
          <select id="code_projet" name="code_projet" required>
            <option value="">— Choisir —</option>
            <?php foreach ($projets as $p): ?>
              <option value="<?php echo htmlspecialchars($p['Code_Projet']); ?>"><?php echo htmlspecialchars((isset($p['Intitule_Projet']) ? $p['Intitule_Projet'] : $p['Code_Projet'])); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="field">
          <label for="role">Rôle</label>
          <input type="text" id="role" name="role" placeholder="Ex: Développeur">
        </div>
        <div class="field">
          <label for="taux">Taux d'affectation (%)</label>
          <input type="number" id="taux" name="taux" min="0" max="100" placeholder="Ex: 50">
        </div>
        <button type="submit" class="btn btn-primary">Créer l'affectation</button>
      </form>
      <?php endif; ?>
    </div>
  </main>
</div>

</body>
</html>
