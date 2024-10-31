jQuery(document).ready(function ($)
{
    function assign_ticket()
    {
        //$loading.show();
        var id = $(document).data('ticketID');
        var userId = $('#assignTicketUser').val();

        $.ajax({
            type: "POST",
            url: quip_support.ajaxurl,
            data: {"action": "quip_support_assign_ticket", "id": id, "userId": userId},
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $("#assignTicketDialog").dialog("close");
                //$loading.hide();
                fsa_showUpdate(data.msg);

                setTimeout(function ()
                {
                    window.location.reload(true);
                }, 1000);
            }
        });
    }

    $("#assignTicketDialog").dialog({
        autoOpen: false,
        height: 200,
        width: 350,
        modal: true,
        buttons: [
            {
                text: "Assign",
                click: assign_ticket
            },
            {
                text: "Cancel",
                click: function ()
                {
                    $(this).dialog("close")
                }
            }
        ]
    });

    $('.assign-ticket').click(function (e)
    {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $(document).data('ticketID', id);
        $("#assignTicketDialog").dialog("open");
        return false;
    });

    ///////////////////////////////////////////
    function delete_ticket()
    {
        //$loading.show();
        var id = $(document).data('ticketID');

        $.ajax({
            type: "POST",
            url: quip_support.ajaxurl,
            data: {"action": "quip_support_delete_ticket", "id": id},
            cache: false,
            dataType: "json",
            success: function (data)
            {
                $("#deleteTicketDialog").dialog("close");
                //$loading.hide();
                fsa_showUpdate(data.msg);

                setTimeout(function ()
                {
                    window.location.reload(true);
                }, 1000);
            }
        });
    }

    $("#deleteTicketDialog").dialog({
        autoOpen: false,
        height: 200,
        width: 350,
        modal: true,
        buttons: [
            {
                text: "Yes",
                click: delete_ticket
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

    $('.delete-ticket').click(function (e)
    {
        e.preventDefault();
        var id = $(this).attr('data-id');
        $(document).data('ticketID', id);
        $("#deleteTicketDialog").dialog("open");
        return false;
    });
});