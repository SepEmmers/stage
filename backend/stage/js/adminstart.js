let allHistoryData = []; // Globale variabele om alle opgehaalde data op te slaan

function ajaxcall() {
  console.log("ajaxcall functie (start info) wordt uitgevoerd.");
  const startButton = document.getElementById("startButton");
  const stopButton = document.getElementById("stopButton");
  $.ajax({
    url: "APA.php",
    type: "POST",
    data: {
      get_start_info: true,
    },
  });
}

function showWorkHistory() {
  console.log("showWorkHistory functie wordt uitgevoerd.");
  const currentDate = new Date();
  const currentYear = currentDate.getFullYear();
  const currentMonth = currentDate.getMonth() + 1;

  $.ajax({
    url: "APA.php",
    type: "POST",
    data: {
      get_monthly_history_admin: true,
      year: currentYear,
      month: currentMonth,
    },
    success: function (data) {
      console.log("Geschiedenis data ontvangen:", data);
      try {
        const response = JSON.parse(data);
        console.log(response);
        if (response.success) {
          console.log("Geschiedenis data ontvangen:", response.data);
          allHistoryData = response.data; // Sla alle data op
          populateNameFilter(allHistoryData); // Vul de filter met namen
          displayWorkHistory(allHistoryData); // Toon de initiële data
        } else {
          console.error("Fout bij het ophalen van de geschiedenis:", response.error);
          // Hier kun je de foutmelding aan de gebruiker tonen op de pagina
        }
      } catch (e) {
        console.error("Fout bij het verwerken van de response:", e);
      }
    },
    error: function (xhr, status, error) {
      console.error("Fout bij het ophalen van de geschiedenis:", status, error);
    },
  });
}

function populateNameFilter(data) {
  const filterNaam = document.getElementById("filterNaam");
  const namen = [...new Set(data.map(record => record.VolledigeNaam))]; // Haal unieke namen op
  namen.forEach(naam => {
    const option = document.createElement("option");
    option.value = naam;
    option.textContent = naam;
    filterNaam.appendChild(option);
  });
}

function filterWorkHistory() {
  const selectedNaam = document.getElementById("filterNaam").value;
  const selectedDatum = document.getElementById("filterDatum").value;

  const filteredByName = selectedNaam
    ? allHistoryData.filter(record => record.VolledigeNaam === selectedNaam)
    : allHistoryData;

  const filteredByDate = selectedDatum
    ? filteredByName.filter(record => record.starttijd.startsWith(selectedDatum))
    : filteredByName;

  sortWorkHistory(filteredByDate); // Sorteer de gefilterde data
}

function sortWorkHistory(dataToSort) {
  const sortByValue = document.getElementById("sortBy").value;
  let sortedData = [...dataToSort]; // Maak een kopie om de originele data niet te wijzigen

  sortedData.sort((a, b) => {
    switch (sortByValue) {
      case "naam_asc":
        return a.VolledigeNaam.localeCompare(b.VolledigeNaam);
      case "naam_desc":
        return b.VolledigeNaam.localeCompare(a.VolledigeNaam);
      case "datum_asc":
        const dateA_asc = new Date(a.starttijd.split(" ")[0]);
        const dateB_asc = new Date(b.starttijd.split(" ")[0]);
        return dateA_asc - dateB_asc;
      case "datum_desc":
        const dateA_desc = new Date(a.starttijd.split(" ")[0]);
        const dateB_desc = new Date(b.starttijd.split(" ")[0]);
        return dateB_desc - dateA_desc;
      case "uren_asc":
        const urenA_asc = parseFloat(a.totaal_tijd.replace(':', '.'));
        const urenB_asc = parseFloat(b.totaal_tijd.replace(':', '.'));
        return urenA_asc - urenB_asc;
      case "uren_desc":
        const urenA_desc = parseFloat(a.totaal_tijd.replace(':', '.'));
        const urenB_desc = parseFloat(b.totaal_tijd.replace(':', '.'));
        return urenB_desc - urenA_desc;
      default:
        return 0;
    }
  });

  displayWorkHistory(sortedData); // Toon de gesorteerde data
}

function displayWorkHistory(historyData) {
  let historyHTML = "<h1>Werkgeschiedenis voor deze maand:</h1>";
  console.log(historyData, "history");
  console.log(historyData ? historyData.length : undefined, "lengte");
  if (historyData && historyData.length > 0) {
    historyHTML += "<table>";
    historyHTML +=
      "<thead><tr><th>Naam</th><th>Datum</th><th>Gewerkte uren</th><th>aanpassen</th></tr></thead>";
    historyHTML += "<tbody>";
    historyData.forEach((record) => {
      const starttijdParts = record.starttijd.split(" ");
      const startDate = new Date(starttijdParts[0] + "T" + starttijdParts[2]);

      const eindtijdParts = record.eindtijd ? record.eindtijd.split(" ") : null;
      const endDate = eindtijdParts
        ? new Date(eindtijdParts[0] + "T" + eindtijdParts[2])
        : null;

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
      historyHTML += `<td>${record.VolledigeNaam}</td>`;
      historyHTML += `<td>${formattedDate}</td>`;
      historyHTML += `<td>Gewerkt van ${startTime} tot ${endTime} in totaal: ${record.totaal_tijd} uur</td>`;
      historyHTML += `<td><a class="edit" href="werkuren_aanpassen.php?id=${record.pk_personeel}&pk=${record.pk_werkuren}">aanpassen</a></td>`;
      historyHTML += `</tr>`;
    });

    // Nieuwe rij toevoegen met een lege cel en een "Toevoegen" knop
    historyHTML += `<tr>`;
    historyHTML += `<td></td>`;
    historyHTML += `<td></td>`;
    historyHTML += `<td></td>`;
    historyHTML += `<td><button id="addWorkHourBtn">Toevoegen</button></td>`;
    historyHTML += `</tr>`;

    historyHTML += "</tbody>";
    historyHTML += "</table>";
  } else {
    historyHTML += "<p>Geen werkuren gevonden voor deze dag.</p>";
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

  // Event listener toevoegen aan de "Toevoegen" knop
  const addButton = document.getElementById("addWorkHourBtn");
  if (addButton) {
    addButton.addEventListener("click", function() {
      window.location.href = "werknemer_toevoegen.php"; // Link naar de pagina om nieuwe werknemers toe te voegen
    });
  }
}

document.addEventListener("DOMContentLoaded", function () {
  ajaxcall();
  const filterNaam = document.getElementById("filterNaam");
  const filterDatum = document.getElementById("filterDatum");
  const sortBy = document.getElementById("sortBy");

  if (filterNaam) {
    filterNaam.addEventListener("change", filterWorkHistory);
  }
  if (filterDatum) {
    filterDatum.addEventListener("change", filterWorkHistory);
  }
  if (sortBy) {
    sortBy.addEventListener("change", () => sortWorkHistory(allHistoryData));
  }
});

showWorkHistory();