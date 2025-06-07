$(document).ready(function(){
    $('#planningEntitiesTable').DataTable(); //Data Table


    //dlete 
    $(document).on("click", ".delete", function () {
        $('input[name=_token]').val($(this).data('record-id'));
        $('#delete_record').attr('action', main_url + 'delete-planning-entity');
    });
  
});