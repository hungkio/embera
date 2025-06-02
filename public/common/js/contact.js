$(function () {

    jQuery.validator.addMethod("isSpace", function(value, element) {
        return value.trim().length !== 0;
    }, "Trường này là khoảng trắng.");

    jQuery.validator.addMethod("regex", function(value, element, regexp) {
        let re = new RegExp(regexp);
        return this.optional(element) || re.test(value);
    }, "Vui lòng kiểm tra thông tin nhập .");

    $('#contact-form').validate({
        rules: {
            first_name: {
                required: true,
                isSpace: true
            },
            last_name: {
                required: true,
                isSpace: true
            },
            email: {
                required: true,
                email: true,
                regex: /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/g
            },
            phone: {
                required: true,
                number: true,
                minlength: 10,
                maxlength: 11,
                isSpace: true
            },
            title: {
                required: true,
                isSpace: true
            },
            message: {
                required: true,
                isSpace: true
            }
        },
        messages: {
            first_name: {
                required: "Vui lòng nhập họ.",
                isSpace: "Họ không được là khoảng trắng."
            },
            last_name: {
                required: "Vui lòng nhâp tên.",
                isSpace: "Tên không được là khoảng trắng."
            },
            email: {
                required: "Vui lòng nhập email.",
                email: "Email không hợp lệ.",
                regex: "Email không hợp lệ."
            },
            phone: {
                required: "Vui lòng nhập số điện thoại.",
                number: "Số điện thoại chỉ bao gồm số.",
                minlength: "Số điện thoại có ít nhất 10 chữ số.",
                maxlength: "Số điện thoại có ít nhất 11 chữ số.",
                isSpace: "Số điện thoại không được là khoảng trắng."
            },
            title: {
                required: "Vui lòng nhập tiêu dề.",
                isSpace: "Tiêu đề không được là khoảng trắng."
            },
            message: {
                required: "Vui lòng nhập tin nhắn.",
                isSpace: "Tin nhắn không được là khoảng trắng."
            }
        },
        onfocusout: false,
        invalidHandler: function(form, validator) {
            let errors = validator.numberOfInvalids();
            if (errors) {
                validator.errorList[0].element.focus();
            }
        }
    });
});
