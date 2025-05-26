let personeel_id_van_login;
let schoolPolygons = []; // Gebruik een array om meerdere polygons op te slaan
let huidigeJaar, huidigeMaand;
let huidigJaarKalender, huidigeMaandKalender;
let currentYearPickerYear; // Om het huidige startjaar van de picker bij te houden

// Break Timer Variables
let breakTimerInterval = null; // Tracks the JS setInterval for visual updates
let breakCurrentTimeInSeconds = 0; // Represents the value shown on the timer display
let isBreakTimerRunning = false; // Tracks if the break is logically running (e.g., not paused)

// New Global Variables for Persistent Break Timer
let serverBreakStartTime = null; // Stores 'current_break_start_timestamp' from server (string or null)
let clientSideCalculatedAccumulatedSeconds = 0; // Stores 'current_break_accumulated_seconds_before_pause' from server, then updated by client on pause
let clientBreakSegmentStartEpoch = null; // JS Date.now() when break starts/resumes on client

// Functie om het aantal dagen in een maand te bepalen
// https://stackoverflow.com/questions/1184334/get-number-days-in-a-specified-month-using-javascript
function getDaysInMonth(year, month) {
  return new Date(year, month, 0).getDate();
}

function isPointInPolygon(point, polygon) {
  const x = point.latitude;
  const y = point.longitude;
  let inside = false;
  for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
    const xi = polygon[i].latitude,
      yi = polygon[i].longitude;
    const xj = polygon[j].latitude,
      yj = polygon[j].longitude;
    const intersect =
      yi > y !== yj > y && x < ((xj - xi) * (y - yi)) / (yj - yi) + xi;
    if (intersect) inside = !inside;
  }
  return inside;
}

function getPersoneelFK() {
  console.log("getPersoneelFK functie wordt uitgevoerd.");
  return $.ajax({
    url: "API.php",
    type: "POST",
    data: {
      action: "get_session_personeel_id", // Voeg de 'action' parameter toe
    },
    dataType: "json",
  })
    .done(function (data) {
      console.log("Personeel ID data ontvangen:", data);
      if (data && data.personeel_id !== null) {
        personeel_id_van_login = data.personeel_id;
        ajaxcall();
      } else {
        console.error(
          "Kon personeel ID niet ophalen:",
          data ? data.error : "Onbekende fout"
        );
      }
    })
    .fail(function (xhr, status, error) {
      console.error("Fout bij het ophalen van personeel ID:", status, error);
    });
}

