<?php
$cfg = require __DIR__ . '/config.php';
?>
<!doctype html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Memory — Accueil</title>
  <link rel="icon" href="img/CR7logo.jpg" type="image/jpeg">
    <link rel="stylesheet" href="css/styles.css" />
  </head>
  <body>
    <header class="site-header hero">
      <div class="container nav-bar">
        <div class="nav-left">
          <a href="index.php" title="Accueil"><img src="img/CR7logo.jpg" class="logo" alt="CR7 logo"/></a>
          <div>
            <h1 class="brand small-brand">Memory</h1>
            <p class="tagline">Dispositif permettant de recueillir et de conserver les informations — moments iconiques</p>
          </div>
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
      <section class="about">
        <h2>Descriptif du projet</h2>
        <p>Recréez le jeu Memory avec vos images. Choisissez le nombre de paires (min 3, max 12), jouez et enregistrez votre score dans le classement des 10 meilleurs.</p>
        <ul>
          <li>Choix du nombre de paires : 3 à 12</li>
          <li>Sauvegarde des profils et scores (top 10)</li>
          <li>Design responsive et tableau des meilleurs</li>
        </ul>
      </section>

      <section class="gallery-preview">
        <h3>Galerie des images utilisées</h3>
        <div id="preview" class="gallery"></div>
      </section>
    </main>

    <footer class="site-footer">
      <div class="container">
        <small>&copy; Memory — Projet pédagogique</small>
      </div>
    </footer>

    <script src="js/preview.js"></script>
  </body>
</html>
