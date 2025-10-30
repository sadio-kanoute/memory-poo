<?php
// Page affichant le top 10 (meilleurs scores = plus petit score)
$cfg = require __DIR__ . '/config.php';
try{
  $pdo = new PDO($cfg['dsn'], $cfg['db_user'], $cfg['db_pass'], $cfg['pdo_options']);
  $stmt = $pdo->query('SELECT p.username, s.pairs, s.moves, s.score, s.created_at FROM scores s JOIN players p ON s.player_id = p.id ORDER BY s.score ASC, s.created_at ASC LIMIT 10');
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $e){
  $rows = [];
  $errorMsg = $e->getMessage();
}
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Hall of Fame — Memory</title>
  <link rel="icon" href="img/CR7logo.jpg" type="image/jpeg">
    <link rel="stylesheet" href="css/styles.css" />
  </head>
  <body>
    <header class="site-header small">
      <div class="container nav-bar">
        <div class="nav-left">
          <a href="index.php" title="Accueil"><img src="img/CR7logo.jpg" class="logo" alt="CR7 logo"/></a>
          <h1 class="brand small-brand">Hall of Fame</h1>
        </div>
        <nav class="nav-links">
          <a class="btn" href="index.php">Accueil</a>
          <a class="btn" href="game.php">Jouer</a>
          <a class="btn" href="profile.php">Profil</a>
          <a class="btn primary" href="halloffame.php">Hall of fame</a>
        </nav>
      </div>
    </header>
    <main class="container main-content">
      <h2>Top 10</h2>
      <?php if(!empty($errorMsg)): ?>
        <p class="error">Impossible de charger le classement : <?= htmlspecialchars($errorMsg) ?></p>
      <?php endif; ?>
      <table class="hof">
        <thead><tr><th>#</th><th>Pseudo</th><th>Paires</th><th>Coups</th><th>Score</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach($rows as $i => $r): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($r['username']) ?></td>
            <td><?= (int)$r['pairs'] ?></td>
            <td><?= (int)$r['moves'] ?></td>
            <td><?= htmlspecialchars($r['score']) ?></td>
            <td><?= htmlspecialchars($r['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </main>
  </body>
</html>
