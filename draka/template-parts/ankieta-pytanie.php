<?php
$draka = Draka::get_instance();
$user = User::get_instance();
$wybrana_metodyka = $draka->get_user_met();

$newQuestions = Questions::get_instance();

global $numer_pytania;


$tipText = $newQuestions->get_tip_text( get_the_ID() );
$questionText = $newQuestions->get_question_content( get_the_ID() );
echo '<h3 class="question ' . ($tipText ? "tip-after" : 0) . '" title="' . $tipText  . '">' . $numer_pytania++ . ". " . $questionText . '</h3>';


$options = $newQuestions->get_options( get_the_ID() );


$i = 0; // dzięki temu nie można modyfikować z kodu ilości punktów.
foreach ($options as $option ) {
    $questionScore = $newQuestions->get_question_score( get_the_ID(), $i, $user->get_ageGroup() )

    ?>

    <label class="draka_option <?php echo ($option[ 'tip' ] ? "tip-after" : 0) ?>" title="<?php echo $option[ 'tip' ]; ?>">
        <input type="radio" name="<?php echo get_the_ID() ?>" value="<?php echo $i; ?>" required >
        <?php echo $option[ 'content' ] . ' (pkt: ' . $questionScore . ')' ; ?>
    </label>

    <?php
    $i++;
}


