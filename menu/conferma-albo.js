jQuery(document).ready(function ($) {
  // Trova il link 'Aggiungi nuovo articolo' e modifica il testo
  var aggiungiLink = $('a.page-title-action[href$="post-new.php?post_type=fornitore"]');
  aggiungiLink.text("Aggiungi nuovo fornitore");

  // Crea il tuo pulsante 'Iscrivi in albo'
  var tuoPulsante = $('<a href="#" class="page-title-action">Iscrivi in albo</a>');

  // Inserisci il pulsante dopo il link 'Aggiungi nuovo fornitore'
  aggiungiLink.after(tuoPulsante);

  // Creazione della modale (una sola volta)
  var modalContent = $("<div></div>");
  modalContent.html(
    "<h2>Conferma fornitori in albo</h2>" +
    "<p>I fornitori selezionati verranno confermati. Spuntando la casella puoi scegliere se inviare una mail di conferma ai fornitori che sono stati accettati</p>" +
    '<input type="checkbox" id="invia-email-conferma" /> Invia email di conferma ai fornitori' +
    '<ul id="lista-fornitori-selezionati" style="max-height: 200px; overflow: scroll;  background: aliceblue;"></ul>' +
    '<div id="loader" style="display: none;"><img src="path_to_loader.gif" alt="Loading..." /><p>Attendere...</p></div>' +
    '<div id="feedback" style="display: none;"></div>'
  );

  // Aggiungi la modale al body (una sola volta)
  $("body").append(modalContent);

  // Configura la modale (una sola volta)
  modalContent.dialog({
    autoOpen: false,
    modal: true,
    width: 400,
    height: 500,
    buttons: {
      "Conferma in albo": function () {
        var sendEmail = $("#invia-email-conferma").is(":checked");
        var fornitori = [];
      
        // Popola l'array fornitori
        $("input[name^='post']:checked").each(function () {
          var postID = $(this).val();
          var postTitle = $("tr#post-" + postID + " td.title.column-title strong a").text();
          var postEmail = $("tr#post-" + postID + " td.email.column-email").text();

          fornitori.push({
            id: postID,
            name: postTitle,
            email: postEmail,
          });
        });

        var obj = {
          send_email: sendEmail,
          fornitori: fornitori,
        };

        // Mostra il loader
        $('#loader').show();
        $('#feedback').hide(); // Nascondi il feedback precedente (se presente)

        // Disabilita il bottone durante l'invio
        $(this).dialog("widget").find(".ui-dialog-buttonpane button:contains('Conferma in albo')").prop('disabled', true);

        // Chiamata AJAX
        $.ajax({
          url: ajax_object.ajax_url,
          method: "POST",
          data: {
            action: "conferma-in-albo",
            fields: obj,
          },
          success: function (response) {
            if (response.success) {
              $('#feedback').html('<p style="color: green;">Operazione completata con successo!</p>').show();
              console.log("ok");
            } else {
              $('#feedback').html('<p style="color: red;">Operazione fallita. Si prega di riprovare più tardi.</p>').show();
              console.log("failed");
            }
            setTimeout(function() {
              location.reload();
            }, 1000);
          },
          error: function (error) {
            $('#feedback').html('<p style="color: red;">Errore durante l\'operazione. Si prega di riprovare più tardi.</p>').show();
            console.log("AJAX error:", error);
          },
          complete: function () {
            // Nascondi il loader e riabilita il bottone
            $('#loader').hide();
            $(this).dialog("widget").find(".ui-dialog-buttonpane button:contains('Conferma in albo')").prop('disabled', false);
            // Chiudi la modale dopo la conferma
            $(this).dialog("close");
          }
        });
      },
    },
    close: function () {
      // Resetta lo stato dei feedback e del bottone quando la modale viene chiusa
      $('#feedback').hide().empty();
      $(this).dialog("widget").find(".ui-dialog-buttonpane button:contains('Conferma in albo')").prop('disabled', false);
    }
  });

  // Aggiungi l'evento click al pulsante 'Iscrivi in albo'
  tuoPulsante.on("click", function (e) {
    e.preventDefault();

    // Conta quanti fornitori sono stati selezionati
    var numFornitori = $("input[name^='post']:checked").length;

    // Pulisce la lista dei fornitori selezionati
    $("#lista-fornitori-selezionati").empty();

    // Popola la lista dei fornitori selezionati
    $("input[name^='post']:checked").each(function () {
      var postID = $(this).val();
      var postTitle = $("tr#post-" + postID + " td.title.column-title strong a").text();
      var postEmail = $("tr#post-" + postID + " td.email.column-email").text();

      $("#lista-fornitori-selezionati").append("<li>" + postTitle + " (" + postEmail + ")</li>");
    });

    // Aggiorna il titolo della modale con il numero di fornitori selezionati
    modalContent.dialog("option", "title", "Fornitori selezionati " + numFornitori);

    // Apri la modale
    modalContent.dialog("open");
  });
});
