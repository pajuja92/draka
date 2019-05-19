<?php get_plugin_part_template('template-parts/header-draka'); ?>


<?php

// global $draka;
$draka = Draka::get_instance();

global $draka_templates;
$draka_templates = array();

$kategorie = get_terms( 'draka_category', array(
    'hide_empty' => true,
    'parent' => 0,
) );


$wybrana_metodyka = get_field('wybrana_metodyka', 'user_'. get_current_user_id() );

echo "<h1>Wypełniasz ankietę dla metodyki: " . $wybrana_metodyka . "</h1>";

var_dumb( $draka->get_answers() );

foreach ($kategorie as $kategoria) {
  ?>
  <div class="subcategory-toggle" for="<?php echo $kategoria->slug; ?>">
    <h1><?php echo $kategoria->name; ?></h1>
  </div>
  <?php
}
echo '<div id="qca_form">';
echo '<form id="ContactForm" action="" onsubmit="qc_process(this);return false;">';

foreach ($kategorie as $kategoria) {

  ?>
  <div id="<?php echo $kategoria->slug; ?>" class="subcategory-container">
  <?php
    $podkategorie = get_term_children($kategoria->term_id, 'draka_category');
    foreach( $podkategorie as $podkategoria ) {
      $args = array(
        'post_type' => 'draka',
        'tax_query' => array(
            'relation' => 'AND',
            array(
              'taxonomy'  => 'draka_category',
              'field'     => 'slug',
              'terms'     => $kategoria->slug,
            ),
            array(
              'taxonomy'  => 'draka_category',
              'field'     => 'id',
              'terms'     => $podkategoria
            )
        ),
      );
      $pytania = new WP_Query( $args );
      $kat = get_term_by('id', $podkategoria, 'draka_category' );

      echo '<h2 class="question">' . $kat->name . '</h2>';

      if ( $pytania->have_posts() ) :
      	while ( $pytania->have_posts() ) : $pytania->the_post();
          $draka_templates[ get_the_ID() ] = array(
            'name' => get_the_ID(),
            'scores' => array(),
          );

          $draka->add_answer(
            array(
              'name' => get_the_ID(),
              'scores' => array(),
            )
          );
          get_plugin_part_template('template-parts/ankieta-pytanie');
        endwhile; wp_reset_postdata();
      endif;
    }
  ?>
  </div>
  <?php
}
echo '<input id="send" type="submit" value="Submit"/>';
echo '<div id="info_box"></div>';
echo "</form>";
echo '</div>';

?>
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
