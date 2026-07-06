<?php
require 'includes/auth.php';
require 'config.php';

$matricule = $_SESSION['matricule_employe'];
$erreurProfil = '';
$succesProfil = '';
$erreurMdp = '';
$succesMdp = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ((isset($_POST['form']) ? $_POST['form'] : '')) === 'profil') {
    $nom    = trim((isset($_POST['nom']) ? $_POST['nom'] : ''));
    $prenom = trim((isset($_POST['prenom']) ? $_POST['prenom'] : ''));
    $email  = trim((isset($_POST['email']) ? $_POST['email'] : ''));
    $poste  = trim((isset($_POST['poste']) ? $_POST['poste'] : ''));
    $tel    = trim((isset($_POST['tel']) ? $_POST['tel'] : ''));

    if ($nom === '' || $prenom === '' || $email === '') {
        $erreurProfil = 'Nom, prénom et email sont obligatoires.';
    } else {
        $update = $pdo->prepare(
            'UPDATE employe SET Nom = :nom, Prenom = :prenom, Email = :email, Poste = :poste, Tel = :tel
             WHERE Matricule_Employer = :matricule'
        );
        $update->execute(array(
            'nom' => $nom, 'prenom' => $prenom, 'email' => $email,
            'poste' => $poste, 'tel' => $tel, 'matricule' => $matricule,
        ));
        $_SESSION['nom_complet'] = $prenom . ' ' . $nom;
        $succesProfil = 'Informations mises à jour.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ((isset($_POST['form']) ? $_POST['form'] : '')) === 'password') {
    $actuel    = (isset($_POST['pwd_actuel']) ? $_POST['pwd_actuel'] : '');
    $nouveau   = (isset($_POST['pwd_nouveau']) ? $_POST['pwd_nouveau'] : '');
    $confirmer = (isset($_POST['pwd_confirm']) ? $_POST['pwd_confirm'] : '');

    $stmt = $pdo->prepare('SELECT mdp FROM utilisateur WHERE Matricule_Employer = :matricule');
    $stmt->execute(array('matricule' => $matricule));
    $hashActuel = $stmt->fetchColumn();

    if (!password_verify($actuel, $hashActuel)) {
        $erreurMdp = 'Le mot de passe actuel est incorrect.';
    } elseif (strlen($nouveau) < 8) {
        $erreurMdp = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
    } elseif ($nouveau !== $confirmer) {
        $erreurMdp = 'Les deux mots de passe ne correspondent pas.';
    } else {
        $update = $pdo->prepare('UPDATE utilisateur SET mdp = :hash WHERE Matricule_Employer = :matricule');
        $update->execute(array('hash' => password_hash($nouveau, PASSWORD_DEFAULT), 'matricule' => $matricule));
        $succesMdp = 'Mot de passe mis à jour.';
    }
}

$stmt = $pdo->prepare('SELECT * FROM employe WHERE Matricule_Employer = :matricule');
$stmt->execute(array('matricule' => $matricule));
$employe = $stmt->fetch();

$initiales = strtoupper(mb_substr($employe['Prenom'], 0, 1) . mb_substr($employe['Nom'], 0, 1));

$active_page = 'profil';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>projet-MANAGER — Mon projet</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app-shell">
  <?php include 'includes/sidebar.php'; ?>

  <main class="main-content">
    <div class="page-header">
      <h1>Mon projet</h1>
    </div>

    <div class="profile-grid">

      <div class="card">
        <h2>Informations du compte</h2>

        <?php if ($erreurProfil): ?><div class="alert alert-error"><?php echo htmlspecialchars($erreurProfil); ?></div><?php endif; ?>
        <?php if ($succesProfil): ?><div class="alert alert-success"><?php echo htmlspecialchars($succesProfil); ?></div><?php endif; ?>

        <div class="avatar-row">
          <div class="avatar"><?php echo htmlspecialchars($initiales); ?></div>
          <button class="btn btn-mint btn-sm" type="button">Changer de photo</button>
        </div>

        <form method="post" action="mon-projet.php">
          <input type="hidden" name="form" value="profil">
          <div class="field">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($employe['Nom']); ?>">
          </div>
          <div class="field">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($employe['Prenom']); ?>">
          </div>
          <div class="field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employe['Email']); ?>">
          </div>
          <div class="field">
            <label for="poste">Poste</label>
            <input type="text" id="poste" name="poste" value="<?php echo htmlspecialchars((isset($employe['Poste']) ? $employe['Poste'] : '')); ?>">
          </div>
          <div class="field">
            <label for="tel">Téléphone</label>
            <input type="tel" id="tel" name="tel" value="<?php echo htmlspecialchars((isset($employe['Tel']) ? $employe['Tel'] : '')); ?>">
          </div>

          <button type="submit" class="btn btn-primary">Modifier les informations</button>
        </form>
      </div>

      <div class="card">
        <h2>Changer le mot de passe</h2>

        <?php if ($erreurMdp): ?><div class="alert alert-error"><?php echo htmlspecialchars($erreurMdp); ?></div><?php endif; ?>
        <?php if ($succesMdp): ?><div class="alert alert-success"><?php echo htmlspecialchars($succesMdp); ?></div><?php endif; ?>

        <form method="post" action="mon-projet.php">
          <input type="hidden" name="form" value="password">
          <div class="field">
            <label for="pwd-actuel">Mot de passe actuel</label>
            <input type="password" id="pwd-actuel" name="pwd_actuel" placeholder="••••••••">
          </div>
          <div class="field">
            <label for="pwd-nouveau">Nouveau mot de passe</label>
            <input type="password" id="pwd-nouveau" name="pwd_nouveau" placeholder="••••••••">
          </div>
          <div class="field">
            <label for="pwd-confirm">Confirmer le nouveau mot de passe</label>
            <input type="password" id="pwd-confirm" name="pwd_confirm" placeholder="••••••••">
          </div>

          <button type="submit" class="btn btn-success">Mettre à jour le mot de passe</button>
        </form>
      </div>

    </div>
  </main>
</div>

</body>
</html>