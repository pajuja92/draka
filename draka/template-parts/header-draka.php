<!DOCTYPE html>
<html lang="pl" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=300, initial-scale=1">
    <title>DRAKA - Grywalizacja Hufca Gdańsk-Śródmieście</title>
    <link rel="stylesheet" href="<?php echo DRAKA_URL . 'css/style.css'; ?>" type="text/css" />
    <link rel="stylesheet" href="<?php echo DRAKA_URL . 'css/fonts/stylesheet.css'; ?>" type="text/css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="<?php echo DRAKA_URL . 'js/scripts.js'; ?>"></script>
  </head>
  <body>
    <header>
      <div id="site-logo">
        <h1 class="logo-heading">
          <span class="logo-letter letter-1">D</span>
          <span class="logo-letter letter-2">R</span>
          <span class="logo-letter letter-3">A</span>
          <span class="logo-letter letter-4">K</span>
          <span class="logo-letter letter-5">A</span>
      </h1>
      </div>

      <nav id="top-menu">
        <ul>
          <li class="top-menu-item item-1">
            <a href="<?php echo get_site_url(); ?>">
              <img src="<?php echo DRAKA_URL . 'img/header/main.png'; ?>" alt="Strona Główna">
              <h4 class="menu-heading">Strona Główna</h4>
            </a>
          </li>
          <li class="top-menu-item item-2">
            <a href="">
              <img src="<?php echo DRAKA_URL . 'img/header/ranking.png'; ?>" alt="Ranking">
              <h4 class="menu-heading">Ranking</h4>
            </a>
          </li>
          <li class="top-menu-item item-3">
            <a href="<?php echo get_site_url() . '/wp-admin/profile.php'; ?>">
              <img src="<?php echo DRAKA_URL . 'img/header/account.png'; ?>" alt="Moje konto">
              <h4 class="menu-heading">Moje konto</h4>
            </a>
          </li>
          <?php if( !is_user_logged_in() ): ?>
          <li class="top-menu-item item-4">
            <a href="">
              <img src="<?php echo DRAKA_URL . 'img/header/account.png'; ?>" alt="Zaloguj">
              <h4 class="menu-heading">Zaloguj</h4>
            </a>
          </li>
          <?php else: ?>
          <li class="top-menu-item item-4">
            <a href="">
              <img src="<?php echo DRAKA_URL . 'img/header/account.png'; ?>" alt="Zaloguj">
              <h4 class="menu-heading">Wyloguj</h4>
            </a>
          </li>
          <li class="dropdown">
            <ul>
            <?php
            $wybrana_metodyka = get_field('wybrana_metodyka', 'user_'. get_current_user_id() );
            echo "<li class='dropdown-menu-item'><a href='" . get_site_url() . "/wp-admin/profile.php'>" . $wybrana_metodyka . "</a></li>";
            ?>
            </ul>
          </li>
          <?php endif; ?>
        </ul>
      </nav>
    </header>
