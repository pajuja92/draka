<?php
    $draka_mainpage_url = get_theme_mod('default_mainpage_url');
    $draka_ankieta_url = get_theme_mod('default_ankieta_url');
    $draka_ranking_url = get_theme_mod('default_ranking_url');
?>

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
        <a class="clear-style" href="<?php echo esc_url( $draka_ankieta_url ); ?>">
            <div id="site-logo">
                <h1 class="logo-heading">
                    <span class="logo-letter letter-1">D</span>
                    <span class="logo-letter letter-2">R</span>
                    <span class="logo-letter letter-3">A</span>
                    <span class="logo-letter letter-4">K</span>
                    <span class="logo-letter letter-5">A</span>
                </h1>
            </div>
        </a>


      <nav id="top-menu">
        <ul>
          <li class="top-menu-item item-1">
            <a href="<?php echo esc_url( $draka_ankieta_url ); ?>"> <!-- // grola: escapowanie: <?php echo esc_url( $draka_mainpage_url ); ?> -->
              <img src="<?php echo DRAKA_URL . 'img/header/main.png'; ?>" alt="Strona Główna"> <!-- // grola: escapowanie, poniżej również trzeba dodać tam gdzie nie ma. -->
              <h4 class="menu-heading">Ankieta</h4>
            </a>
          </li>
          <li class="top-menu-item item-2">
            <a href="<?php echo $draka_ranking_url; ?>">
              <img src="<?php echo DRAKA_URL . 'img/header/ranking.png'; ?>" alt="Ranking">
              <h4 class="menu-heading">Ranking</h4>
            </a>
          </li>
          <li class="top-menu-item item-3">
            <a href="<?php echo get_site_url() . '/wp-admin/profile.php'; ?>">
              <?php
                $wybrana_metodyka = get_field('wybrana_metodyka', 'user_'. get_current_user_id() );
                if( $wybrana_metodyka ) :
              ?>
                <img src="<?php echo DRAKA_URL . 'img/header/znak-met-' . $wybrana_metodyka . '.png'; ?>" alt="Moje konto">
                <h4 class="menu-heading">Moje konto</h4>
              <?php else: ?>
                <img src="<?php echo DRAKA_URL . 'img/header/account.png'; ?>" alt="Moje konto">
                <h4 class="menu-heading">Moje konto</h4>
              <?php endif; ?>

            </a>
          </li>
          <?php if( !is_user_logged_in() ): ?>
            <li class="top-menu-item item-4">
              <a href="<?php echo wp_login_url(); ?>">
                <img src="<?php echo DRAKA_URL . 'img/header/account.png'; ?>" alt="Zaloguj">
                <h4 class="menu-heading">Wyloguj</h4>
              </a>
            </li>
          <?php else: ?>
          <li class="top-menu-item item-4">
            <a href="<?php echo wp_logout_url(); ?>">
              <img src="<?php echo DRAKA_URL . 'img/header/account.png'; ?>" alt="Zaloguj">
              <h4 class="menu-heading">Wyloguj</h4>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </nav>
    </header>