function ajaxcall() {
  console.log("ajaxcall functie (start info) wordt uitgevoerd.");
  const startButton = document.getElementById("startButton");
  const stopButton = document.getElementById("stopButton");
  // Optioneel: voeg een element toe in je HTML om een bericht te tonen
  const messageDisplay = document.getElementById("messageDisplay"); // Bijv: <p id="messageDisplay"></p>

  // Zorg dat knoppen bestaan
  if (!startButton || !stopButton) {
    console.error("Start- of stopknop niet gevonden in ajaxcall.");
    return;
  }
  // Verberg bericht initieel (indien gebruikt)
  if (messageDisplay) {
    messageDisplay.style.display = "none";
    messageDisplay.textContent = "";
  }

  if (personeel_id_van_login === undefined) {
    console.warn(
      "Personeel ID is nog niet geladen. Wacht of controleer de getPersoneelFK functie."
    );
    // Optioneel: verberg knoppen als ID ontbreekt
    startButton.style.display = "none";
    stopButton.style.display = "none";
    return;
  }

  $.ajax({
    url: "API.php",
    type: "POST",
    data: {
      action: "get_start_info",
      personeel_fk: personeel_id_van_login,
    },
    // dataType: "json", // Meestal niet nodig als PHP de Content-Type header correct zet

    success: function (data) {
      // 'data' is HIER AL HET JAVASCRIPT OBJECT
      console.log("Start info data ontvangen:", data); // Nu zou je het object moeten zien

      let startInfo = data; // GEBRUIK 'data' DIRECT ALS HET startInfo OBJECT

      // Voeg eventueel een extra check toe of startInfo wel een object is
      if (startInfo && typeof startInfo === "object") {
        // Initialize break state variables from server data
        serverBreakStartTime = startInfo.current_break_start_timestamp; 
        clientSideCalculatedAccumulatedSeconds = startInfo.current_break_accumulated_seconds_before_pause || 0;

        if (serverBreakStartTime) {
            // Break was running when user last left or server state was last updated
            // Assuming serverBreakStartTime is a string like "YYYY-MM-DD HH:MM:SS" from MySQL DATETIME (implicitly UTC or server local)
            // To parse it reliably as UTC for calculations:
            const serverStartTimeEpoch = new Date(serverBreakStartTime.replace(" ", "T") + "Z").getTime();
            const elapsedSinceServerStart = (Date.now() - serverStartTimeEpoch) / 1000; // in seconds
            
            breakCurrentTimeInSeconds = clientSideCalculatedAccumulatedSeconds + Math.max(0, elapsedSinceServerStart);
            isBreakTimerRunning = true; // Indicates break is logically running
            clientBreakSegmentStartEpoch = serverStartTimeEpoch; // To continue counting from this point if user enters break mode
        } else {
            // Break was paused or not active
            breakCurrentTimeInSeconds = clientSideCalculatedAccumulatedSeconds;
            isBreakTimerRunning = false; // Indicates break is logically paused
            clientBreakSegmentStartEpoch = null; // Not actively timing a segment
        }
        // Update the timer display in the hidden break UI, so it's correct if user enters break mode
        const timerDisplayElement = document.getElementById('timerDisplay');
        if (timerDisplayElement) {
            timerDisplayElement.textContent = formatTime(Math.max(0, Math.round(breakCurrentTimeInSeconds)));
        }

        // --- Rest van je logica blijft hetzelfde ---
        const naamDisplay = document.getElementById("naamDisplay");
        if (naamDisplay && startInfo.personeelsnaam) {
          naamDisplay.textContent = startInfo.personeelsnaam;
          // console.log("Naam weergegeven:", "Hey " + startInfo.personeelsnaam); // Al gelogd
        } else {
          console.log("naamDisplay element niet gevonden of naam ontbreekt.");
        }
        // console.log("Waarde van startInfo:", startInfo); // Al gelogd

        if (startInfo.polygons && Array.isArray(startInfo.polygons)) {
          schoolPolygons = startInfo.polygons; // Wijs de array direct toe
          console.log(
            "Polygon coördinaten geladen:",
            schoolPolygons.length,
            "polygon(en)"
          );
        } else {
          console.warn(
            "Geen geldige polygon coördinaten ontvangen in startInfo.polygons."
          );
          schoolPolygons = []; // Reset naar lege array
        }

        // --- Knop Logica (blijft hetzelfde) ---
        if (startInfo.isVandaagAlGewerktofGestopt === true) {
          console.log("Werkdag al voltooid vandaag. Knoppen verbergen.");
          startButton.style.display = "none";
          stopButton.style.display = "none";
          if (messageDisplay) {
            messageDisplay.textContent =
              "Je hebt je werksessie voor vandaag al voltooid.";
            messageDisplay.style.display = "block";
          }
        } else {
          console.log("Werkdag nog niet voltooid. Controleer actieve status.");
          if (messageDisplay) {
            messageDisplay.style.display = "none";
          }
          if (startInfo.isWerkuurGestart === true) {
            console.log("Werkuur is actief. Toon Stop-knop.");
            startButton.style.display = "none";
            stopButton.style.display = "block";
          } else {
            console.log("Geen actief werkuur. Toon Start-knop.");
            startButton.style.display = "block";
            stopButton.style.display = "none";
          }
        }
        // --- Einde Knop Logica ---
      } else {
        // Fallback als 'data' om een of andere reden geen geldig object is
        console.error(
          "Ongeldige of lege startinformatie ontvangen:",
          startInfo
        );
        startButton.style.display = "none";
        stopButton.style.display = "none";
        if (messageDisplay) {
          messageDisplay.textContent = "Kon status niet correct ophalen.";
          messageDisplay.style.display = "block";
        }
      }
    },
    error: function (xhr, status, error) {
      // Log eventueel xhr.responseText voor meer details bij een AJAX error
      console.error(
        "Fout bij het ophalen van startinformatie (AJAX):",
        status,
        error,
        xhr.responseText
      );
      startButton.style.display = "none";
      stopButton.style.display = "none";
      if (messageDisplay) {
        messageDisplay.textContent = "Communicatiefout met server.";
        messageDisplay.style.display = "block";
      }
    },
  });
}

// Zorg ervoor dat de rest van je start.js code (event listeners etc.) correct is.

function startWerkuur() {
  console.log("startWerkuur functie wordt uitgevoerd.");
  const startButton = document.getElementById("startButton");
  const stopButton = document.getElementById("stopButton");
  const timeDisplay = document.getElementById("timeDisplay");

  if (personeel_id_van_login === undefined) {
    console.warn(
      "Personeel ID is nog niet geladen. Start werkuur actie geannuleerd."
    );
    return;
  }

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      function (position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        console.log("Huidige locatie:", latitude, longitude);

        const userLocation = {
          latitude: latitude,
          longitude: longitude,
        };

        let isInsideAnyPolygon = false;
        if (schoolPolygons && Array.isArray(schoolPolygons)) {
          for (const polygon of schoolPolygons) {
            if (isPointInPolygon(userLocation, polygon)) {
              isInsideAnyPolygon = true;
              break;
            }
          }
        }

        if (isInsideAnyPolygon) {
          const school_fk = 101;
          const job_fk = 1;

          $.ajax({
            url: "API.php",
            type: "POST",
            data: {
              action: "start_werkuur", // Voeg de 'action' parameter toe
              personeel_fk: personeel_id_van_login,
              school_fk: school_fk,
              job_fk: job_fk,
            },
            success: function (data) {
              console.log("Start werkuur response ontvangen:", data);
              let response = data;
              if (response.status === "success") {
                console.log("Werkuur gestart!");
                if (startButton && stopButton) {
                  startButton.style.display = "none";
                  stopButton.style.display = "block";
                }
                ajaxcall();
              } else if (response.status === "error") {
                alert(response.message);
                console.error("Fout bij starten werkuur:", response.message);
              } else {
                alert("Er is een onbekende fout opgetreden.");
                console.error("Fout bij starten werkuur:", response);
              }
            },
            error: function (xhr, status, error) {
              console.error(
                "Fout bij het starten van het werkuur:",
                status,
                error
              );
            },
          });
        } else {
          alert("Je moet op schoolgrond zijn om je werkuur te starten.");
        }
      },
      function (error) {
        switch (error.code) {
          case error.PERMISSION_DENIED:
            alert(
              "Locatietoegang geweigerd. Sta locatiebepaling toe om te starten."
            );
            break;
          case error.POSITION_UNAVAILABLE:
            alert("Locatie-informatie is momenteel niet beschikbaar.");
            break;
          case error.TIMEOUT:
            alert("De aanvraag voor locatiebepaling is verlopen.");
            break;
          case error.UNKNOWN_ERROR:
            alert(
              "Er is een onbekende fout opgetreden bij de locatiebepaling."
            );
            break;
        }
      }
    );
  } else {
    alert("Geolocation wordt niet ondersteund door deze browser.");
  }
}

