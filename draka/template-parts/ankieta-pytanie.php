<?php
// global $draka;
$draka = Draka::get_instance();
$wybrana_metodyka = $draka->get_user_met();

echo '<h3 class="question">' . get_the_title() . '</h3>';

if( have_rows('odpowiedzi') ):

    $i = 0;
    while ( have_rows('odpowiedzi') ) : the_row();
        echo '<label><input type="radio" name="' . get_the_ID() . '" value="' . $i . '" >' . get_sub_field('tresc_odpowiedzi') . ' (pkt: ' . $punkty . ', min: ' . get_sub_field('obowiazkowe_' . $wybrana_metodyka ) . ')' .'</label>';
        $i++;
    endwhile;

else :
    echo __('Brak pytań do wyświetlenia', 'draka');
endif;
