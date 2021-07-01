function distributeItems() {
    let radius = 75;

    let items = $('.custom-church-health-item'), container = $('#custom-church-health-items-container'),
    	item_count = items.length;
        width = container.width(),
        height = container.height(),
        angle = 0,
        step = (2*Math.PI) / items.length;
        y_offset = -35;

        if ( item_count >= 5 && item_count < 7 ) {
        	radius = 90;
        }

        if ( item_count >= 7 & item_count < 11 ) {
        	radius = 100;
        }

        if ( item_count >= 11 ) {
        	radius = 110;
        }

        if ( item_count == 3 ) {
        	angle = 22.5;
        }
    items.each(function() {
        let X = Math.round(width/2 + radius * Math.cos(angle) - $(this).width()/2);
        let y = Math.round(height/2 + radius * Math.sin(angle) - $(this).height()/2) + y_offset;
        
        if ( item_count == 1 ) {
        	X = 112.5;
        	y = 68;
        }
        $(this).css({
            left: X + 'px',
            top: y + 'px'
        });
        angle += step;
    });
}

"use strict"
jQuery(document).ready(function($) {
  distributeItems();

  let group_id       = window.detailsSettings.post_id;
  let post_type      = window.detailsSettings.post_type;
  let post           = window.detailsSettings.post_fields;
  let field_settings = window.detailsSettings.post_settings.fields;

  $('.summary-icons').on('click', function () {
    let fieldId = $(this).attr('id');
    if (fieldId == 'church_commitment') {
      $( '#custom-church-health-items-container' ).toggleClass( 'committed' );
    } else {
      $( '#' + fieldId ).toggleClass('half-opacity');
      $( '#icon_' + fieldId ).toggleClass('half-opacity');
    }

    $.ajax( {
      type: 'POST',
      contentType: 'application/json; charset=utf-8',
      dataType: "json",
      url: window.wpApiShare.root + 'custom_church_health_tile/v1/update_practice/' + group_id + '/' + fieldId,
      beforeSend: function(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', window.wpApiShare.nonce );
          },
      } )
  })
});