function stopWerkuur() {
  console.log("stopWerkuur functie wordt uitgevoerd.");
  const startButton = document.getElementById("startButton");
  const stopButton = document.getElementById("stopButton");
  // const timeDisplay = document.getElementById("timeDisplay"); // Niet gebruikt in deze functie

  if (personeel_id_van_login === undefined) {
    console.warn(
      "Personeel ID is nog niet geladen. Stop werkuur actie geannuleerd."
    );
    return;
  }

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      function (position) {
        // --- FIX IS HIER ---
        const userLocation = {
          latitude: position.coords.latitude,
          // Gebruik direct de waarde uit het position object
          longitude: position.coords.longitude,
        };
        // --- EINDE FIX ---
        console.log(
          "Huidige locatie voor stop:",
          userLocation.latitude,
          userLocation.longitude
        ); // Goed voor debugging

        let isInsideAnyPolygon = false;
        if (schoolPolygons && Array.isArray(schoolPolygons)) {
          for (const polygon of schoolPolygons) {
            if (isPointInPolygon(userLocation, polygon)) {
              isInsideAnyPolygon = true;
              break;
            }
          }
        }

        if (isInsideAnyPolygon) {
          if (confirm("Weet je zeker dat je het werkuur wilt stoppen?")) {
            let finalTotalBreakSeconds = 0;
            if (isBreakTimerRunning && clientBreakSegmentStartEpoch) { // Break was running actively when work stopped
                const elapsedThisSegment = (Date.now() - clientBreakSegmentStartEpoch) / 1000;
                finalTotalBreakSeconds = clientSideCalculatedAccumulatedSeconds + elapsedThisSegment;
            } else { // Break was paused or never started/resumed in this client session view
                finalTotalBreakSeconds = clientSideCalculatedAccumulatedSeconds;
            }
            finalTotalBreakSeconds = Math.round(finalTotalBreakSeconds);

            $.ajax({
              url: "API.php",
              type: "POST",
              data: {
                action: "stop_werkuur", // PHP uses $_SESSION['personeel_id']
                total_final_break_seconds: finalTotalBreakSeconds
              },
              dataType: "json", // Expecting JSON response from the updated API
              success: function (response) { // 'response' is already a JS object
                console.log("Stop werkuur response ontvangen:", response);
                
                if (response.success) {
                  console.log("Werkuur gestopt, break time recorded.");
                  // Reset client-side break variables
                  serverBreakStartTime = null;
                  clientSideCalculatedAccumulatedSeconds = 0;
                  breakCurrentTimeInSeconds = 0;
                  isBreakTimerRunning = false; // Logical break state
                  clientBreakSegmentStartEpoch = null;
                  if (breakTimerInterval) clearInterval(breakTimerInterval); // Clear visual timer
                  
                  // Update UI: show Start button, hide Stop button, refresh general info
                  // ajaxcall() will handle this and also reset the timer display in break UI to 00:00
                  ajaxcall(); 
                } else {
                  alert("Fout bij stoppen werkuur: " + (response.message || "Onbekende fout."));
                  console.error("Fout bij stoppen werkuur (API):", response.message || response.error || "Onbekende API fout");
                }
              },
              error: function (xhr, status, error) {
                console.error(
                  "Fout bij het stoppen van het werkuur (AJAX):",
                  status,
                  error,
                  xhr.responseText
                );
                alert("Communicatiefout bij het stoppen van het werkuur.");
              },
            });
          } else {
            console.log("Stoppen van werkuur geannuleerd.");
          }
        } else {
          alert("Je moet op schoolgrond zijn om je werkuur te stoppen.");
        }
      },
      function (error) {
        // Error handling (geen wijziging nodig hier)
        switch (error.code) {
          case error.PERMISSION_DENIED:
            alert(
              "Locatietoegang geweigerd. Sta locatiebepaling toe om te stoppen."
            );
            break;
          case error.POSITION_UNAVAILABLE:
            alert("Locatie-informatie is momenteel niet beschikbaar.");
            break;
          case error.TIMEOUT:
            alert("De aanvraag voor locatiebepaling is verlopen.");
            break;
          case error.UNKNOWN_ERROR:
            alert(
              "Er is een onbekende fout opgetreden bij de locatiebepaling."
            );
            break;
        }
        console.error("Geolocation error:", error); // Log het error object voor meer details
      }
    );
  } else {
    alert("Geolocation wordt niet ondersteund door deze browser.");
  }
}

