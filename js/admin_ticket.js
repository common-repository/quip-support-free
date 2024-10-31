jQuery(document).ready(function ($)
{
    var $loading = $('.showLoading');

    $('#ticketReplyForm').submit(function (e)
    {
        e.preventDefault();
        tinymce.triggerSave();
        $loading.show();

        var $form = $(this);

        $.ajax({
            type: "POST",
            url: quip_support.ajaxurl,
            data: $form.serialize(),
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $loading.hide();

                if (data.success)
                {
                    fsa_resetForm($form);
                    fsa_showUpdate(data.msg);

                    setTimeout(function ()
                    {
                        window.location.reload(true);
                    }, 1000);
                }
                else
                {
                    fsa_showError(data.msg);
                }
            }
        });

        return false;
    });

    $('#ticketCloseForm, #ticketUpdateStatusForm, #ticketOpenForm').submit(function (e)
    {
        e.preventDefault();
        $loading.show();
        var $form = $(this);

        $.ajax({
            type: "POST",
            url: quip_support.ajaxurl,
            data: $form.serialize(),
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $loading.hide();

                if (data.success)
                {
                    fsa_resetForm($form);
                    fsa_showUpdate(data.msg);

                    setTimeout(function ()
                    {
                        window.location.reload(true);
                    }, 1000);
                }
                else
                {
                    fsa_showError(data.msg);
                }
            }
        });

        return false;
    });

    ////////////////////////////////////

    $("#templatePickerDialog").dialog({
        autoOpen: false,
        height: 300,
        width: 450,
        modal: false,
        buttons: [
            {
                text: "Close",
                click: function ()
                {
                    $(this).dialog("close")
                }
            }
        ]
    });

    $('#templatePickerButton').click(function (e)
    {
        e.preventDefault();
        $("#templatePickerDialog").dialog("open");
        return false;
    });


});