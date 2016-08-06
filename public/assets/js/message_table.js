// Enable table row to link to message view.
$('tr[data-href]').on("click", function() {
    document.location = $(this).data('href');
});