function toggleYearPicker() {
  const yearPickerContainer = document.getElementById("yearPickerContainer");
  yearPickerContainer.classList.toggle("hidden");
  if (!yearPickerContainer.classList.contains("hidden")) {
    currentYearPickerYear = huidigJaarKalender;
    renderYearPicker(currentYearPickerYear);
  }
}

function changeYearPickerYear(offset) {
  currentYearPickerYear += offset;
  renderYearPicker(currentYearPickerYear);
}

function renderYearPicker(year) {
  const yearPickerTitle = document.getElementById("yearPickerTitle");
  const yearPickerGrid = document.getElementById("yearPickerGrid");
  yearPickerTitle.textContent = `${year - 5} - ${year + 4}`; // Toon een reeks van 10 jaar
  yearPickerGrid.innerHTML = "";

  for (let i = year - 5; i <= year + 4; i++) {
    const yearButton = document.createElement("button");
    yearButton.textContent = i;
    yearButton.addEventListener("click", function () {
      huidigJaarKalender = i;
      genereerKalender();
      displayMaandJaar();
      toggleYearPicker();
    });
    if (i === huidigJaarKalender) {
      yearButton.classList.add("bg-blue-500", "text-white"); // Markeer het huidige jaar
    }
    yearPickerGrid.appendChild(yearButton);
  }
}

function vorigeMaand() {
  huidigeMaandKalender--;
  if (huidigeMaandKalender < 1) {
    huidigeMaandKalender = 12;
    huidigJaarKalender--;
  }
  genereerKalender();
  displayMaandJaar();
}

function volgendeMaand() {
  huidigeMaandKalender++;
  if (huidigeMaandKalender > 12) {
    huidigeMaandKalender = 1;
    huidigJaarKalender++;
  }
  genereerKalender();
  displayMaandJaar();
}

function selectMonth(month) {
  huidigeMaandKalender = month + 1; // Maand is 0-based in selectMonth
  genereerKalender();
  displayMaandJaar();
  document.getElementById("closeModal").click();
}

function displayMaandJaar() {
  const maanden = [
    "Januari",
    "Februari",
    "Maart",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Augustus",
    "September",
    "Oktober",
    "November",
    "December",
  ];
  document.getElementById("maandDisplay").textContent =
    maanden[huidigeMaandKalender - 1];
  document.getElementById("displayYear").textContent = huidigJaarKalender;
}

function genereerKalender() {
  let jaar = huidigJaarKalender;
  let maand = huidigeMaandKalender;

  let dagenInMaanden = getDaysInMonth(jaar, maand);
  let eersteDag = new Date(jaar, maand - 1, 1).getDay(); // 0 is zondag, 1 is maandag
  let offset = eersteDag === 0 ? 6 : eersteDag - 1;

  let showKalender = document.getElementById("showKalender");
  let html =
    '<div class="flex flex-row flex-wrap divide-x-[1px] divide-kalender-day border-b-[1px] border-kalender-day">';
  let dagTeller = 0; // Houd de dag van de week bij om de rijen te sluiten

  // Dagen van de vorige maand aan het begin grijs maken
  if (offset > 0) {
    let vorigeMaand = maand === 1 ? 12 : maand - 1;
    let vorigJaar = maand === 1 ? jaar - 1 : jaar;
    let dagenInVorigeMaand = getDaysInMonth(vorigJaar, vorigeMaand);
    for (let i = 0; i < offset; i++) {
      const dagNummerVorigeMaand = dagenInVorigeMaand - offset + i + 1;
      html += `<button class="flex justify-center items-center p-kalender py-[10px] md:py-2 bg-kalender-legencellen w-[14.28%] text-gray-400"><p>${dagNummerVorigeMaand}</p></button>`;
      dagTeller++;
    }
  }

  $.ajax({
    url: "API.php",
    type: "POST",
    data: {
      action: "get_monthly_history",
      personeel_fk: personeel_id_van_login,
      year: jaar,
      month: maand,
    },
    dataType: "json",
    success: function (monthlyHistory) {
      console.log("Maandelijkse werkuren ontvangen:", monthlyHistory);

      for (let i = 1; i <= dagenInMaanden; i++) {
        let dayClass = "";
        const dagString = i.toString().padStart(2, "0");
        const maandString = maand.toString().padStart(2, "0");
        const zoekDatum = `${jaar}-${maandString}-${dagString}`;

        const historyForDay = monthlyHistory.filter((record) => {
          if (record.starttijd) {
            const recordDatum = record.starttijd.split(" ")[0]; // Haal de datumdeel uit de starttijd
            return recordDatum === zoekDatum;
          }
          return false;
        });

        if (historyForDay.length > 0) {
          const hasStart = historyForDay.some(
            (record) => record.starttijd !== null
          );
          const hasStop = historyForDay.some(
            (record) => record.eindtijd !== null
          );

          if (hasStart && !hasStop) {
            dayClass = "bg-blue-200"; // werkuur-gestart
          } else if (hasStart && hasStop) {
            dayClass = "bg-green-200"; // werkuur-gestopt
          }
        }

        (function (dagNummer) {
          html += `<button onclick="toonWerkurenDag(${dagNummer})" class="flex justify-center items-center p-kalender py-[10px] md:py-2 w-[14.28%] ${dayClass}"><p>${dagNummer}</p></button>`;
        })(i);
        dagTeller++;

        if (dagTeller % 7 === 0 && i < dagenInMaanden) {
          html +=
            '</div><div class="flex flex-row flex-wrap divide-x-[1px] divide-kalender-day border-b-[1px] border-kalender-day">';
        }
      }

      // Dagen van de volgende maand grijs maken
      let volgendeMaandDagen = 1;
      while (dagTeller % 7 !== 0) {
        html += `<button class="flex justify-center items-center p-kalender py-[10px] md:py-2 bg-kalender-legencellen w-[14.28%] text-gray-400"><p>${volgendeMaandDagen}</p></button>`;
        volgendeMaandDagen++;
        dagTeller++;
      }

      html += "</div>"; // Sluit de laatste rij af

      if (showKalender) {
        showKalender.innerHTML = html;
      }
    },
    error: function (xhr, status, error) {
      console.error(
        "Fout bij het ophalen van maandelijkse werkuren:",
        status,
        error
      );
      // Fallback om de kalender zonder markering te tonen bij een fout
      for (let i = 1; i <= dagenInMaanden; i++) {
        (function (dagNummer) {
          html += `<button onclick="toonWerkurenDag(${dagNummer})" class="flex justify-center items-center p-kalender py-[10px] md:py-2 w-[14.28%]"><p>${dagNummer}</p></button>`;
        })(i);
        dagTeller++;
        if (dagTeller % 7 === 0 && i < dagenInMaanden) {
          html +=
            '</div><div class="flex flex-row flex-wrap divide-x-[1px] divide-kalender-day border-b-[1px] border-kalender-day">';
        }
      }
      let volgendeMaandDagen = 1;
      while (dagTeller % 7 !== 0) {
        html += `<button class="flex justify-center items-center p-kalender py-[10px] md:py-2 bg-kalender-legencellen w-[14.28%] text-gray-400"><p>${volgendeMaandDagen}</p></button>`;
        volgendeMaandDagen++;
        dagTeller++;
      }
      html += "</div>";
      if (showKalender) {
        showKalender.innerHTML = html;
      }
    },
  });
}

