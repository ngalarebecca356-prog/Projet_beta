<?php
session_start();
require 'config.php';

$erreur = '';
$succes = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom          = trim((isset($_POST['nom']) ? $_POST['nom'] : ''));
    $prenom       = trim((isset($_POST['prenom']) ? $_POST['prenom'] : ''));
    $email        = trim((isset($_POST['email']) ? $_POST['email'] : ''));
    $motdepasse   = (isset($_POST['password']) ? $_POST['password'] : '');
    $confirmation = (isset($_POST['password2']) ? $_POST['password2'] : '');
    $telephone    = trim((isset($_POST['tel']) ? $_POST['tel'] : ''));

    if ($nom === '' || $prenom === '' || $email === '' || $motdepasse === '') {
        $erreur = 'Merci de remplir tous les champs obligatoires.';
    } elseif ($motdepasse !== $confirmation) {
        $erreur = 'Les deux mots de passe ne correspondent pas.';
    } elseif (strlen($motdepasse) < 8) {
        $erreur = 'Le mot de passe doit contenir au moins 8 caractères.';
    } else {
        $verif = $pdo->prepare('SELECT 1 FROM employe WHERE Email = :email');
        $verif->execute(array('email' => $email));

        if ($verif->fetch()) {
            $erreur = 'Un compte existe déjà avec cet email.';
        } else {
            // Génère le prochain matricule (EMP001, EMP002...)
            $dernier = $pdo->query('SELECT Matricule_Employer FROM employe ORDER BY Matricule_Employer DESC LIMIT 1')->fetchColumn();
            $prochainNumero = $dernier ? ((int) substr($dernier, 3)) + 1 : 1;
            $matricule = 'EMP' . str_pad((string) $prochainNumero, 3, '0', STR_PAD_LEFT);

            // NB : Salaire, Poste, Departement et Contrat existent dans ton dictionnaire
            // mais ne sont pas saisis par ce formulaire public. On les initialise à une
            // valeur provisoire, à corriger ensuite par un administrateur.
            $pdo->beginTransaction();
            try {
                $insertEmploye = $pdo->prepare(
                    'INSERT INTO employe
                        (Matricule_Employer, Nom, Prenom, Date_Embauche, Salaire, Email, Poste, Contrat, Statut_Contrat, Tel)
                     VALUES
                        (:matricule, :nom, :prenom, CURDATE(), 0, :email, "À définir", "À définir", "Actif", :tel)'
                );
                $insertEmploye->execute(array(
                    'matricule' => $matricule,
                    'nom'       => $nom,
                    'prenom'    => $prenom,
                    'email'     => $email,
                    'tel'       => $telephone,
                ));

                $insertUtilisateur = $pdo->prepare(
                    'INSERT INTO utilisateur (Matricule_Employer, mdp, Role, Date_Creation_Compte, Statut_Compte)
                     VALUES (:matricule, :hash, "Collaborateur", NOW(), "Actif")'
                );
                $insertUtilisateur->execute(array(
                    'matricule' => $matricule,
                    'hash'      => password_hash($motdepasse, PASSWORD_DEFAULT),
                ));

                $pdo->commit();
                $succes = true;
            } catch (Exception $e) {
                $pdo->rollBack();
                $erreur = 'Une erreur est survenue, réessaie.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>projet-MANAGER — Inscription</title>
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
      <h1>Page d'inscription</h1>

      <?php if ($erreur): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($erreur); ?></div>
      <?php endif; ?>

      <?php if ($succes): ?>
        <div class="alert alert-success">Compte créé avec succès. <a href="connexion.php" class="btn-link">Se connecter</a></div>
      <?php else: ?>
      <form method="post" action="inscription.php">
        <div class="field-row">
          <div class="field">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" placeholder="Votre nom" value="<?php echo htmlspecialchars((isset($_POST['nom']) ? $_POST['nom'] : '')); ?>" required>
          </div>
          <div class="field">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" placeholder="Votre prénom" value="<?php echo htmlspecialchars((isset($_POST['prenom']) ? $_POST['prenom'] : '')); ?>" required>
          </div>
        </div>

        <div class="field">
          <label for="email">Email professionnel</label>
          <input type="email" id="email" name="email" placeholder="votre@email.com" value="<?php echo htmlspecialchars((isset($_POST['email']) ? $_POST['email'] : '')); ?>" required>
        </div>

        <div class="field">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" placeholder="••••••••" required>
        </div>

        <div class="field">
          <label for="password2">Confirmer le mot de passe</label>
          <input type="password" id="password2" name="password2" placeholder="••••••••" required>
        </div>

        <div class="field">
          <label for="tel">Téléphone</label>
          <input type="tel" id="tel" name="tel" placeholder="Votre numéro professionnel" value="<?php echo htmlspecialchars((isset($_POST['tel']) ? $_POST['tel'] : '')); ?>">
        </div>

        <button type="submit" class="btn btn-primary btn-block">Créer un compte</button>

        <p class="form-foot-link" style="text-align:center; margin-top:1.5rem;">
          Vous avez déjà un compte&nbsp;? <a href="connexion.php" class="btn-link">Se connecter</a>
        </p>
      </form>
      <?php endif; ?>
    </div>
  </main>
</div>

</body>
</html>