jQuery(document).ready(function ($) {
  function getFornitoriByCategories(categories) {
    $.ajax({
      url: fornitori_ajax_object.ajax_url,
      type: "POST",
      data: {
        action: "get_fornitori_by_categories",
        categories: categories,
        security: fornitori_ajax_object.ajax_nonce,
      },
      success: function (response) {
        var fornitoriList = $("#fornitori-list");
        fornitoriList.empty();
        if (response.success && response.data.length > 0) {
          $.each(response.data, function (index, post) {
            fornitoriList.append(
              '<div class="fornitore-item">' +
                '<div> <span class="fornitore-title">' +
                post.title +
                '</span> -  <span class="fornitore-email">' +
                post.email +
                " </span></div>" +
                '<span class="remove-fornitore">&times;</span>' +
                "</div>"
            );
          });
        } else {
          fornitoriList.append("<p>Nessun fornitore trovato.</p>");
        }

        // Gestisci il click per rimuovere l'elemento
        $(".remove-fornitore").on("click", function () {
          $(this).parent().remove();
        });
      },
    });
  }

  $("#category-checkboxes input[type='checkbox']").on("change", function () {
    var selectedCategories = [];
    $("#category-checkboxes input[type='checkbox']:checked").each(function () {
      selectedCategories.push($(this).val());
    });

    if (selectedCategories.length > 0) {
      getFornitoriByCategories(selectedCategories);
    } else {
      $("#fornitori-list").empty(); // Svuota la lista se non ci sono checkbox selezionate
    }
  });
  function inviaPreventivo() {
    // Funzione per gestire l'apertura della modale senza AJAX
    $("#open-modal-button").click(function () {
      // Prendi il titolo del preventivo selezionato
      var preventivoTitle = $("#preventivo-select option:selected").text();

      // Prendi la lista dei fornitori dal div #fornitori-list
      var fornitori = [];
      $("#fornitori-list .fornitore-item").each(function () {
        var title = $(this).find(".fornitore-title").text();
        var email = $(this).find(".fornitore-email").text();
        fornitori.push({ name: title, email: email });
      });

      // Aggiorna il titolo del preventivo nella modale
      $("#selected-preventivo").text(preventivoTitle);

      // Mostra i fornitori nella modale
      var modalFornitoriList = $("#modal-fornitori-list");
      modalFornitoriList.empty();

      if (fornitori.length > 0) {
        $.each(fornitori, function (index, forn) {
          modalFornitoriList.append(
            "<p>" + forn.name + " - " + forn.email + "</p>"
          );
        });
      } else {
        modalFornitoriList.append("<p>Nessun fornitore trovato.</p>");
      }

      // Apri la modale
      $("#dialog").dialog({
        modal: true,
        width: 600,
        buttons: {
          Chiudi: function () {
            $(this).dialog("close");
          },
        },
      });
    });

    // Gestione click su "Invia richiesta preventivo"
    $(document).on("click", "#invia-richiesta-button", function () {
      // Prendi l'id del preventivo selezionato
      var preventivoId = $("#preventivo-select").val();

      // Prendi la lista dei fornitori dal div #fornitori-list
      var fornitori = [];
      $("#fornitori-list .fornitore-item").each(function () {
        var title = $(this).find(".fornitore-title").text();
        var email = $(this).find(".fornitore-email").text();
        fornitori.push({ name: title, email: email });
      });

      // Prepara i dati da inviare via AJAX
      var data = {
        action: "invia_email_fornitori",
        security: fornitori_ajax_object.ajax_nonce,
        preventivo_id: preventivoId,
        fornitori: fornitori,
      };

      // Esegui la chiamata AJAX
      $.ajax({
        url: fornitori_ajax_object.ajax_url,
        type: "POST",
        data: data,
        success: function (response) {
          if (response.success) {
            alert("Richiesta del preventivo inviata con successo!");
            $("#dialog").dialog("close");
          } else {
            alert("Si è verificato un errore durante l'invio della richiesta.");
          }
        },
        error: function (xhr, status, error) {
          console.error(xhr.responseText);
          alert("Si è verificato un errore durante l'invio della richiesta.");
        },
      });
    });
  }

  // Chiama la funzione per inizializzare il comportamento
  getFornitoriByCategories();
  inviaPreventivo();
});


jQuery(document).ready(function ($) {
 
});
