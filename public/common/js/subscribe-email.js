$(function () {

    jQuery.validator.addMethod("isSpace", function(value, element) {
        return value.trim().length !== 0;
    }, "Trường này là khoảng trắng.");

    jQuery.validator.addMethod("regex", function(value, element, regexp) {
        let re = new RegExp(regexp);
        return this.optional(element) || re.test(value);
    }, "Vui lòng kiểm tra thông tin nhập .");

    jQuery.validator.addMethod("regexEmail", function(value, element) {
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        return regex.test(value);
    });

    $('#subscribe-email-form').validate({
        rules: {
            email: {
                required: true,
                email: true,
                maxlength: 255,
                regexEmail: true
            },
        },
        messages: {
            email: {
                required: "Vui lòng nhập email.",
                unique: "Email đã được đăng ký!",
                email: "Nhập đúng định dạng email.",
                regexEmail: "Nhập đúng định dạng email.",
                maxlength: "Email chỉ được tối đa 255 ký tự!",
            },
        },
    });
});
