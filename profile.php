<?php
session_start();

// CSRF helpers
function csrf_token()
{
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['csrf'];
}
function verify_csrf($token)
{
  return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$token);
}

$cfg = require __DIR__ . '/config.php';
$pdo = null;
try{
  $pdo = new PDO($cfg['dsn'], $cfg['db_user'], $cfg['db_pass'], $cfg['pdo_options']);
}catch(Exception $e){
  $pdo = null;
}

$error = '';
$username = $_SESSION['username'] ?? '';
$player_id = $_SESSION['player_id'] ?? null;

// Handle POST actions: register, login, logout
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $action = $_POST['action'] ?? '';
  if(!verify_csrf($_POST['csrf'] ?? '')){
    $error = 'Jeton CSRF invalide.';
  } else {
    if($action === 'logout'){
      session_unset(); session_destroy(); header('Location: profile.php'); exit;
    }
    if(!$pdo){ $error = 'Impossible de se connecter à la base.'; }
    else {
      if($action === 'register'){
        $name = trim($_POST['username'] ?? '');
        $pass = $_POST['password'] ?? '';
        if($name === ''){ $error = 'Pseudo requis.'; }
        else {
          // check exists
          $stmt = $pdo->prepare('SELECT id, password_hash FROM players WHERE username = :u');
          $stmt->execute([':u'=>$name]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          if($row){
            $error = 'Pseudo déjà utilisé.';
          } else {
            $pwdHash = $pass !== '' ? password_hash($pass, PASSWORD_DEFAULT) : null;
            $stmt = $pdo->prepare('INSERT INTO players (username,password_hash) VALUES (:u,:ph)');
            $stmt->execute([':u'=>$name,':ph'=>$pwdHash]);
            $pid = $pdo->lastInsertId();
            session_regenerate_id(true);
            $_SESSION['player_id'] = $pid;
            $_SESSION['username'] = $name;
            header('Location: profile.php'); exit;
          }
        }
      } elseif($action === 'login'){
        $name = trim($_POST['username'] ?? '');
        $pass = $_POST['password'] ?? '';
        if($name === ''){ $error = 'Pseudo requis.'; }
        else {
          $stmt = $pdo->prepare('SELECT id,password_hash FROM players WHERE username = :u');
          $stmt->execute([':u'=>$name]);
          $row = $stmt->fetch(PDO::FETCH_ASSOC);
          if(!$row){
            $error = 'Utilisateur non trouvé.';
          } else {
            $hash = $row['password_hash'];
            if($hash && !password_verify($pass, $hash)){
              $error = 'Mot de passe incorrect.';
            } else {
              // allow login for accounts without password (legacy) if none provided
              session_regenerate_id(true);
              $_SESSION['player_id'] = $row['id'];
              $_SESSION['username'] = $name;
              header('Location: profile.php'); exit;
            }
          }
        }
      }
    }
  }
}
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
        <form method="post" action="profile.php">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>" />
          <input type="hidden" name="action" value="logout" />
          <button class="btn">Se déconnecter</button>
        </form>

        <?php if($player_id && $pdo):
            // profile stats
            $stmt = $pdo->prepare('SELECT MIN(score) AS best_score, COUNT(*) AS games, AVG(score) AS avg_score FROM scores WHERE player_id = :pid');
            $stmt->execute([':pid'=>$player_id]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
          <section class="profile-stats">
            <h3>Mes statistiques</h3>
            <ul>
              <li>Parties jouées: <?= (int)($stats['games'] ?? 0) ?></li>
              <li>Meilleur score: <?= htmlspecialchars($stats['best_score'] ?? '-') ?></li>
              <li>Score moyen: <?= htmlspecialchars($stats['avg_score'] ? number_format($stats['avg_score'],2) : '-') ?></li>
            </ul>
          </section>
        <?php endif; ?>

      <?php else: ?>
        <h2>Créer / choisir un profil</h2>
        <?php if($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <div class="forms">
          <form method="post" action="profile.php" class="box">
            <h4>Inscription</h4>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>" />
            <input type="hidden" name="action" value="register" />
            <label for="r_username">Pseudo :</label>
            <input id="r_username" name="username" required />
            <label for="r_password">Mot de passe (optionnel) :</label>
            <input id="r_password" name="password" type="password" />
            <button type="submit" class="btn primary">S'inscrire</button>
          </form>

          <form method="post" action="profile.php" class="box">
            <h4>Connexion</h4>
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>" />
            <input type="hidden" name="action" value="login" />
            <label for="l_username">Pseudo :</label>
            <input id="l_username" name="username" required />
            <label for="l_password">Mot de passe :</label>
            <input id="l_password" name="password" type="password" />
            <button type="submit" class="btn">Se connecter</button>
          </form>
        </div>
      <?php endif; ?>
    </main>
  </body>
</html>