function showWorkHistory() {
  console.log("showWorkHistory functie wordt uitgevoerd.");

  const calendarSection = document.getElementById("calendarSection");
  const startButton = document.getElementById("startButton");
  const stopButton = document.getElementById("stopButton");
  const historyButton = document.getElementById("historyButton");
  const displayYearElement = document.getElementById("displayYear");
  const maandDisplayElement = document.getElementById("maandDisplay");

  // Controleer of alle elementen bestaan
  if (
    !calendarSection ||
    !startButton ||
    !stopButton ||
    !historyButton ||
    !displayYearElement ||
    !maandDisplayElement
  ) {
    console.error(
      "Een of meer benodigde elementen niet gevonden voor showWorkHistory."
    );
    return; // Stop de functie als een element ontbreekt
  }

  const isCalendarVisible = calendarSection.style.display === "flex";

  if (isCalendarVisible) {
    // --- Kalender is ZICHTBAAR, dus VERBERG hem ---
    console.log("Kalender sluiten.");
    calendarSection.style.display = "none";
    historyButton.style.display = "block";

    // Roep ajaxcall opnieuw aan om de juiste start/stop knop te tonen
    ajaxcall();
  } else {
    // --- Kalender is VERBORGEN, dus TOON hem ---
    console.log("Kalender openen.");
    calendarSection.style.display = "flex"; // Toon de kalender
    historyButton.style.display = "none"; // Verberg de geschiedenisknop

    // Bepaal de huidige maand en jaar voor de kalender
    let vandaag = new Date();
    huidigJaarKalender = vandaag.getFullYear();
    huidigeMaandKalender = vandaag.getMonth() + 1;

    // Genereer de kalender
    genereerKalender();
    displayMaandJaar();

    // Zorg dat de juiste start/stop knop zichtbaar blijft
    const isStartCurrentlyVisible = startButton.style.display !== "none";
    if (isStartCurrentlyVisible) {
      startButton.style.display = "block";
      stopButton.style.display = "none";
    } else {
      startButton.style.display = "none";
      stopButton.style.display = "block";
    }
  }
}

function toonWerkurenDag(dagNummer) {
  const gekozenDatum = new Date(
    huidigJaarKalender,
    huidigeMaandKalender - 1,
    dagNummer
  );
  const dag = gekozenDatum.getDate().toString().padStart(2, "0");
  const maand = (gekozenDatum.getMonth() + 1).toString().padStart(2, "0");
  const jaar = gekozenDatum.getFullYear();
  const formattedDatum = `${jaar}-${maand}-${dag}`;

  console.log(`Werkuren ophalen voor: ${formattedDatum}`);

  if (personeel_id_van_login === undefined) {
    console.warn("Personeel ID is nog niet geladen.");
    return;
  }

  $.ajax({
    url: "API.php",
    type: "POST",
    data: {
      action: "get_daily_history", // Voeg de 'action' parameter toe
      personeel_fk: personeel_id_van_login,
      date: formattedDatum, // Stuur de specifieke datum
    },
    success: function (data) {
      console.log("Werkuren voor dag ontvangen:", data);
      const dailyHistory = data;
      displayDailyWorkHours(dailyHistory, formattedDatum);
    },
    error: function (xhr, status, error) {
      console.error(
        "Fout bij het ophalen van de werkuren voor de dag:",
        status,
        error
      );
    },
  });
}

