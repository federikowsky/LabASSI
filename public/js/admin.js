$('#submitTournamentBtn').click(function() {
    $('#tournamentForm').submit();
});

$('.tournView').click(function() {

});

$('.tournAct').click(function() {
    $.ajax({
        type: "POST",
        url: "/admin/tournaments/start",
        data: {
            tournament_id: $(this).attr('data-id'),
            csrf_token: $('input[name="csrf_token"]').val()
        },
        success: function(data) {
            console.log(data);
            location.reload();
        },
        error: function(xhr, status, error) {
            console.log("Errore: " + xhr.responseText);
        }
    });
});

$('.tournDel').click(function() {
    $.ajax({
        type: "POST",
        url: "/admin/tournaments/delete",
        data: {
            tournament_id: $(this).attr('data-id'),
            csrf_token: $('input[name="csrf_token"]').val()
        },
        success: function(data) {
            console.log(data);
            location.reload();
        },
        error: function(xhr, status, error) {
            console.log("Errore: " + xhr.responseText);
        }
    });
});