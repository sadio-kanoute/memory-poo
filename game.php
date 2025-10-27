<?php
session_start();
// page de jeu : frontend en JS, sauvegarde via save_score.php
$username = $_SESSION['username'] ?? '';
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Memory — Jouer</title>
  <link rel="icon" href="img/CR7logo.jpg" type="image/jpeg">
    <link rel="stylesheet" href="css/styles.css" />
  </head>
  <body>
    <header class="site-header small">
      <div class="container nav-bar">
        <div class="nav-left">
          <a href="index.php" title="Accueil"><img src="img/CR7logo.jpg" class="logo" alt="CR7 logo"/></a>
          <h1 class="brand small-brand">Memory — Jouer</h1>
        </div>
        <nav class="nav-links">
          <a class="btn" href="index.php">Accueil</a>
          <a class="btn primary" href="game.php">Jouer</a>
          <a class="btn" href="profile.php">Profil</a>
          <a class="btn" href="halloffame.php">Hall of fame</a>
        </nav>
      </div>
    </header>

    <main class="container main-content">
      <section class="game-setup">
        <label for="pairs">Nombre de paires (3–12) :</label>
        <input id="pairs" type="number" min="3" max="12" value="6" />
        <button id="startBtn" class="btn primary">Démarrer</button>
        <div id="status">Prêt</div>
      </section>

      <section id="gameArea" class="game-area" aria-live="polite">
        <!-- le jeu est généré par js/memory.js -->
      </section>
      
      <!-- Overlay écran de fin -->
      <div id="endOverlay" class="end-overlay" aria-hidden="true">
        <div class="end-card" role="dialog" aria-labelledby="endTitle">
          <h3 id="endTitle">Partie terminée</h3>
          <p id="endScore">Votre score : 0.00 (coups: 0)</p>
          <div class="end-actions">
            <button id="btnSave" class="btn primary">Enregistrer le score</button>
            <button id="btnSkip" class="btn">Ne pas enregistrer</button>
            <button id="btnRestart" class="btn">Relancer partie</button>
            <a href="profile.php" class="btn">Votre profil</a>
            <a href="halloffame.php" class="btn">Hall of fame</a>
          </div>
        </div>
      </div>
    </main>

    <footer class="site-footer">
      <div class="container">
        <small>Memory — Jouez et comparez vos scores</small>
      </div>
    </footer>

    <script>
      window.MEMORY_CONFIG = {
        username: <?= json_encode($username) ?>
      };
    </script>
    <script src="js/memory.js"></script>
  </body>
</html>
