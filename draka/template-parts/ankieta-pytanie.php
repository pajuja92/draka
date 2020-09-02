<?php
$draka = Draka::get_instance();
$wybrana_metodyka = $draka->get_user_met();
global $numer_pytania;

$tip = wp_strip_all_tags( get_the_content( get_the_ID() ) );
echo '<h3 class="question ' . ($tip ? "tip-after" : 0) . '" title="' . $tip  . '">' . $numer_pytania++ . ". " . get_the_title() . '</h3>';

if( have_rows('odpowiedzi') ):

    $i = 0;
    while ( have_rows('odpowiedzi') ) : the_row();
        $poziom_min = get_sub_field('obowiazkowe_' . $wybrana_metodyka );
        $multi = $draka->is_level_smaller( $draka->get_user_level(0), $poziom_min );

        echo '<p class="multii">' . $multi  .'</p>';
        $tip_q = get_sub_field( 'podpowiedz_pytania' );
        $punkty = $draka->get_points( get_the_ID(), $i );
        if( $multi  && $multi != 1 ) {
          echo '<label class="draka_answer ' . ($tip_q ? "tip-after" : 0) . '" title="' . $tip_q . '"><input type="radio" name="' . get_the_ID() . '" value="' . $i . '" >' . get_sub_field('tresc_odpowiedzi') . ' (<span class="multi" title="Za to pytanie możesz zdobyć maksymalnie ' . (int)( $multi * 100 ) . '% z całości punktów. Aby podwyższyć procent zdobądź następny poziom.">pkt: ' . $draka->get_points( get_the_ID(), $i ) * $multi . '</span>, min: ' . $poziom_min . ')' .'</label>';
        } else {
          echo '<label class="draka_answer ' . ($tip_q ? "tip-after" : 0) . '" title="' . $tip_q . '"><input type="radio" name="' . get_the_ID() . '" value="' . $i . '" >' . get_sub_field('tresc_odpowiedzi') . ' (pkt: ' . $draka->get_points( get_the_ID(), $i ) . ', min: ' . $poziom_min . ')' .'</label>';
        }
        $i++;
    endwhile;

else :
    echo __('Brak pytań do wyświetlenia', 'draka');
endif;
