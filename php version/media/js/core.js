(function(jQuery) {

  jQuery(document).ready(function() {

    jQuery('html').on('click touch', function() {
      jQuery('#search-overlay, #mobile-search-modal').hide();
      jQuery('#post-status-form textarea').removeClass('active');
      jQuery('.smiles-container').velocity("fadeOut", { duration: 150 });
    });

    jQuery('#main-nav ul.expended > li').on('mouseover', function() {
      if( !jQuery(this).hasClass('hover') )
      {
        jQuery(this).parent().children('li').removeClass('hover');
        jQuery(this).addClass('hover');
      }
    });

    jQuery('#mobile-search').on('click touch', function(e) {
      e.stopPropagation();
      jQuery('#search-overlay, #mobile-search-modal').show();
    });

    jQuery('#form-holder').on('click touch', function(e) {
      e.stopPropagation();
    });

    jQuery('#content.sign-up-page .list input').on('change', function() {
      jQuery('#content.sign-up-page .list div.about > div').hide();
      jQuery('#content.sign-up-page .list div.about > .' + jQuery(this).val()).show();
    });

    jQuery('#post-status-form textarea').on('focus', function(e) {
      jQuery(this).addClass('active');
    });

    jQuery('#post-status-form').on('click touch', function(e) {
      e.stopPropagation();
    });

    jQuery('button.smiles-opener').on('click touch', function() {
      var smilesContainer = jQuery(this).attr('data-smiles-container');

      if( jQuery(smilesContainer).is(':visible') )
      {
        jQuery(smilesContainer).velocity("fadeOut", { duration: 150 });
      } else
      {
        jQuery(smilesContainer).velocity("fadeIn", { duration: 250 });
      }
    });

  });

})(jQuery);