$('.qnt').change(function() {
    qtes = $('select',this).val();
    id = $('input', this).val();
    $.ajax({
        type:"POST",
        url: '/commande/setQuantiteProduit/' + qtes + '/' + id,
        data:  'qtes'
    });
})
