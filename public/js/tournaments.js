$('button.subscribe').on('click', function() {
    let tournament_id = $(this).attr('data-id');
    
    // Trova il form con lo stesso data-id del pulsante cliccato
    let form = $('form[data-id="' + tournament_id + '"]');
    
    // Invia il form
    form.submit();
});


$('button.unsubscribe').on('click', function() {
    let tournament_id = $(this).attr('data-id');
    
    // Trova il form con lo stesso data-id del pulsante cliccato
    let form = $('form[data-id="' + tournament_id + '"]');
    
    // Invia il form
    form.submit();
});
