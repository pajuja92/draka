
<?php global $kategoria; ?>
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
      global $numer_pytania;
      $numer_pytania = 1;
      while ( $pytania->have_posts() ) : $pytania->the_post();
        get_plugin_part_template('template-parts/ankieta-pytanie');
      endwhile; wp_reset_postdata();
    endif;
  }
?>

</div>