function displayDailyWorkHours(historyData, datum) {
  let alertHTML =
    '<div class="fixed top-0 left-0 w-full h-full bg-black/50 flex items-center justify-center z-20">';
  alertHTML +=
    '<div class="bg-white w-full max-w-[500px] min-h-[200px] rounded-xl flex flex-col items-center justify-center gap-4 p-4">';
  alertHTML += `<h2>Werkuren voor ${datum}</h2>`;

  if (historyData && historyData.length > 0) {
    historyData.forEach((record) => {
      const starttijdParts = record.starttijd.split(" ");
      const startTime = starttijdParts[1].substring(0, 5);
      const eindtijdParts = record.eindtijd ? record.eindtijd.split(" ") : null;
      const endTime = eindtijdParts
        ? eindtijdParts[1].substring(0, 5)
        : "Nog niet gestopt";
      alertHTML += `<p>Van ${startTime} tot ${endTime}</p>`;
    });
  } else {
    alertHTML += "<p>Geen werkuren gevonden voor deze dag.</p>";
  }

  alertHTML +=
    '<button onclick="sluitAlert()" class="text-center bg-default-grey text-white rounded-md py-2 px-4 cursor-pointer">OK</button>';
  alertHTML += "</div></div>";

  let customAlert = document.getElementById("customAlert");
  if (customAlert) {
    customAlert.innerHTML = alertHTML;
  }
}

function sluitAlert() {
  let customAlert = document.getElementById("customAlert");
  if (customAlert) {
    customAlert.innerHTML = "";
  }
}

window.history.pushState(null, null, window.location.pathname);
window.addEventListener("popstate", function (event) {
  window.history.pushState(null, null, window.location.pathname);
});

