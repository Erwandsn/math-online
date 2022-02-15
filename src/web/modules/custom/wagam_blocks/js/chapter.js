/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.wagamChapter = {
    attach: function (context, settings) {
      $('.chapter-toggler').once().on('click', function(){
        const target = $(this).parents('.multi-col-wrapper').find('.chapter-content-wrapper');
        target.addClass('animate__animated');
        if(target.is(':visible')){
          target.addClass('animate__fadeOut');
          target.addClass('animate__animated');
          setTimeout(() => {
            target.removeClass('animate__fadeOut');
            target.removeClass('animate__animated');
            target.addClass('d-none');
          }, 750);
        }else{
          target.addClass('animate__fadeIn');
          target.addClass('animate__animated');
          target.removeClass('d-none');
          setTimeout(() => {
            target.removeClass('animate__fadeIn');
            target.removeClass('animate__animated');
          }, 750);
        }

        $(this).attr('aria-expanded', target.is(':visible'));
      });
    }
  };

})(jQuery, Drupal);
