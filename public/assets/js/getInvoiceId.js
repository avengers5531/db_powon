jQuery(function($) {
    $('#confirmPaymentModal').on('show.bs.modal', function (e) {
        $('#confirmPaymentModal').unbind();
        //var invoiceId = $(e.relatedTarget).data('id');
        var invoiceId = $(e.relatedTarget).attr('data-id');
        console.log(invoiceId);
        var formAction = $('#search').attr('action');
        $('#search').attr('action', formAction + invoiceId);
        document.getElementById('invoice_id').value = invoiceId;


    });
});
