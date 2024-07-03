

jQuery(function($) {
  var form = $('#fornitori_form');
  form.on('submit_success', function(e, data) {
    if (data.data && data.data.fields) {
      var fields = data.data.fields; // Accedi al valore del campo "name"

      // Invia una richiesta AJAX per creare un post
      $.ajax({
        url: ajax_object.ajax_url,
        method: 'POST',
        data: {
          action: 'create_post_from_form',
          fields: fields // Passa il valore del campo "name"
        },
        success: function(response) {
          if (response.success) {
            console.log('Post created successfully with ID: ' + fields.name);
          } else {
            console.log('Post creation failed: ' + fields.name);
          }
        },
        error: function(error) {
          console.log('AJAX error:', error);
        }
      });
    }
  });
});