document.addEventListener("DOMContentLoaded", function () {
  getPersoneelFK();

  const startButton = document.getElementById("startButton");
  const stopButton = document.getElementById("stopButton"); // Main work stop button
  const historyButton = document.getElementById("historyButton");
  // const displayYearElement = document.getElementById("displayYear"); // Already declared if needed for calendar

  // New elements for break mode
  const breakButton = document.getElementById('breakButton');
  const stopBreakButton = document.getElementById('stopBreakButton');
  const playButton = document.getElementById('playButton');
  const pauseButton = document.getElementById('pauseButton');
  // helloMessageContainer, timerDisplayContainer, timerDisplay are primarily accessed within functions

  if (startButton) {
    startButton.addEventListener("click", startWerkuur);
  } else {
    console.error("Start knop (werk) niet gevonden!");
  }

  if (stopButton) { // Main work stop button
    stopButton.addEventListener("click", stopWerkuur);
  } else {
    console.error("Stop knop (werk) niet gevonden!");
  }

  if (historyButton) {
    historyButton.addEventListener("click", showWorkHistory);
  } else {
    console.error("Geschiedenis knop niet gevonden!");
  }

  // Event listeners for break mode
  if (breakButton) {
      breakButton.addEventListener('click', function() {
          // Reset client-side counters for a new break sequence
          clientSideCalculatedAccumulatedSeconds = 0; 
          breakCurrentTimeInSeconds = 0; // Display will start from 0
          clientBreakSegmentStartEpoch = Date.now(); // Mark start of this segment
          
          $.ajax({
              url: 'API.php',
              type: 'POST',
              data: {
                  action: 'start_break',
                  client_timestamp_ms: clientBreakSegmentStartEpoch // Send the client's perceived start time
              },
              dataType: 'json',
              success: function(response) {
                  if (response.status === 'success') {
                      isBreakTimerRunning = true; // JS timer will be started by toggleBreakMode
                      // serverBreakStartTime will be updated by the next ajaxcall, or can be set here if needed for immediate consistency
                      // For now, we assume ajaxcall() will refresh it soon or it's implicitly handled by UI flow.
                      // Approximate server time for consistency if needed before next ajaxcall
                      serverBreakStartTime = new Date(clientBreakSegmentStartEpoch).toISOString().slice(0, 19).replace('T', ' ');
                      
                      toggleBreakMode(true); // Shows UI, handles button states, and should call startBreakTimer
                  } else {
                      alert('Error starting break: ' + (response.message || 'Unknown error'));
                      // If API fails, reset clientBreakSegmentStartEpoch as the segment didn't really start server-side
                      clientBreakSegmentStartEpoch = null;
                  }
              },
              error: function() {
                  alert('API communication error starting break.');
                  clientBreakSegmentStartEpoch = null; // Reset on error
              }
          });
      });
  } else {
      console.error("Break knop niet gevonden!");
  }

  if (stopBreakButton) {
      stopBreakButton.addEventListener('click', function() {
          if (breakTimerInterval) clearInterval(breakTimerInterval); // Stop JS interval
          // The logical state (isBreakTimerRunning, serverBreakStartTime, clientSideCalculatedAccumulatedSeconds, clientBreakSegmentStartEpoch)
          // remains as is, reflecting the true state of the break.
          // breakCurrentTimeInSeconds also holds the last displayed value.
          toggleBreakMode(false); // Hides break UI, shows main UI. ajaxcall() inside will refresh main buttons.
      });
  } else {
      console.error("Stop Break knop niet gevonden!");
  }

  if (playButton) {
      playButton.addEventListener('click', function() {
          clientBreakSegmentStartEpoch = Date.now(); // New start for this running portion

          $.ajax({
              url: 'API.php',
              type: 'POST',
              data: {
                  action: 'resume_break',
                  client_timestamp_ms: clientBreakSegmentStartEpoch
              },
              dataType: 'json',
              success: function(response) {
                  if (response.status === 'success') {
                      isBreakTimerRunning = true; // Logically, the break is running
                      // serverBreakStartTime will be updated by API, but for immediate UI consistency:
                      serverBreakStartTime = new Date(clientBreakSegmentStartEpoch).toISOString().slice(0, 19).replace('T', ' ');
                      
                      // startBreakTimer will handle button states and the visual interval
                      startBreakTimer(); 
                  } else {
                      alert('Error resuming break: ' + (response.message || 'Unknown error'));
                      // If API fails, don't consider the segment started
                      clientBreakSegmentStartEpoch = null;
                  }
              },
              error: function() {
                  alert('API communication error resuming break.');
                  clientBreakSegmentStartEpoch = null;
              }
          });
      });
  } else {
      console.error("Play knop (pauze) niet gevonden!");
  }

  if (pauseButton) {
      pauseButton.addEventListener('click', function() {
          if (!isBreakTimerRunning && !clientBreakSegmentStartEpoch) {
              // Not logically running or no segment started, UI buttons might be out of sync
              if(document.getElementById('playButton')) document.getElementById('playButton').disabled = false;
              if(document.getElementById('pauseButton')) document.getElementById('pauseButton').disabled = true;
              return;
          }

          if (breakTimerInterval) clearInterval(breakTimerInterval); // Stop JS visual interval

          // Calculate elapsed time for the current segment and add to total
          const elapsedThisSegment = clientBreakSegmentStartEpoch ? (Date.now() - clientBreakSegmentStartEpoch) / 1000 : 0;
          clientSideCalculatedAccumulatedSeconds += elapsedThisSegment;
          breakCurrentTimeInSeconds = clientSideCalculatedAccumulatedSeconds; // Update display to final paused time
          clientBreakSegmentStartEpoch = null; // Mark segment as paused/ended

          $.ajax({
              url: 'API.php',
              type: 'POST',
              data: {
                  action: 'pause_break',
                  accumulated_seconds: Math.round(clientSideCalculatedAccumulatedSeconds)
              },
              dataType: 'json',
              success: function(response) {
                  if (response.status === 'success') {
                      isBreakTimerRunning = false; // Logically paused
                      serverBreakStartTime = null; // Server confirms break is not actively running from a 'start' timestamp perspective
                      
                      // Update UI timer display one last time to be sure
                      const timerDisplayElement = document.getElementById('timerDisplay');
                      if (timerDisplayElement) {
                          timerDisplayElement.textContent = formatTime(Math.max(0, Math.round(breakCurrentTimeInSeconds)));
                      }
                      if (document.getElementById('playButton')) document.getElementById('playButton').disabled = false;
                      if (document.getElementById('pauseButton')) document.getElementById('pauseButton').disabled = true;
                  } else {
                      alert('Error pausing break: ' + (response.message || 'Unknown error'));
                      // Re-enable segment timing if API failed? Or leave as paused UI?
                      // For now, UI reflects the pause attempt. If API failed, clientSideCalculatedAccumulatedSeconds might be off from server.
                      // Re-sync via ajaxcall() might be needed or a specific error handling strategy.
                      // For simplicity, we assume UI reflects what we told the server.
                  }
              },
              error: function() {
                  alert('API communication error pausing break.');
              }
          });
      });
  } else {
      console.error("Pause knop (pauze) niet gevonden!");
  }

  // Initial display state for main start/stop buttons (stop is initially hidden by default)
  // This existing logic should correctly hide the main stop button on page load.
  if (startButton && stopButton) {
    stopButton.style.display = "none";
  }

  let vandaag = new Date();
  huidigJaarKalender = vandaag.getFullYear();
  huidigeMaandKalender = vandaag.getMonth() + 1;
  displayMaandJaar(); // Initial display van maand en jaar
});

function herlaad() {
  location.reload();
}

