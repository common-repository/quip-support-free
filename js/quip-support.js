jQuery(document).ready(function ($)
{
    var $loading = $('.showLoading');
    $("#createSupportTicketForm").submit(function (e)
    {
        e.preventDefault();
        $loading.show();

        var $form = $(this);
        var $msg = $('#quip-support-message');
        $msg.hide().removeClass('qs-success-box').removeClass('qs-error-box');

        $.ajax({
            type: "POST",
            url: quip_support.ajaxurl,
            data: $form.serialize(),
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $loading.hide();
                document.body.scrollTop = document.documentElement.scrollTop = 0;

                if (data.success)
                {
                    $form.find('input, textarea').val('');
                    $msg.text(data.msg);
                    $msg.addClass('qs-success-box').show();

                    if (data.redirectURL != '')
                    {
                        setTimeout(function ()
                        {
                            window.location = data.redirectURL;
                        }, 2000);
                    }
                }
                else
                {
                    $msg.text(data.msg);
                    $msg.addClass('qs-error-box').show();
                }
            }
        });

        return false;
    });

    $('#ticketReplyForm, #surveyReplyForm').submit(function (e)
    {
        e.preventDefault();
        $('#success-message').hide();
        $('#error-message').hide();
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
                    $form.find('input:text, input:password, input:file, select, textarea').val('');
                    $('#success-message-text').text(data.msg);
                    $('#success-message').show();

                    if (data.redirectURL == '')
                    {
                        setTimeout(function ()
                        {
                            window.location.reload(true);
                        }, 1000);
                    }
                    else
                    {
                        setTimeout(function ()
                        {
                            window.location = data.redirectURL;
                        }, 1000);
                    }
                }
                else
                {
                    $('#error-message-text').text(data.msg);
                    $('#error-message').show();
                }
            }
        });

        return false;
    });
});