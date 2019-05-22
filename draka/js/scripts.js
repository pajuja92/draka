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