// Helper function to format time in MM:SS
function formatTime(totalSeconds) {
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;
    return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

// Function to toggle UI for break mode
function toggleBreakMode(isInBreakMode) {
    const helloMessageContainer = document.getElementById('helloMessageContainer');
    const timerDisplayContainer = document.getElementById('timerDisplayContainer');
    const mainStartWorkButton = document.getElementById('startButton'); 
    const mainBreakButton = document.getElementById('breakButton'); 
    const breakControlsContainer = document.getElementById('breakControlsContainer');
    const mainStopWorkButton = document.getElementById('stopButton'); 
    const timerDisplayElement = document.getElementById('timerDisplay');
    const playButton = document.getElementById('playButton');
    const pauseButton = document.getElementById('pauseButton');

    if (isInBreakMode) {
        // --- ENTERING BREAK MODE UI ---
        if (helloMessageContainer) helloMessageContainer.style.display = 'none';
        if (timerDisplayContainer) timerDisplayContainer.style.display = 'block';
        if (mainStartWorkButton) mainStartWorkButton.style.display = 'none';
        if (mainBreakButton) mainBreakButton.style.display = 'none';
        if (breakControlsContainer) breakControlsContainer.style.display = 'block';
        if (mainStopWorkButton) mainStopWorkButton.style.display = 'none';

        // Initialize timer display with the current break time (could be > 0 if resuming from page reload)
        if (timerDisplayElement) {
            timerDisplayElement.textContent = formatTime(Math.max(0, Math.round(breakCurrentTimeInSeconds)));
        }
        
        // If the break is logically running (or just started/resumed), start the JS interval
        // isBreakTimerRunning reflects the logical state (true if running, false if paused)
        if (isBreakTimerRunning) { 
            startBreakTimer(); // This will disable play, enable pause, and start interval
        } else {
            // Break is paused (e.g., page reloaded while break was paused)
            if (playButton) playButton.disabled = false;
            if (pauseButton) pauseButton.disabled = true;
            if (breakTimerInterval) clearInterval(breakTimerInterval); // Ensure no stray interval is running
        }

    } else {
        // --- EXITING BREAK MODE UI (returning to main screen) ---
        if (helloMessageContainer) helloMessageContainer.style.display = 'flex'; 
        if (timerDisplayContainer) timerDisplayContainer.style.display = 'none'; 
        // Main work buttons (startButton, breakButton, stopButton) visibility will be handled by ajaxcall()
        if (mainStartWorkButton) mainStartWorkButton.style.display = 'flex'; // Temporary, ajaxcall will refine
        if (mainBreakButton) mainBreakButton.style.display = 'flex';   // Temporary, ajaxcall will refine
        if (breakControlsContainer) breakControlsContainer.style.display = 'none'; 
        
        if (breakTimerInterval) clearInterval(breakTimerInterval); // Stop JS visual interval when exiting break screen
        // DO NOT reset breakCurrentTimeInSeconds, clientSideCalculatedAccumulatedSeconds, serverBreakStartTime, or isBreakTimerRunning (logical state) here.
        // These variables hold the actual state of the break.
        
        if (typeof ajaxcall === 'function') {
           ajaxcall(); // Refresh main button states and potentially re-sync break info
        } else {
           console.error("ajaxcall function not found. Main button states may be incorrect.");
           if (mainStopWorkButton && mainStartWorkButton && mainStartWorkButton.style.display !== 'none') {
               mainStopWorkButton.style.display = 'none';
           }
        }
    }
}

// Timer logic functions
function startBreakTimer() { 
    // isBreakTimerRunning (logical state) should be true if this is called.
    // This function handles the visual interval timer.
    const playButton = document.getElementById('playButton');
    const pauseButton = document.getElementById('pauseButton');
    const timerDisplayElement = document.getElementById('timerDisplay');

    if (playButton) playButton.disabled = true;
    if (pauseButton) pauseButton.disabled = false;

    // Update display immediately with the current state before interval starts.
    // breakCurrentTimeInSeconds should have been updated by ajaxcall or button handlers.
    if (timerDisplayElement) {
        timerDisplayElement.textContent = formatTime(Math.max(0, Math.round(breakCurrentTimeInSeconds)));
    }

    if (breakTimerInterval) clearInterval(breakTimerInterval); // Clear any existing interval

    breakTimerInterval = setInterval(() => {
        let currentDisplayTime;
        if (clientBreakSegmentStartEpoch) { // Actively timing a segment
             const elapsedThisSegment = (Date.now() - clientBreakSegmentStartEpoch) / 1000; // in seconds
             currentDisplayTime = clientSideCalculatedAccumulatedSeconds + elapsedThisSegment;
        } else { // Should not happen if startBreakTimer is called when logically running, but as a fallback:
             currentDisplayTime = clientSideCalculatedAccumulatedSeconds; // Or breakCurrentTimeInSeconds
        }
        breakCurrentTimeInSeconds = currentDisplayTime; // Update global for display
        
        if (timerDisplayElement) {
            timerDisplayElement.textContent = formatTime(Math.max(0, Math.round(breakCurrentTimeInSeconds)));
        }
    }, 1000);
}

function pauseBreakTimer() {
    // This function will be modified later by step 6.
    // For now, keep the old logic or a placeholder if it was removed.
    // The existing pauseBreakTimer logic from previous task was:
    if (!isBreakTimerRunning && !clientBreakSegmentStartEpoch) { // Check if it's actually running or if a segment has started
        if(document.getElementById('playButton')) document.getElementById('playButton').disabled = false;
        if(document.getElementById('pauseButton')) document.getElementById('pauseButton').disabled = true; 
        return;
    }

    isBreakTimerRunning = false; // This refers to the JS interval; logical state handled by API response.
    if(document.getElementById('playButton')) document.getElementById('playButton').disabled = false;
    if(document.getElementById('pauseButton')) document.getElementById('pauseButton').disabled = true; 

    clearInterval(breakTimerInterval);
    // The rest of pause logic (API call, updating accumulated seconds) will be in the event handler.
}
