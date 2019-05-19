<?php
  /*
   * Template Name: My Custom Page
   * Description: A Page Template with a darker design.
   */
?>

<?php get_plugin_part_template('template-parts/header-draka'); ?>

<div id="site-content">
  <div id="main-container" class="col-8-d col-12-t col-12-m">
      <h1 class="site-heading">Ranking</h1>

<?php


$table_name = $wpdb->prefix . 'draka_save';
$select = "
SELECT
  *
FROM
  (SELECT
     `user_id`, MAX(`save_date`) AS `save_date`, `user_met`
   FROM
     `wp_draka_save`
   GROUP BY
     `user_id`, `user_met`) AS latest_orders
INNER JOIN
  `wp_draka_save`
ON
  `wp_draka_save`.user_id = latest_orders.user_id AND
  `wp_draka_save`.`save_date` = latest_orders.`save_date` AND
  `wp_draka_save`.`user_met` = latest_orders.`user_met`
";
$results = $wpdb->get_results( $select ); // Query to fetch data from database table and storing in $results
if(!empty($results)) :
  // echo '<pre>';
  // var_dump( $results );
  // echo '</pre>';
  $sortArray = array();

  foreach($results as $result){
      foreach($result as $key=>$value){
          if(!isset($sortArray[$key])){
              $sortArray[$key] = array();
          }
          $sortArray[$key][] = $value;
      }
  }

  $orderby = "user_sum"; //change this to whatever key you want from the array

  array_multisort($sortArray[$orderby],SORT_DESC,$results);

endif; ?>



        <table id="ranking-table">
          <tbody>
            <tr>
              <th class="table-column lp">LP</th>
              <th class="table-column name">Nazwa jednostki</th>
              <th class="table-column score">Liczba punktów</th>
              <th class="table-column badge">Odnzaka</th>
              <th class="table-column met">Metodyka</th>
            </tr>
            <?php
              $i = 1;
              if(!empty($results)) :
                foreach ($results as $result ) {
                  ?>
                  <tr>
                    <td class="table-column lp"><?php echo $i++; ?></td>
                    <td class="table-column name"><?php echo $result->user_nicename; ?></td>
                    <td class="table-column score"><?php echo $result->user_sum; ?></td>
                    <td class="table-column badge"><?php echo $result->user_level; ?></td>
                    <td class="table-column met"><?php echo $result->user_met; ?></td>
                  </tr>
                  <?
                }
              endif;
              ?>

          </tbody>
        </table>

        </div>

        <div class="col-4-d col-0-t col-0-m">
            <img src='<?php echo DRAKA_URL . "img/parts/lion.png"; ?>' alt="Lew prawy" id="lion-right">
        </div>
    </div>

<?php get_plugin_part_template('template-parts/footer-draka'); ?>
