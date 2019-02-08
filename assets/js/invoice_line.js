$(function ()
{
    function referenceChange()
    {
        var form = $(this).closest('form');
        var description = $("#invoice_line_description");
        var rate = $("#invoice_line_rate");

        var ref = $("#invoice_line_reference");
        var value = ref.val();

        var ajax_url = form.attr('data-ajax');
        ajax_url = ajax_url.replace('0', value);

        if (value) {
            $.ajax({
                url: ajax_url,
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    description.val(data.description);
                    rate.val(data.price / 100);
                },
                error: function () {
                }
            });
        }
    }
    var reference = $("#invoice_line_reference");

    reference.change(referenceChange);
});
