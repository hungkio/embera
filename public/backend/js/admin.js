$(document).ready(function() {
    $('input[name="inlineRadioOptions"]').on('change', function() {
        if (this.checked) {
            handleRadioChange(this.value);
        }
    });

    function handleRadioChange(value) {
        if (value === 'option2') {
            $('.external-link').hide();
            $('.internal-link').show();
        } else if (value === 'option3') {
            $('.internal-link').hide();
            $('.external-link').show();
        }
    }
});