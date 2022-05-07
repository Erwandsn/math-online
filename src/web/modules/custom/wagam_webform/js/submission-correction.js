/**
 * @file
 * Javascript functionality for the focal point widget.
 */

 (function ($, Drupal) {
    'use strict';

    /**
     * Focal Point indicator.
     */
    Drupal.behaviors.submissionCorrection = {
      attach: function (context) {

        $('#previous-quest-action').once().on('click', function(){
          prevPage(this);
          manageCounter(this, 'prev');
        });

        $('#next-quest-action').once().on('click', function(){
          nextPage(this);
          manageCounter(this, 'next');
        });
      }
    };

    const manageCounter = (element, type) => {
      const current = $(element).parents('.quiz-navigation-header').find('.state .current');
      let count = 0;
      if(type === 'next'){
        count = parseInt(current.html()) + 1;
      }else{
        count = parseInt(current.html()) - 1;
      }
      current.html(count);
    }

    const nextPage = (element) => {
      let questions = $(element).parents('.quiz-submission').find('.question-wrapper');
      let nextQuestion = 0;

      questions.map((key, question) => {
          if($(question).hasClass('current-question')){
            nextQuestion = key+1;
            $(question).removeClass('current-question');
            $(question).addClass('d-none');
          }

          if(nextQuestion == key){
            if(key == questions.length -1){
              $(element).addClass('d-none');
            }
            $(question).addClass('current-question');
            $(question).removeClass('d-none');
          }
      });

      const prevButton = $(element).siblings('.btn-prev');
      if(prevButton.hasClass('d-none')){
        prevButton.removeClass('d-none');
      }
    }

    const prevPage = (element) => {
      let questions = $(element).parents('.quiz-submission').find('.question-wrapper');
      let prevQuestion = 0;

      for(let i = questions.length; i > 0; i--){
        const question = questions[i-1];
        console.log(question);
        if($(question).hasClass('current-question')){
          prevQuestion = i-1;
          $(question).removeClass('current-question');
          $(question).addClass('d-none');
        }

        if(prevQuestion == i){
          if(i == 1){
            $(element).addClass('d-none');
          }
          $(question).addClass('current-question');
          $(question).removeClass('d-none');
        }
      }

      const nextButton = $(element).siblings('.btn-next');
      if(nextButton.hasClass('d-none')){
        nextButton.removeClass('d-none');
      }
    }

  })(jQuery, Drupal);
