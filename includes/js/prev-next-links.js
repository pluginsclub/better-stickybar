jQuery(function($) {
          var prevPopupContent =
            '<div class="sticky-bar-popup-image"><img src="<?= get_the_post_thumbnail_url( $prev_post, 'full' ) ?>" alt="Previous Post Image"></div>' +
            '<div class="sticky-bar-popup-content">' +
            '<p><?= $prev_title ?></p>' +
            '<span class="sticky-bar-popup-date"><?= $prev_date ?></span>' +
            '</div>';

          var nextPopupContent =
            '<div class="sticky-bar-popup-image"><img src="<?= get_the_post_thumbnail_url( $next_post, 'full' ) ?>" alt="Next Post Image"></div>' +
            '<div class="sticky-bar-popup-content">' +
            '<p><?= $next_title ?></p>' +
            '<span class="sticky-bar-popup-date"><?= $next_date ?></span>' +
            '</div>';

          $('.sticky-bar-navigation a').eq(0).hover(function() {
            $(this).append('<div class="sticky-bar-popup">' + prevPopupContent + '</div>');
          }, function() {
            $(this).find('.sticky-bar-popup').remove();
          });

          $('.sticky-bar-navigation a').eq(1).hover(function() {
            $(this).append('<div class="sticky-bar-popup">' + nextPopupContent + '</div>');
          }, function() {
            $(this).find('.sticky-bar-popup').remove();
          });
        });