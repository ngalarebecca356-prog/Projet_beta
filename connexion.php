<?php
session_start();
require 'config.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email       = trim((isset($_POST['email']) ? $_POST['email'] : ''));
    $motdepasse  = (isset($_POST['password']) ? $_POST['password'] : '');

    $stmt = $pdo->prepare(
        'SELECT u.Matricule_Employer, u.mdp, u.Role, u.Statut_Compte,
                e.Nom, e.Prenom
         FROM utilisateur u
         JOIN employe e ON e.Matricule_Employer = u.Matricule_Employer
         WHERE e.Email = :email'
    );
    $stmt->execute(array('email' => $email));
    $compte = $stmt->fetch();

    if ($compte && password_verify($motdepasse, $compte['mdp'])) {
        if ($compte['Statut_Compte'] !== 'Actif') {
            $erreur = "Ce compte n'est pas actif. Contacte un administrateur.";
        } else {
            $_SESSION['matricule_employe'] = $compte['Matricule_Employer'];
            $_SESSION['nom_complet']       = $compte['Prenom'] . ' ' . $compte['Nom'];
            $_SESSION['role']              = $compte['Role'];
            header('Location: tableau-de-bord.php');
            exit;
        }
    } else {
        $erreur = 'Adresse e-mail ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>projet-MANAGER — Connexion</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-page">
  <aside class="auth-visual">
    <div class="brand">
      <svg class="brand-mark" viewBox="0 0 44 44" fill="none">
        <circle cx="22" cy="22" r="22" fill="#4c6fa5"/>
        <circle cx="22" cy="22" r="12" stroke="#fff" stroke-width="1.4" fill="none"/>
        <ellipse cx="22" cy="22" rx="12" ry="5" stroke="#fff" stroke-width="1.2" fill="none"/>
        <line x1="10" y1="22" x2="34" y2="22" stroke="#fff" stroke-width="1.2"/>
        <line x1="22" y1="10" x2="22" y2="34" stroke="#fff" stroke-width="1.2"/>
      </svg>
      <div>
        <div class="brand-name">projet-MANAGER</div>
        <div class="brand-sub">Gestion de la ESN</div>
      </div>
    </div>

    <h2>Gardez le contrôle total sur vos services numériques</h2>

    <div class="auth-illustration">
      <svg viewBox="0 0 400 260" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="200" cy="238" rx="130" ry="14" fill="rgba(15,26,51,.08)"/>
        <rect x="60" y="90" width="62" height="115" rx="31" fill="#20325f"/>
        <circle cx="91" cy="70" r="26" fill="#f0c9a0"/>
        <rect x="200" y="98" width="58" height="107" rx="29" fill="#4c6fa5"/>
        <circle cx="229" cy="80" r="24" fill="#e8b48c"/>
        <rect x="130" y="126" width="98" height="72" rx="9" fill="#ffffff" stroke="#ffffff" stroke-width="3"/>
        <rect x="144" y="140" width="26" height="9" rx="3" fill="#aec9ea"/>
        <rect x="144" y="156" width="68" height="5" rx="2.5" fill="#dbe8f8"/>
        <rect x="144" y="167" width="50" height="5" rx="2.5" fill="#dbe8f8"/>
        <rect x="144" y="178" width="34" height="12" rx="3" fill="#7cb63c"/>
      </svg>
    </div>
  </aside>

  <main class="auth-form-panel">
    <div class="auth-card">
      <h1>PAGE DE CONNEXION</h1>

      <?php if ($erreur): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($erreur); ?></div>
      <?php endif; ?>

      <form method="post" action="connexion.php">
        <div class="field">
          <label for="email">Adresse e-mail</label>
          <input type="email" id="email" name="email" placeholder="votre@email.com" value="<?php echo htmlspecialchars((isset($_POST['email']) ? $_POST['email'] : '')); ?>" required>
        </div>

        <div class="field">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.75rem;">
          <label class="check-row">
            <input type="checkbox" name="remember">
            Se souvenir de moi
          </label>
          <a href="#" class="btn-link" style="font-size:.85rem;">Mot de passe oublié&nbsp;?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Se connecter</button>

        <p class="form-foot-link" style="text-align:center; margin-top:1.5rem;">
          Vous n'avez pas de compte&nbsp;? <a href="inscription.php" class="btn-link">S'inscrire</a>
        </p>
      </form>
    </div>
  </main>
</div>

</body>
</html>