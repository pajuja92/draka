<?php
  /*
   * Template Name: My Custom Page
   * Description: A Page Template with a darker design.
   */
?>

<?php

    get_plugin_part_template('template-parts/header-draka');
    $user = User::get_instance();

?>

<script>
    jQuery( document ).ready( function() {
       jQuery( '.choose-met[for=<?php echo$user->get_age_group(); ?>]').click();
    });
</script>

<div id="site-content">
  <div id="main-container" class="col-8-d col-12-t col-12-m">
      <h1 class="site-heading">Ranking</h1>

    <script>
        function get_ajax_response(e) {
          var met = jQuery( e ).attr('for');
          var data = {
              action: "draka_seek_results_callback",
              metodyka: met
          };
          jQuery.post("<?php echo admin_url("admin-ajax.php"); ?>", data, function(response) {
              jQuery('#ranking-table').html(response);
          });
        }
    </script>

    <div class="choose-met-container">
      <button class="choose-met" onclick="get_ajax_response(this);return false;" for="z">
        <img src="<?php echo DRAKA_URL . 'img/header/znak-met-z.png'; ?>" alt="Moje konto">
      </button>
      <button class="choose-met"  onclick="get_ajax_response(this);return false;" for="h">
        <img src="<?php echo DRAKA_URL . 'img/header/znak-met-h.png'; ?>" alt="Moje konto">
      </button>
      <button class="choose-met"  onclick="get_ajax_response(this);return false;" for="hs">
        <img src="<?php echo DRAKA_URL . 'img/header/znak-met-hs.png'; ?>" alt="Moje konto">
      </button>
      <button class="choose-met"  onclick="get_ajax_response(this);return false;" for="w">
        <img src="<?php echo DRAKA_URL . 'img/header/znak-met-w.png'; ?>" alt="Moje konto">
      </button>
    </div>

    <div class="ranking-table-container">
      <table id="ranking-table">
      </table>
    </div>



  </div>

  <div class="col-4-d col-0-t col-0-m">
      <img src='<?php echo DRAKA_URL . "img/parts/lion.png"; ?>' alt="Lew prawy" id="lion-right">
  </div>
</div>

<?php get_plugin_part_template('template-parts/footer-draka'); ?>
