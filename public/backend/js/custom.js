/* ------------------------------------------------------------------------------
 *
 *  # Custom JS code
 *
 *  Place here all your custom js. Make sure it's loaded after app.js
 *
 * ---------------------------------------------------------------------------- */

function Admin() {}

Admin.ready = function (callback) {
    return jQuery(document).ready(function () {
        return callback(jQuery)
    })
}


Admin.blockUI = function (element) {
    $(element).block({
        message: '<i class="icon-spinner2 spinner"></i>',
        overlayCSS: {
            backgroundColor: '#fff',
            opacity: 0.8,
            cursor: 'wait',
            'box-shadow': '0 0 0 1px #ddd'
        },
        css: {
            border: 0,
            padding: 0,
            backgroundColor: 'none'
        }
    });
}

Admin.unBlock = function (element) {
    $(element).unblock();
}

Admin.adminUrl = function (path) {
    return Config.adminPrefix + '/' + path;
}


//Init

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': Config.csrf,
    },
});

Admin.ready(function () {
    // $('form').submit(function (e) {
    //     if ($(this).is('[data-block]')) {
    //         var card = $(this).find('.tab-content');
    //         if (card.length) {
    //             Admin.blockUI(card);
    //         } else {
    //             Admin.blockUI($(this).find('.card'))
    //         }
    //     }
    // })

    $('.form-input-styled').uniform();

    $('.select-file').click(function () {

        let input = $(this).data('input');

        $(input).click();
    });

    $('input[type="file"]').on('change', function () {
        let preview = $(this).data('preview');
        if (preview) {
            readURL(this, preview)
        }
    });

    $('.form-control-select2').select2({
        width: '100%',
        allowClear: true
    });

    // Initialize
    $('.sidebar-sticky .sidebar').stick_in_parent({
        offset_top: 20,
        parent: '.content'
    });

    // Detach on mobiles
    $('.sidebar-mobile-component-toggle').on('click', function() {
        $('.sidebar-sticky .sidebar').trigger("sticky_kit:detach");
    });

    // Sticky Action

    $(window).scroll(function() {
        let lastCard = $('.left-content .card:last');
        if (lastCard.length == 0) {
            return;
        }
        if($(window).scrollTop() >= lastCard.offset().top + lastCard.outerHeight() - window.innerHeight) {
            $('#action-form').removeClass('action').removeClass('justify-content-center').addClass('justify-content-end');
        } else {
            $('#action-form').addClass('action').addClass('justify-content-center').remove('justify-content-end');
        }
    });


    // SubmitType

    $('.submit-type').click(function () {
        let submitType = $(this).data('redirect');
        $(this).closest('form').append('<input type="hidden" value="'  + submitType + '" name="redirect_url">');
        $(this).closest('form').submit();
    })


    $('.icon-circle-down2').click(function () {
        $(this).toggleClass('rotate-90-inverse');
    })
})

$(document).on('click', '.js-delete', function () {
    var deleteUrl = $(this).data('url');

    confirmAction(Lang.confirm_delete, function (result) {
        if (result) {
            $.ajax({
                type: 'POST',
                url: deleteUrl,
                data: {
                    _method: "DELETE"
                },
                success: function (res) {
                    if (res.status == 'error') {
                        showMessage('error', res.message);
                    } else {
                        showMessage('success', res.message);
                    }
                    Object.keys(window.LaravelDataTables).forEach(function (table) {
                        window.LaravelDataTables[table].ajax.reload()
                    })
                }
            })
        }
    })
})

function readURL(input, preview) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $(preview).attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]); // convert to base64 string
    }
}

var stack_custom_bottom = {"dir1": "right", "dir2": "up", "spacing1": 1};
var stack_custom_top = {"dir1": "right", "dir2": "down", "push": "top", "spacing1": 1};

// Custom bottom position
function showMessage(type, message) {
    var opts = {
        title: Lang.system,
        text: message,
        addclass: "bg-primary",
        stack: stack_custom_top
    };
    switch (type) {
        case 'error':
            opts.title = Lang.oh_no;
            opts.addclass = "bg-white notifi-danger";
            opts.type = "error";
            break;

        case 'info':
            opts.title = "Breaking News";
            opts.addclass = "bg-white notifi-primary";
            opts.type = "info";
            break;

        case 'success':
            opts.title = Lang.success;
            opts.addclass = "bg-white notifi-success";
            opts.type = "success";
            break;
    }
    new PNotify(opts);
}

function confirmAction(text, callback) {
    bootbox.confirm({
        title: Lang.confirm,
        message: text,
        buttons: {
            confirm: {
                label: '<i class="fal fa-check mr-2"></i> ' + Lang.yes,
                className: 'btn btn-primary'
            },
            cancel: {
                label: '<i class="fal fa-times mr-2"></i>' + Lang.no,
                className: 'btn btn-danger'
            }
        },
        callback: callback
    });
}
