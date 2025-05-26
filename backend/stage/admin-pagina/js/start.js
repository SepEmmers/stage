let personeel_id_van_login; // Declareer een variabele om de personeel_id op te slaan

function getPersoneelFK() {
  console.log("getPersoneelFK functie wordt uitgevoerd.");
  return $.ajax({
    url: "API.php",
    type: "POST",
    data: {
      get_session_personeel_id: true, // Verander de naam van de actie
    },
    dataType: "json", // Verwacht een JSON response
  })
    .done(function (data) {
      console.log("Personeel ID data ontvangen:", data);
      if (data && data.personeel_id !== null) {
        // Controleer op 'personeel_id'
        personeel_id_van_login = data.personeel_id; // Gebruik 'personeel_id'
        ajaxcall(); // Roep ajaxcall aan nadat de personeel_id is opgehaald
      } else {
        console.error(
          "Kon personeel ID niet ophalen:",
          data ? data.error : "Onbekende fout"
        );
        // Hier kun je eventueel een fallback implementeren of de gebruiker informeren
      }
    })
    .fail(function (xhr, status, error) {
      console.error("Fout bij het ophalen van personeel ID:", status, error);
      // Hier kun je eventueel een fallback implementeren of de gebruiker informeren
    });
}

function ajaxcall() {
  console.log("ajaxcall functie (start info) wordt uitgevoerd.");
  const startButton = document.getElementById("startButton");
  const stopButton = document.getElementById("stopButton");

  if (personeel_id_van_login === undefined) {
    console.warn(
      "Personeel ID is nog niet geladen. Wacht of controleer de getPersoneelFK functie."
    );
    return; // Stop de functie als de personeel_id nog niet beschikbaar is
  }

  $.ajax({
    url: "API.php",
    type: "POST",
    data: {
      get_start_info: true,
      personeel_fk: personeel_id_van_login, // Gebruik nu de variabele 'personeel_id_van_login'
    },
    success: function (data) {
      console.log("Start info data ontvangen:", data);
      let startInfo = JSON.parse(data);

      if (startInfo) {
  
        const naamDisplay = document.getElementById("naamDisplay");


        

        if (naamDisplay && startInfo.personeelsnaam) {
          naamDisplay.textContent = "Hey " + startInfo.personeelsnaam;
          console.log("Naam weergegeven:", "Hey " + startInfo.personeelsnaam);
        } else {
          console.log("naamDisplay element niet gevonden of naam ontbreekt.");
        }
        console.log("Waarde van startInfo:", startInfo);
        // Stel de initiële staat van de knoppen in
        if (startButton && stopButton && timeDisplay) {
          if (startInfo.isWerkuurGestart) {
            startButton.style.display = "none";
            stopButton.style.display = "block";
          } else {
            startButton.style.display = "block";
            stopButton.style.display = "none";
          }
        }
      } else {
        console.log("Geen startinformatie gevonden.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Fout bij het ophalen van startinformatie:", status, error);
    },
  });
}

function startWerkuur() {
  console.log("startWerkuur functie wordt uitgevoerd.");
  const school_fk = 101; // Correcte school_fk
  const job_fk = 1; // Tijdelijk ingesteld op 1 voor testdoeleinden
  const startButton = document.getElementById("startButton");
  const stopButton = document.getElementById("stopButton");
  const timeDisplay = document.getElementById("timeDisplay");

  if (personeel_id_van_login === undefined) {
    console.warn(
      "Personeel ID is nog niet geladen. Start werkuur actie geannuleerd."
    );
    return;
  }

  $.ajax({
    url: "API.php",
    type: "POST",
    data: {
      start_werkuur: true,
      personeel_fk: personeel_id_van_login, // Gebruik nu de variabele 'personeel_id_van_login'
      school_fk: school_fk,
      job_fk: job_fk,
    },
    success: function (data) {
      console.log("Start werkuur response ontvangen:", data);
      let response = JSON.parse(data);
      if (response.status === "success") {
        console.log("Werkuur gestart!");
        if (startButton && stopButton && timeDisplay) {
          startButton.style.display = "none";
          stopButton.style.display = "block";
        }
        ajaxcall(); // Roep ajaxcall opnieuw aan om de knoppen te updaten
        
      } else if (response.status === "error") {
        alert(response.message);
        console.error("Fout bij starten werkuur:", response.message);
      } else {
        alert("Er is een onbekende fout opgetreden.");
        console.error("Fout bij starten werkuur:", response);
      }
    },
    error: function (xhr, status, error) {
      console.error("Fout bij het starten van het werkuur:", status, error);
    },
  });
}

function stopWerkuur() {
  console.log("stopWerkuur functie wordt uitgevoerd.");
  const startButton = document.getElementById("startButton");
  const stopButton = document.getElementById("stopButton");
  const timeDisplay = document.getElementById("timeDisplay");

  if (personeel_id_van_login === undefined) {
    console.warn(
      "Personeel ID is nog niet geladen. Stop werkuur actie geannuleerd."
    );
    return;
  }

  if (confirm("Weet je zeker dat je het werkuur wilt stoppen?")) {
    $.ajax({
      url: "API.php",
      type: "POST",
      data: {
        stop_werkuur: true,
        personeel_fk: personeel_id_van_login, // Gebruik nu de variabele 'personeel_id_van_login'
      },
      success: function (data) {
        console.log("Stop werkuur response ontvangen:", data);
        let response = JSON.parse(data);
        if (response.success) {
          console.log("Werkuur gestopt!");
          if (startButton && stopButton && timeDisplay) {
            startButton.style.display = "block";
            stopButton.style.display = "none";
          }
          ajaxcall(); // Roep ajaxcall opnieuw aan om de knoppen te updaten
        } else {
          alert("Er is een fout opgetreden bij het stoppen van het werkuur.");
          console.error("Fout bij stoppen werkuur:", response.error);
        }
      },
      error: function (xhr, status, error) {
        console.error("Fout bij het stoppen van het werkuur:", status, error);
      },
    });
  } else {
    console.log("Stoppen van werkuur geannuleerd.");
  }
}

function showWorkHistory() {
  console.log("showWorkHistory functie wordt uitgevoerd.");
  const currentDate = new Date();
  const currentYear = currentDate.getFullYear();
  const currentMonth = currentDate.getMonth() + 1;
console.log(personeel_id_van_login);
  if (personeel_id_van_login === undefined) {
    console.warn(
      "Personeel ID is nog niet geladen. Toon geschiedenis actie geannuleerd."
    );
    return;
  }

  $.ajax({
    url: "API.php",
    type: "POST",
    data: {
      get_monthly_history: true,
      personeel_fk: personeel_id_van_login, // Gebruik nu de variabele 'personeel_id_van_login'
      year: currentYear,
      month: currentMonth,
    },
    success: function (data) {
      console.log("Geschiedenis data ontvangen:", data);
      const historyData = JSON.parse(data);
      console.log(historyData);
      displayWorkHistory(historyData);
    },
    error: function (xhr, status, error) {
      console.error("Fout bij het ophalen van de geschiedenis:", status, error);
    },
  });
}

function displayWorkHistory(historyData) {
  let historyHTML = "<h1>Werkgeschiedenis voor deze maand:</h1>";
  console.log(historyData + "history")
  console.log(historyData.length + "lengte")
  if (historyData && historyData.length > 0) {
    historyHTML += "<table>";
    historyHTML +=
      "<thead><tr><th>Dag</th><th>Datum</th><th>Gewerkte uren</th></tr></thead>";
    historyHTML += "<tbody>";
    const daysOfWeek = [
      "Zondag",
      "Maandag",
      "Dinsdag",
      "Woensdag",
      "Donderdag",
      "Vrijdag",
      "Zaterdag",
    ];
    historyData.forEach((record) => {
      const starttijdParts = record.starttijd.split(" ");
      const startDate = new Date(starttijdParts[0] + "T" + starttijdParts[2]);

      const eindtijdParts = record.eindtijd ? record.eindtijd.split(" ") : null;
      const endDate = eindtijdParts
        ? new Date(eindtijdParts[0] + "T" + eindtijdParts[2])
        : null;

      const dayOfWeek = daysOfWeek[startDate.getDay()];
      const dayOfMonth = startDate.getDate().toString().padStart(2, "0");
      const monthOfYear = (startDate.getMonth() + 1)
        .toString()
        .padStart(2, "0");
      const formattedDate = `${dayOfMonth}/${monthOfYear}`;

      const startTime = `${startDate
        .getHours()
        .toString()
        .padStart(2, "0")}:${startDate
        .getMinutes()
        .toString()
        .padStart(2, "0")}`;
      const endTime = endDate
        ? `${endDate.getHours().toString().padStart(2, "0")}:${endDate
            .getMinutes()
            .toString()
            .padStart(2, "0")}`
        : "Nog niet gestopt";

      historyHTML += `<tr>`;
      historyHTML += `<td>${dayOfWeek}</td>`;
      historyHTML += `<td>${formattedDate}</td>`;
      historyHTML += `<td>Gewerkt van ${startTime} tot ${endTime}</td>`;
      historyHTML += `</tr>`;
    });

    historyHTML += "</tbody>";
    historyHTML += "</table>";
  } else {
    historyHTML += "<p>Geen werkuren gevonden voor deze maand.</p>";
  }

  let historyDisplay = document.getElementById("historyDisplay");
  if (historyDisplay) {
    historyDisplay.innerHTML = historyHTML;
  } else {
    historyDisplay = document.createElement("div");
    historyDisplay.id = "historyDisplay";
    historyDisplay.innerHTML = historyHTML;
    document.body.appendChild(historyDisplay);
  }
}

// This approach tries to push a new state to the history when the user navigates back.
// It's not foolproof and can be frustrating for users.
window.history.pushState(null, null, window.location.pathname);
window.addEventListener('popstate', function(event) {
  window.history.pushState(null, null, window.location.pathname);
});

document.addEventListener("DOMContentLoaded", function () {
  getPersoneelFK(); // Haal de personeel_id op bij het laden van de pagina

  const startButton = document.getElementById("startButton");
  const stopButton = document.getElementById("stopButton");
  const historyButton = document.getElementById("historyButton");

  if (startButton) {
    startButton.addEventListener("click", startWerkuur);
  } else {
    console.error("Start knop niet gevonden!");
  }

  if (stopButton) {
    stopButton.addEventListener("click", stopWerkuur);
  } else {
    console.error("Stop knop niet gevonden!");
  }

  if (historyButton) {
    historyButton.addEventListener("click", showWorkHistory);
  } else {
    console.error("Geschiedenis knop niet gevonden!");
  }

  // Stel de initiële weergave van de knoppen in
  if (startButton && stopButton) {
    stopButton.style.display = "none"; // Startknop is standaard zichtbaar
  }
});

function herlaad() {
  location.reload();
}
