<?php get_plugin_part_template('template-parts/header-draka'); ?>

<div id="site-content">

  <div class="col-4-d col-0-t col-0-m">
    <img src="<?php echo DRAKA_URL . 'img/parts/lion.png'; ?>" alt="Lew lewy" id="lion-left">
  </div>

  <div class="col-6-d col-12-t col-12-m">
    <h1 class="site-heading">Strona główna</h1>
    <?php wp_nav_menu( array(
      'container'      => '',
      'theme-location' => 'draka_main_menu',
      'items_wrap'     => '<ul id="menu-main">%3$s</ul>',
      'container_class'      => 'qweqwe',
    )); ?>
  </div>

  <div class="col-4-d col-0-t col-0-m">
    <img src="<?php echo DRAKA_URL . 'img/parts/lion.png'; ?>" alt="Lew prawy" id="lion-right">
  </div>
</div>


<?php get_plugin_part_template('template-parts/footer-draka');
