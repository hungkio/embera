$(function () {
    // Get the form.
    function ajax_form(form) {
        // Get the messages div.
        var formMessages = $('.ajax-response');

        // Set up an event listener for the contact form.
        $(form).submit(function (e) {
            // Stop the browser from submitting the form.
            e.preventDefault();

            // Serialize the form data.
            var formData = $(form).serialize();
            // Submit the form using AJAX.
            if (form.valid()) {
                $.ajax({
                    type: 'POST',
                    url: $(form).attr('action'),
                    data: formData
                }).done(function (response) {
                    // Make sure that the formMessages div has the 'success' class.
                    $(formMessages).removeClass('error');
                    $(formMessages).addClass('success');

                    // Set the message text.
                    toastr.success(response.message, 'Thành công !');

                    // Clear the form.
                    $('#contact-form input,#contact-form textarea').not(':input[name=_token]').val('');
                    $('#subscribe-email-form input').not(':input[name=_token]').val('');
                }).fail(function (data) {
                    // Make sure that the formMessages div has the 'error' class.
                    $(formMessages).removeClass('success');
                    $(formMessages).addClass('error');
                    // Set the message text.
                    if (data.responseText !== '') {
                        var errors = $.parseJSON(data.responseText);
                        $.each(errors, function (index, value) {
                            if (value.email){
                                toastr.error(value.email, 'Lỗi !');
                            }
                        });
                    } else {
                        toastr.error('Đã xảy ra lỗi và không thể gửi tin nhắn của bạn !', 'Lỗi !');
                    }
                });
            }
        });
    }
    var contactForm = $('#contact-form');
    if (contactForm) {
        ajax_form(contactForm)
    }

    var subscribeEmailForm = $('#subscribe-email-form');
    if (subscribeEmailForm) {
        ajax_form(subscribeEmailForm)
    }
});
