jQuery(document).ready(function ($) {
  // Trova il link 'Aggiungi nuovo articolo' e modifica il testo
  var aggiungiLink = $(
    'a.page-title-action[href$="post-new.php?post_type=fornitore"]'
  );
  aggiungiLink.text("Aggiungi nuovo fornitore");

  // Crea il tuo pulsante 'Invia Preventivo'
  var tuoPulsante = $(
    '<a href="#" class="page-title-action">Invia Preventivo</a>'
  );

  // Inserisci il pulsante dopo il link 'Aggiungi nuovo fornitore'
  aggiungiLink.after(tuoPulsante);

  // Aggiungi l'evento click al pulsante
  tuoPulsante.on("click", function (e) {
    e.preventDefault();

    // Conta quanti fornitori sono stati selezionati
    var numFornitori = $("input[name^='post']:checked").length;

    // Creazione della modale
    var modalContent = $("<div></div>");
    modalContent.html(
      '<label for="select-preventivi">Seleziona Preventivo:</label>' +
        '<select id="select-preventivi">' +
        '<option value="0">Nessun preventivo</option>' +
        "</select>" +
        '<ul id="lista-fornitori-selezionati"></ul>'
    );

    // Aggiungi la modale al body
    $("body").append(modalContent);

    // Popola la select con i preventivi
    $.ajax({
      url: my_ajax_object.ajax_url, // URL del tuo endpoint per recuperare i preventivi
      type: "GET",
      data: {
        action: "get_preventivi", // Azione che hai definito nella funzione get_preventivi_callback
      },
      success: function (data) {
        var preventivi = data.data;
        var selectPreventivi = $("#select-preventivi");
        $.each(preventivi, function (key, value) {
          selectPreventivi.append(
            $("<option>", {
              value: key,
              text: value,
            })
          );
        });
      },
    });

    // Popola la lista dei fornitori selezionati
    $("input[name^='post']:checked").each(function () {
      var postID = $(this).val();
      var postTitle = $(
        "tr#post-" + postID + " td.title.column-title strong a"
      ).text();
      var postEmail = $("tr#post-" + postID + " td.email.column-email").text();

      $("#lista-fornitori-selezionati").append(
        "<li>" + postTitle + " (" + postEmail + ")</li>"
      );
    });

    // Apri la modale
    modalContent.dialog({
      title: "Fornitori selezionati " + numFornitori,
      modal: true,
      width: 400,
      height: 500,
      buttons: {
        "Invia Preventivo": function () {
          // Implementa l'invio dei preventivi
          var idPreventivo = $("#select-preventivi").val();
          var nomePreventivo = $("#select-preventivi option:selected").text();
          console.log(
            "Preventivo selezionato: " +
              nomePreventivo +
              " (ID: " +
              idPreventivo +
              ")"
          );
          // Chiudi la modale
          $(this).dialog("close");
        },
        Annulla: function () {
          // Chiudi la modale
          $(this).dialog("close");
        },
      },
      close: function () {
        // Rimuovi la modale dal DOM dopo la chiusura
        $(this).remove();
      },
    });
  });
});
