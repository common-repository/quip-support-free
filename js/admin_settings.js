jQuery(document).ready(function ($)
{
    var $loading = $('.showLoading');

    $('form').submit(function (e)
    {
        e.preventDefault();
        $loading.show();

        if (typeof(tinymce) != "undefined")
        {
            tinymce.triggerSave();
        }

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

    $('.removeAgent').click(function (e)
    {
        e.preventDefault();
        $loading.show();
        var id = $(this).attr('data-id');

        $.ajax({
            type: "POST",
            url: quip_support.ajaxurl,
            data: {wpUserId: id, action: 'quip_support_remove_agent'},
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $loading.hide();
                if (data.success)
                {
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

    $('.removeTemplate').click(function (e)
    {
        e.preventDefault();
        $loading.show();
        var name = $(this).attr('data-name');

        $.ajax({
            type: "POST",
            url: quip_support.ajaxurl,
            data: {name: name, action: 'quip_support_remove_template'},
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $loading.hide();
                if (data.success)
                {
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


    $("#viewTemplateDialog").dialog({
        autoOpen: false,
        height: 400,
        width: 500,
        modal: false,
        buttons: [
            {
                text: "Done",
                click: function ()
                {
                    $(this).dialog("close")
                }
            }
        ]
    });

    $('.viewTemplate').click(function (e)
    {
        e.preventDefault();
        var name = $(this).attr('data-name');
        var content = Base64.decode($(this).attr('data-value'));

        $('#viewTemplateName').text(name);
        $('#viewTemplateContent').val(content);
        $("#viewTemplateDialog").dialog("open");
        return false;
    });


    //for uploading images using WordPress media library
    var custom_uploader;

    function uploadImage(inputID, imgSrc, showImg, showImgID)
    {
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader)
        {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function ()
        {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $(inputID).val(attachment.url);
            if (showImg)
            {
                $(imgSrc).attr('src', attachment.url);
                $(showImgID).show();
            }
        });

        //Open the uploader dialog
        custom_uploader.open();
    }

    // show the image if it exists
    if ($('#companyLogoSrc').attr('src') != "")
        $('#companyLogoImage').show();

    //upload company logo image
    $('#uploadImageButton').click(function (e)
    {
        e.preventDefault();
        uploadImage('#companyLogo', '#companyLogoSrc', true, '#companyLogoImage');
    });

    $('#clearLogo').click(function (e)
    {
        e.preventDefault();
        $('#companyLogo').val("");
        $('#companyLogoImage').hide();
        return false;
    });

    ///

    $('#sendTestEmails').click(function (e)
    {
        e.preventDefault();
        $loading.show();

        $.ajax({
            type: "POST",
            url: quip_support.ajaxurl,
            data: {action: 'quip_support_send_test_emails'},
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $loading.hide();
                if (data.success)
                {
                    fsa_showUpdate(data.msg);
                }
                else
                {
                    fsa_showError(data.msg);
                }
            }
        });

        return false;
    });
});