$(document).on('change', ':file', function () {
    let input = $(this);

    $('.upload-text').val(input.get(0).files[0].name);
});
