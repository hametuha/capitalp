/**
 * Description
 */

/*global JobBoard: false*/

(function ($) {

  'use strict';

  $(document).on('click','#capitalp-job-submit-button', function(e){
    e.preventDefault();
    var $btn = $(this);
    if ( $(this).hasClass('disabled') ) {
      return;
    }
    $(this).attr('disabled', true).addClass('disabled');
    $.post(JobBoard.endpoint, {
       _wpnonce: JobBoard.nonce
    }).done(function(response){
      alert('求人情報の申し込み先を表示いたします。画面をリロードしますので、"OK"を押してください。');
      window.location.reload();
    }).fail(function(response){
      var msg = '申し込みに失敗しました。またあとでやり直してください。';
      if ( response.responseJSON && response.responseJSON.message ) {
        msg = response.responseJSON.message;
      }
      alert( msg );
    }).always(function(){
      $btn.removeClass('disabled').attr('disabled', null);
    });
  });


})(jQuery);
