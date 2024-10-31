jQuery(document).ready(function ($)
{
    var $loading = $('.showLoading');

    $('#quip-support-article-form, #quip-support-topic-form').submit(function (e)
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
                        window.location = data.redirectURL;
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

    $('#saveDraftButton').click(function (e)
    {
        e.preventDefault();
        $('#articleStatus').val("draft");
        $('#quip-support-article-form').submit();
        return false;
    });


    ///////////////////////////////////////////
    function delete_article()
    {
        //$loading.show();
        var id = $(document).data('articleId');

        $.ajax({
            type: "POST",
            url: quip_support.ajaxurl,
            data: {"action": "quip_support_delete_article", "id": id},
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $("#deleteArticleDialog").dialog("close");
                //$loading.hide();
                fsa_showUpdate(data.msg);

                setTimeout(function ()
                {
                    window.location.reload(true);
                }, 1000);
            }
        });
    }

    $("#deleteArticleDialog").dialog({
        autoOpen: false,
        height: 200,
        width: 350,
        modal: true,
        buttons: [
            {
                text: "Yes",
                click: delete_article
            },
            {
                text: "No",
                click: function ()
                {
                    $(this).dialog("close")
                }
            }
        ]
    });

    $('.delete-article').click(function (e)
    {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $(document).data('articleId', id);
        $("#deleteArticleDialog").dialog("open");
        return false;
    });

    ///////////////////////////////////////////
    function delete_topic()
    {
        //$loading.show();
        var id = $(document).data('topicId');

        $.ajax({
            type: "POST",
            url: quip_support.ajaxurl,
            data: {"action": "quip_support_delete_topic", "id": id},
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $("#deleteTopicDialog").dialog("close");
                //$loading.hide();
                fsa_showUpdate(data.msg);

                setTimeout(function ()
                {
                    window.location.reload(true);
                }, 1000);
            }
        });
    }

    $("#deleteTopicDialog").dialog({
        autoOpen: false,
        height: 200,
        width: 350,
        modal: true,
        buttons: [
            {
                text: "Yes",
                click: delete_topic
            },
            {
                text: "No",
                click: function ()
                {
                    $(this).dialog("close")
                }
            }
        ]
    });

    $('.delete-topic').click(function (e)
    {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $(document).data('topicId', id);
        $("#deleteTopicDialog").dialog("open");
        return false;
    });
});