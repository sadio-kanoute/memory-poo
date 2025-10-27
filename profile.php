<?php
session_start();
// simple gestion de profil : registre / connexion via session
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['username'] ?? '');
    if($name !== ''){
        // création/connexion : stocker en session
        $_SESSION['username'] = $name;
        header('Location: profile.php');
        exit;
    }
}
$username = $_SESSION['username'] ?? '';
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Memory — Mon profil</title>
  <link rel="icon" href="img/CR7logo.jpg" type="image/jpeg">
    <link rel="stylesheet" href="css/styles.css" />
  </head>
  <body>
    <header class="site-header small">
      <div class="container nav-bar">
        <div class="nav-left">
          <a href="index.php" title="Accueil"><img src="img/CR7logo.jpg" class="logo" alt="CR7 logo"/></a>
          <h1 class="brand small-brand">Mon profil</h1>
        </div>
        <nav class="nav-links">
          <a class="btn" href="index.php">Accueil</a>
          <a class="btn" href="game.php">Jouer</a>
          <a class="btn primary" href="profile.php">Profil</a>
          <a class="btn" href="halloffame.php">Hall of fame</a>
        </nav>
      </div>
    </header>
    <main class="container main-content">
      <?php if($username): ?>
        <h2>Bonjour, <?= htmlspecialchars($username) ?></h2>
        <p>Vous êtes connecté·e pour jouer et sauvegarder vos scores.</p>
        <form method="post" action="">
          <button name="logout" value="1" class="btn">Se déconnecter</button>
        </form>
        <?php if(isset($_POST['logout'])){ session_unset(); session_destroy(); header('Location: profile.php'); exit;} ?>
      <?php else: ?>
        <h2>Créer / choisir un profil</h2>
        <form method="post" action="profile.php">
          <label for="username">Pseudo :</label>
          <input id="username" name="username" required />
          <button type="submit" class="btn primary">Sauvegarder</button>
        </form>
      <?php endif; ?>
    </main>
  </body>
</html>
