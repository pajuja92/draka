<?php get_plugin_part_template('template-parts/header-draka'); ?>


<?php

$draka = Draka::get_instance();
$draka->load_answers();
$customized_shortcode = get_theme_mod('default_contact_form');

if( !is_user_logged_in() ) {
  echo "<h3 class='warning'>Uwaga! Bez zalogowania część funkcjonalności może nie działać. <a href='" . wp_login_url() . "'>Zaloguj się!</a></h3>";
} elseif( ! $draka->get_user_met() ) {
  echo "<h3 class='warning'>Uwaga! Nie masz wybranej metodyki. Przejdź do  <a href='" . get_edit_profile_url() . "'>panelu edycji profilu</a> i zaktualizuj informacje</h3>";
}

$kategorie = get_terms( 'draka_category', array(
    'hide_empty' => true,
    'parent' => 0,
) );

?>
<div id="site-content">
  <div id="main-container" class="col-8-d col-12-t col-12-m">

    <div class="subcategory-toggle" for="all">
      <h2>Wszystkie</h2>
    </div>
    <?php foreach ($kategorie as $kategoria) {  ?>
      <div class="subcategory-toggle" for="<?php echo $kategoria->slug; ?>">
        <h2><?php echo $kategoria->name; ?></h2>
      </div>
    <?php } ?>

    <div id="draka_form_container">
      <form id="draka_form" action="" onsubmit="qc_process(this);return false;">

        <?php
        foreach ($kategorie as $kategoria) {
          global $kategoria;
          get_plugin_part_template('template-parts/ankieta-kategorie');
        }
        ?>

        <div id="submit-info-box">
          <div id="info_box" class="hidden"></div>
          <input id="send" type="submit" value="Zapisz"/>
        </div>
      </form>
    </div>
  </div>
  <div class="col-4-d col-0-t col-0-m">
    <?php if ( $customized_shortcode ) : ?>
      <?php echo do_shortcode( $customized_shortcode ); ?>
    <?php else: ?>
      <img src='<?php echo DRAKA_URL . "img/parts/lion.png"; ?>' alt="Lew prawy" id="lion-right" class="sticky">
    <?php endif; ?>
  </div>
</div>


<script>
  function qc_process(e){

    var selected = [];
    jQuery('input:checked').each(function() {
        selected.push({
          name: jQuery(this).attr('name'),
          value: jQuery(this).attr('value'), // TODO: zabezpieczyć dodawanie punktów z HTML
        });
    });
    var data = {
        action: "draka_submit_callback",
        user_answers: JSON.stringify( selected ), // ta linia zawiera błąd, dlatego wywala wszystko...
    };
    console.log( e );

    jQuery.post("<?php echo admin_url("admin-ajax.php"); ?>", data, function(response) {
        console.log( "Response: " + response );
        jQuery("#info_box").html(response);
    });
  }
</script>

<?php get_plugin_part_template('template-parts/footer-draka'); ?>
