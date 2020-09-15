var pttestimonial = {
    'saveTestimonial' : function (ok_button) {
        var container = ok_button.closest('.newsletter-content');
        var authorP = container.find('.testimonial_author').val();
        var textP = container.find('.testimonial_text').val();

        $.ajax({
            url: 'index.php?route=plaza/testimonial/add',
            type: 'post',
            data: {
                author : authorP,
                text : textP
            },
            beforeSend: function () {
                ok_button.button('loading');
                container.find('.newsletter-notification').removeClass().addClass('newsletter-notification').html('');
            },
            success: function (json) {
                if(json['status'] == true) {
                    container.find('.newsletter-notification').addClass('success').html(json['success']);
                    pttestimonial.closePopup();
                } else {
                    container.find('.newsletter-notification').addClass('error').html(json['error']);
                }
            },
            complete: function () {
                ok_button.button('reset');
            }
        });
    },

    'closePopup': function () {
        setTimeout(function() {$('.testimonial-popup').hide();}, 1000);
    }
};