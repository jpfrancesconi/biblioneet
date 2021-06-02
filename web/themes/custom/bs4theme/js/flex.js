(function ($, Drupal) {
  Drupal.behaviors.flex = {
    attach: function (context, settings) {
      'use strict';
      $('.flexslider').flexslider({
        animation: "slide"
      });
    }
  };
})(jQuery, Drupal);
