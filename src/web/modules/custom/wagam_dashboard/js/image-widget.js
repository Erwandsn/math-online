/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.wagamImageWidget = {
    attach: function (context, settings) {
      $('#change-default-img').once().on('click', () => {
        toggleUpload();
      });
    }
  };

  const toggleUpload = () => {
    const upload_button = $('#change-default-img');
    upload_button.fadeOut();
    upload_button.parents('.input-group').find('.default-img').fadeOut();

    upload_button.parents('.js-form-type-managed-file').find('.description').addClass('animate__animated animate__backInLeft');
    upload_button.parents('.input-group').find('.form-file').addClass('animate__animated animate__fadeInDown');
    upload_button.parents('.input-group').find('.form-file').toggleClass('d-none');
    upload_button.parents('.js-form-type-managed-file').find('.description').toggleClass('visually-hidden');
  }
})(jQuery, Drupal);
