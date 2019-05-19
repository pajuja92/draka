jQuery( document ).ready( function() {
  console.log('js-ready');
  jQuery('.subcategory-toggle').on('click', function( e ) {

      jQuery('.subcategory-container').hide();
      var id = jQuery( this ).attr( "for" );
      jQuery( "#" + id ).show();

  });

  jQuery('#minimize-button').on('click', function( e ) {

      jQuery('.info-data').toggle();
      // var id = jQuery( this ).attr( "for" );
      // jQuery( "#" + id ).show();

  });
});
