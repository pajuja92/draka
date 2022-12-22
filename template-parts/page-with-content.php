<?php get_plugin_part_template('template-parts/header-draka'); ?>

<div id="site-content">
  <div id="main-container" class="col-8-d col-12-t col-12-m">

    <?php
      echo "<h1>" . get_the_title() . "</h1>";
      $post = get_post( get_the_ID() );
      $content = apply_filters('the_content', $post->post_content);
      echo $content;
     ?>

  </div>
  <div class="col-4-d col-0-t col-0-m">
      <img src='<?php echo DRAKA_URL . "img/parts/lion.png"; ?>' alt="Lew prawy" id="lion-right" class="sticky">
  </div>
</div>

<?php get_plugin_part_template('template-parts/footer-draka'); ?>
