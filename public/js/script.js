$(document).ready(function () {
    $('.header').sticky({ topSpacing: 0, zIndex: 20 });

    $('input').change(function () {
        if ($(this).val()) {
            $(this).focus()
        } else {
            $(this).blur()
        }
    })
});
