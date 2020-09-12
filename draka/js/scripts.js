jQuery( document ).ready( function() {
  console.log('js-ready');
  jQuery('.subcategory-toggle').on('click', function( e ) {
    var id = jQuery( this ).attr( "for" );

    if( id != "all" ) {
      jQuery('.subcategory-toggle').removeClass('active');
      jQuery( this ).addClass('active')
      jQuery('.subcategory-container').slideUp("fast", function() {
        jQuery( "#" + id ).slideDown("fast");
      });
    } else {
      jQuery('.subcategory-toggle').removeClass('active');

      jQuery( this ).addClass('active')
      jQuery('.subcategory-container').slideDown("fast");
    }
  });

  jQuery('#minimize-button').on('click', function( e ) {
      jQuery('.info-data').toggle();
  });

  jQuery("label").click( function( e ) {
    e.preventDefault();
    var input = jQuery(this).find('input');
    var state = input.is(':checked');

    input.prop("checked", !state );
  });

  function hideMessage() {
    jQuery( '#submit-info-box #info_box' ).slideUp('slow');
  }

  jQuery('#send').on('click', function(e) {
    jQuery('#submit-info-box #info_box').slideDown('slow');
    setTimeout( hideMessage, 10000 );
  });

  jQuery('.choose-met').click(function() {
  	jQuery('.choose-met').removeClass('active');
  	jQuery( this ).addClass('active');
  })

  jQuery( document ).tooltip({
  classes: {
    "ui-tooltip": "highlight",
  },

});
});

function sortTable( n , type ) {

  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("ranking-table");
  switching = true;
  dir = "asc";
  while (switching) {
    switching = false;
    rows = table.rows;
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];


      if ( type == 'int') {
        if (dir == "asc") {
          if ( parseInt( x.innerHTML.toLowerCase() ) > parseInt( y.innerHTML.toLowerCase() )) {
            shouldSwitch = true;
            break;
          }
        } else if (dir == "desc") {
          if ( parseInt( x.innerHTML.toLowerCase() ) < parseInt( y.innerHTML.toLowerCase() ) ) {
            shouldSwitch = true;
            break;
          }
        }
      } else {
        if (dir == "asc") {
          if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
            shouldSwitch = true;
            break;
          }
        } else if (dir == "desc") {
          if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
            shouldSwitch = true;
            break;
          }
        }
      }

    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount ++;
    } else {
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
