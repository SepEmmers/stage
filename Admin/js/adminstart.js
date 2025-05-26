let allHistoryData = []; // Globale variabele om alle opgehaalde data op te slaan

//==================picker module===================//

//===============---date picker module---===============//
let datePickerModule; // Declare outside
let filterDatumModule; // Declare outside (gebruiken we voor de listener)

function showDatePicker() {
    if (datePickerModule) {
        datePickerModule.classList.remove('hidden');
    }
}

function hideDatePicker() {
    if (datePickerModule) {
        datePickerModule.classList.add('hidden');
    }
}

//===============---Name picker module---===============//
let namePickerModule; // Declare outside
let filterNaamModule; // Declare outside

function showNamePicker() {
    if (filterNaamModule) {
        filterNaamModule.innerHTML = '<option value="">Alle</option>'; // Reset de opties
        if (allHistoryData) {
            populateNameFilterModule(allHistoryData);
        }
        // Reset de verborgen filter waarde ook voor de zekerheid
        const filterNaamHidden = document.getElementById("filterNaam");
        if (filterNaamHidden) {
             filterNaamHidden.value = "";
        }
    }
    if (namePickerModule) {
        namePickerModule.classList.remove('hidden');
    }
}

function hideNamePicker() {
    if (namePickerModule) {
        namePickerModule.classList.add('hidden');
    }
}

function populateNameFilterModule(data) {
    const namen = [...new Set(data.map(record => record.VolledigeNaam))]; // Haal unieke namen op
    namen.sort((a, b) => a.localeCompare(b)); // Sorteer de namen alfabetisch
    if (filterNaamModule) {
        // Leegmaken behalve de 'Alle' optie
        while (filterNaamModule.options.length > 1) {
            filterNaamModule.remove(1);
        }
        // Voeg nieuwe namen toe
        namen.forEach(naam => {
            const option = document.createElement("option");
            option.value = naam;
            option.textContent = naam;
            filterNaamModule.appendChild(option);
        });
    }
}

//===============---sorting filter module---===============//
let sortingFilterModule; // Declare outside
let sortByModule; // Declare outside (gebruiken we voor de listener)

function showSortingFilter() {
     // Reset de verborgen sort waarde ook voor de zekerheid
     const sortByHidden = document.getElementById("sortBy");
         if (sortByHidden) {
             sortByHidden.value = "";
         }
    if (sortingFilterModule) {
        sortingFilterModule.classList.remove('hidden');
    }
}

function hideSortingFilter() {
    if (sortingFilterModule) {
        sortingFilterModule.classList.add('hidden');
    }
}

function ajaxcall() {
    console.log("ajaxcall functie (start info) wordt uitgevoerd.");
    // const startButton = document.getElementById("startButton"); // Deze lijken niet gebruikt te worden
    // const stopButton = document.getElementById("stopButton");
    $.ajax({
        url: "APA.php",
        type: "POST",
        data: {
            get_start_info: true,
        },
        // Voeg eventueel success/error handlers toe als je iets met de response wilt doen
    });
}

function refreshPage(){
    window.location.reload();
}

function showWorkHistory() {
    console.log("showWorkHistory functie wordt uitgevoerd.");
    const currentDate = new Date();
    // Gebruik de huidige datum/tijd van de browser, PHP kant gebruikt server tijd
    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth() + 1;
    console.log(`Bezig met ophalen van werkgeschiedenis voor jaar: ${currentYear}, maand: ${currentMonth}`);

    $.ajax({
        url: "APA.php",
        type: "POST",
        data: {
            get_monthly_history_admin: true,
            year: currentYear,
            month: currentMonth,
        },
        success: function (data) {
            console.log("Volledige response van server:", data); // Log de volledige response
            try {
                const response = JSON.parse(data);
                console.log("Geparsede response:", response);
                if (response.success && response.data) { // Check of data bestaat
                    console.log("Geschiedenis data ontvangen (response.data):", response.data);
                    allHistoryData = response.data; // Sla alle data op
                    // Roep filter direct aan om initiele data te tonen (en eventueel te sorteren)
                    filterWorkHistory();
                    // Namen worden nu gevuld wanneer de name picker wordt geopend
                } else {
                    console.error("Fout bij het ophalen van de geschiedenis:", response.error || "Geen data ontvangen");
                    allHistoryData = []; // Zet data op leeg array bij fout
                    filterWorkHistory(); // Toon lege staat of foutmelding
                    const historyDisplay = document.getElementById("historyDisplay");
                    if (historyDisplay) {
                        historyDisplay.innerHTML = `<p>Fout bij ophalen data: ${response.error || 'Geen data gevonden.'}</p>`;
                    }
                }
            } catch (e) {
                console.error("Fout bij het verwerken van de response:", e);
                allHistoryData = []; // Zet data op leeg array bij fout
                filterWorkHistory(); // Toon lege staat of foutmelding
                const historyDisplay = document.getElementById("historyDisplay");
                if (historyDisplay) {
                    historyDisplay.innerHTML = `<p>Fout bij verwerken data.</p>`;
                }
            }
        },
        error: function (xhr, status, error) {
            console.error("Fout bij het ophalen van de geschiedenis:", status, error);
            allHistoryData = []; // Zet data op leeg array bij fout
            filterWorkHistory(); // Toon lege staat of foutmelding
            const historyDisplay = document.getElementById("historyDisplay");
            if (historyDisplay) {
                historyDisplay.innerHTML = `<p>Serverfout bij ophalen data.</p>`;
            }
        },
    });
}


function filterWorkHistory() {
    console.log("filterWorkHistory wordt uitgevoerd");
    // Haal waarden op uit de VERBORGEN elementen die we bijwerken vanuit de modules
    const selectedNaam = document.getElementById("filterNaam") ? document.getElementById("filterNaam").value : '';
    const selectedDatum = document.getElementById("filterDatum") ? document.getElementById("filterDatum").value : ''; // Dit is de input in de date picker module

    console.log("Filteren op Naam:", selectedNaam);
    console.log("Filteren op Datum:", selectedDatum);

    let filteredData = [...allHistoryData]; // Start met alle data

    if (selectedNaam) {
        filteredData = filteredData.filter(record => record.VolledigeNaam === selectedNaam);
    }

    if (selectedDatum) {
        // Filter op basis van het *correct geparste* datumdeel
        filteredData = filteredData.filter(record => {
            if (!record.starttijd) return false;
            const partsStart = record.starttijd.split(" ");
            if (partsStart.length >= 3) {
                 // Vergelijk de inputYYYY-MM-DD met de tweede datum string uit de data
                return partsStart[1] === selectedDatum;
            }
            return false; // Ongeldig formaat, niet filteren
        });
    }

    console.log("Aantal records na filteren:", filteredData.length);
    sortWorkHistory(filteredData); // Sorteer de gefilterde data
}

function sortWorkHistory(dataToSort) {
    console.log("sortWorkHistory wordt uitgevoerd");
    // Haal waarde op uit het VERBORGEN element
    const sortByValue = document.getElementById("sortBy") ? document.getElementById("sortBy").value : '';
    console.log("Sorteren op:", sortByValue);

    let sortedData = [...dataToSort]; // Maak een kopie

    // Hulpfunctie om datums veilig te parsen vanuit het (mogelijk incorrecte) string formaat
    function parseSafeDate(dateString) {
        if (!dateString) return null;
        const parts = dateString.split(" ");
        if (parts.length >= 3) {
            const validDateString = `${parts[1]}T${parts[2]}`; // Gebruik YYYY-MM-DDTHH:MM:SS
            const dt = new Date(validDateString);
            return isNaN(dt) ? null : dt; // Geef null terug bij ongeldige datum
        }
        return null; // Geef null terug bij onverwacht formaat
    }

    // Hulpfunctie om tijd (in uren) veilig te parsen uit HH:MM formaat
     function parseSafeTime(timeString) {
        if (!timeString || typeof timeString !== 'string') return 0;
        const parts = timeString.split(':');
        if (parts.length === 2) {
            const hours = parseFloat(parts[0]);
            const minutes = parseFloat(parts[1]) / 60; // Minuten omzetten naar fractie van uren
            return hours + minutes;
        }
        return 0; // Ongeldig formaat
    }


    // Alleen sorteren als er een sorteeroptie is geselecteerd
    if (sortByValue) {
        sortedData.sort((a, b) => {
            switch (sortByValue) {
                case "naam_asc":
                    // Gebruik localeCompare voor correcte alfabetische sortering
                    return (a.VolledigeNaam || "").localeCompare(b.VolledigeNaam || "");
                case "naam_desc":
                    return (b.VolledigeNaam || "").localeCompare(a.VolledigeNaam || "");
                case "datum_asc":
                    const dateA_asc = parseSafeDate(a.starttijd);
                    const dateB_asc = parseSafeDate(b.starttijd);
                    // Plaats null (ongeldige datums) achteraan
                    if (dateA_asc === null) return 1;
                    if (dateB_asc === null) return -1;
                    return dateA_asc - dateB_asc;
                case "datum_desc":
                    const dateA_desc = parseSafeDate(a.starttijd);
                    const dateB_desc = parseSafeDate(b.starttijd);
                    // Plaats null (ongeldige datums) achteraan
                    if (dateA_desc === null) return 1;
                    if (dateB_desc === null) return -1;
                    return dateB_desc - dateA_desc;
                case "uren_asc":
                    // Gebruik totaal_tijd uit DB indien beschikbaar en geldig
                    const urenA_asc = parseSafeTime(a.totaal_tijd);
                    const urenB_asc = parseSafeTime(b.totaal_tijd);
                    return urenA_asc - urenB_asc;
                case "uren_desc":
                     const urenA_desc = parseSafeTime(a.totaal_tijd);
                     const urenB_desc = parseSafeTime(b.totaal_tijd);
                    return urenB_desc - urenA_desc;
                default:
                    return 0; // Geen sortering als waarde onbekend is
            }
        });
    } else {
         // Standaard sorteren op datum (nieuw -> oud) als geen sorteeroptie is gekozen
         sortedData.sort((a, b) => {
            const defaultDateA = parseSafeDate(a.starttijd);
            const defaultDateB = parseSafeDate(b.starttijd);
            if (defaultDateA === null) return 1; // null achteraan
            if (defaultDateB === null) return -1; // null achteraan
            return defaultDateB - defaultDateA; // Nieuwste eerst
        });
    }

    displayWorkHistory(sortedData); // Toon de gesorteerde data
}


function displayWorkHistory(historyData) {
    const historyDisplay = document.getElementById("historyDisplay");
    if (!historyDisplay) {
        console.error("Element 'historyDisplay' niet gevonden!");
        return;
    }

    let historyHTML = ""; // Start met een lege string
    console.log("Data voor weergave:", historyData);

    if (historyData && historyData.length > 0) {
        historyData.forEach((record, index) => {
            const bgColor = index % 2 === 0 ? 'bg-white' : 'bg-achtergrond2';

            let formattedDate = "Ongeldige datum";
            let startTime = "Ongeldige tijd";
            let endTime = "Nog niet gestopt"; // Default voor eindtijd
            // let totalTimeDisplay = record.totaal_tijd || "N/A"; // Totaaltijd niet meer nodig voor weergave hier

            try {
                // --- Starttijd verwerking ---
                if (record.starttijd) {
                    const partsStart = record.starttijd.split(" ");
                    if (partsStart.length >= 3) {
                        const validStartString = `${partsStart[1]}T${partsStart[2]}`; // Gebruik T separator
                        const startDate = new Date(validStartString);
                        if (!isNaN(startDate)) {
                            const dayOfMonth = startDate.getDate().toString().padStart(2, "0");
                            const monthOfYear = (startDate.getMonth() + 1).toString().padStart(2, "0");
                            const year = startDate.getFullYear();
                            formattedDate = `${dayOfMonth}/${monthOfYear}/${year}`;

                            startTime = `${startDate.getHours().toString().padStart(2, "0")}:${startDate.getMinutes().toString().padStart(2, "0")}`;
                        } else {
                            console.warn("Kon starttijd niet parsen na opschonen:", record.starttijd, "->", validStartString);
                        }
                    } else {
                        console.warn("Onverwacht starttijd formaat:", record.starttijd);
                    }
                }

                // --- Eindtijd verwerking ---
                if (record.eindtijd) {
                    const partsEnd = record.eindtijd.split(" ");
                    if (partsEnd.length >= 3) {
                        const validEndString = `${partsEnd[1]}T${partsEnd[2]}`; // Gebruik T separator
                        const endDate = new Date(validEndString);
                        if (!isNaN(endDate)) {
                            endTime = `${endDate.getHours().toString().padStart(2, "0")}:${endDate.getMinutes().toString().padStart(2, "0")}`;
                        } else {
                            console.warn("Kon eindtijd niet parsen na opschonen:", record.eindtijd, "->", validEndString);
                            endTime = "Ongeldige eindtijd";
                        }
                    } else {
                        console.warn("Onverwacht eindtijd formaat:", record.eindtijd);
                        endTime = "Ongeldige eindtijd";
                    }
                }
                // Als er geen record.eindtijd is, blijft endTime "Nog niet gestopt"
            } catch (e) {
                console.error("Fout bij parsen datum/tijd voor record:", record, e);
                // Laat de default 'ongeldig' waarden staan
            }

            // Bouw HTML rij
            historyHTML += `<div class="flex flex-row w-full min-h-[62px] divide-x-[1.5px] border-b-[1.5px] divide-rooster border-rooster ${bgColor}">`;
            historyHTML += `<a href="werkwijzigen.php?id=${record.pk_personeel}&pk=${record.pk_werkuren}&school_pk=${record.school_fk}&job_pk=${record.job_fk}" class="flex justify-center items-center w-[7%]"><img src="media library/Bewerken.webp" alt="Bewerken" class="w-[30px] h-[30px]"></a>`;
            historyHTML += `<div class="flex items-center justify-center w-[18%] px-1 text-center"><p>${formattedDate}</p></div>`;
            historyHTML += `<div class="flex items-center justify-center w-[35%] px-1 text-center"><p>${record.VolledigeNaam || 'Onbekend'}</p></div>`;
            historyHTML += `<div class="flex items-center justify-center w-[20%] px-1 text-center"><p>${startTime}</p></div>`;
            historyHTML += `<div class="flex items-center justify-center w-[20%] px-1 text-center"><p>${endTime}</p></div>`; // Laatste kolom is eindtijd
            historyHTML += `</div>`;
        });
    } else {
        // Toon een duidelijke melding als er geen data is (kan na filteren zijn)
        const message = allHistoryData.length === 0 ? "Geen werkuren gevonden voor deze periode." : "Geen werkuren voldoen aan de filtercriteria.";
        historyHTML = `<div class="flex justify-center items-center w-full min-h-[62px] border-b-[1.5px] border-rooster bg-white"><p>${message}</p></div>`;
    }

    historyDisplay.innerHTML = historyHTML;
}


// Functie om een werkuur te verwijderen (lijkt correct)
function deleteWorkHour(werkurenId) {
    console.log("deleteWorkHour functie wordt uitgevoerd voor ID:", werkurenId);
    if (!confirm("Bent u zeker dat u dit werkuur wilt verwijderen?")) {
         return; // Stop als de gebruiker annuleert
    }
    $.ajax({
        url: "APA.php",
        type: "POST",
        data: {
            delete_work_hour: true,
            werkuren_id: werkurenId,
        },
        success: function (response) {
            console.log("Response van verwijderactie:", response);
            try {
                const parsedResponse = JSON.parse(response);
                if (parsedResponse.success) {
                    alert("Werkuur succesvol verwijderd.");
                    showWorkHistory(); // Herlaad de lijst
                } else {
                    alert("Fout bij het verwijderen van het werkuur: " + (parsedResponse.error || 'Onbekende fout'));
                }
            } catch (e) {
                console.error("Fout bij het verwerken van de response van de verwijderactie:", e);
                alert("Er is een onverwachte fout opgetreden bij het verwijderen.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Fout bij de AJAX call om te verwijderen:", status, error);
            alert("Er is een fout opgetreden bij het communiceren met de server.");
        },
    });
}


// Functie om personeelsdata te laden (lijkt correct voor de personen.html pagina)
function loadPersoneelData() {
    console.log("loadPersoneelData functie wordt uitgevoerd.");
    $.ajax({
        url: "APA.php",
        type: "POST",
        dataType: "json", // Zorg dat jQuery de JSON direct parset
        data: {
            get_personeel_data: true
        },
        success: function(response) {
            console.log("Personeelsdata ontvangen:", response);
            const personeelContainer = document.getElementById("personeelOverzichtContainer");
            if (personeelContainer) {
                let personeelHTML = `<tr>`;
                if (response.success && response.data && response.data.length > 0) {
                    response.data.forEach(persoon => {
                        personeelHTML += `<div class="flex flex-row w-full min-h-[62px] divide-x-[1.5px] border-b-[1.5px] divide-rooster border-rooster bg-white">`; // Zorg dat deze class bestaat in je CSS
                        personeelHTML += `<a href="persoonwijzigen.php?id=${persoon.pk_personeel}" class="flex justify-center items-center w-[7%]"><img src="media library/Bewerken.webp" alt="Bewerken" class="w-[30px] h-[30px]"></a>`;
                        personeelHTML += `<div class="flex items-center justify-center w-[35%]"><p>${persoon.VolledigeNaam}</p></div>`;
                        personeelHTML += `<div class="flex items-center justify-center w-[25%]"><p>${persoon.gebruikersnaam}</p></div>`;
                        personeelHTML += `<div class="flex items-center justify-center w-[32%]"><p>${persoon.Email}</p></div>`;
                        personeelHTML += `</div>`;
                    });
                } else if (response.success) {
                    personeelHTML += "<p>Geen personeelsleden gevonden.</p>";
                } else {
                    personeelHTML += `<p>Fout bij het ophalen van personeelsdata: ${response.error || 'Onbekende fout'}</p>`;
                }
                personeelContainer.innerHTML = personeelHTML;

                // Event listener specifiek voor de personeel toevoegen knop
                const addPersonButton = document.getElementById("addPersonBtn");
                if (addPersonButton) {
                    addPersonButton.addEventListener("click", function() {
                        window.location.href = "werknemer_toevoegen.php"; // Link naar de juiste pagina
                    });
                }

            } else {
                console.error("De container 'personeelOverzichtContainer' is niet gevonden in de HTML.");
            }
        },
        error: function(xhr, status, error) {
            console.error("Fout bij het ophalen van personeelsdata via AJAX:", status, error);
            const personeelContainer = document.getElementById("personeelOverzichtContainer");
            if (personeelContainer) {
                personeelContainer.innerHTML = "<p>Er is een fout opgetreden bij het ophalen van de personeelsdata.</p>";
            }
        }
    });
}


function addNewWorkHours(event) {
    event.preventDefault(); // Voorkom het standaardgedrag van het formulier (pagina reload)

    const persoonId = document.getElementById('naam').value; // Gebruik de value van de <select>
    // Haal de datum op uit het element met id 'datum'
    const datumInput = document.getElementById('datum').value;
    const datumDelen = datumInput.split('-');
    const datum = datumDelen[1] + '/' + datumDelen[0] + '/' + datumDelen[2];
    const begintijdValue = document.getElementById('begintijd').value;
    const eindtijdValue = document.getElementById('eindtijd').value;
    // Combineer de datum met de begintijd
    const startTijd = (`${datum} ${begintijdValue}`);
    
    // Combineer de datum met de eindtijd
    const eindTijd = (`${datum} ${eindtijdValue}`);
    const school = document.getElementById('school').value;
    const Job = document.getElementById('Job').value;

    $.ajax({
        url: "APA.php",
        type: "POST",
        data: {
            add_work_hours: true,
            persoon_id: persoonId,
            datum: datum,
            start_tijd: startTijd,
            eind_tijd: eindTijd,
            school_id: school,
            job_id: Job
        },
        success: function (response) {
            console.log("Response van toevoegen werkuren:", response);
            try {
                const parsedResponse = JSON.parse(response);
                if (parsedResponse.success) {
                    alert(parsedResponse.message);
                    // Optioneel: herlaad de werkgeschiedenis om de nieuwe uren te zien
                    // Als je op een andere pagina bent (werktoevoegen.php), wil je misschien terugnavigeren
                    window.location.href = 'admin.html'; // Of de pagina waar je de werkgeschiedenis toont
                } else {
                    alert("Fout bij het toevoegen van werkuren: " + parsedResponse.error);
                }
            } catch (e) {
                console.error("Fout bij het verwerken van de response:", e);
                alert("Er is een onverwachte fout opgetreden.");
            }
        },
        error: function (xhr, status, error) {
            console.error("Fout bij de AJAX call:", status, error);
            alert("Er is een fout opgetreden bij het communiceren met de server.");
        }
    });
}


// === Document Ready ===
document.addEventListener("DOMContentLoaded", function () {
    ajaxcall(); // Initial call

    // --- Haal elementen op (zowel verborgen als module-specifiek) ---
    const filterNaamHidden = document.getElementById("filterNaam"); // Verborgen select
    const sortByHidden = document.getElementById("sortBy");          // Verborgen select

    // --- Module elementen ---
    datePickerModule = document.getElementById('date-picker-module');
    namePickerModule = document.getElementById('name-picker-module');
    sortingFilterModule = document.getElementById('sorting-filter-module');

    // --- Module controls ---
    filterDatumModule = document.getElementById('filterDatum'); // Date input in date module
    filterNaamModule = document.getElementById('filterNaamModule'); // Select in name module
    sortByModule = document.getElementById('sortByModule');          // Select in sort module

    // --- Event Listeners ---

    // 1. Date Picker Module
    if (filterDatumModule && datePickerModule) {
        filterDatumModule.addEventListener("change", function() {
            console.log("Datum geselecteerd:", this.value);
            filterWorkHistory(); // Pas filter toe
            hideDatePicker();     // Verberg module
        });
        datePickerModule.addEventListener('click', function(event) {
            if (event.target === datePickerModule) { // Klik op de overlay zelf
                hideDatePicker();
            }
        });
    } else {
        console.error("Date picker elementen niet gevonden (filterDatum of date-picker-module)");
    }

    // --- DEBUGGING: Controleer of name picker elementen bestaan ---
    console.log("DEBUG: Zoeken naar Name Picker Elementen:");
    console.log(" - filterNaamModule:", filterNaamModule ? 'Gevonden' : 'NIET GEVONDEN');
    console.log(" - filterNaamHidden:", filterNaamHidden ? 'Gevonden' : 'NIET GEVONDEN');
    console.log(" - namePickerModule:", namePickerModule ? 'Gevonden' : 'NIET GEVONDEN');
    // --- Einde DEBUGGING ---

    // 2. Name Picker Module
    if (filterNaamModule && filterNaamHidden && namePickerModule) {
        filterNaamModule.addEventListener("change", function() {
            console.log("Naam geselecteerd:", this.value);
            filterNaamHidden.value = this.value; // Update verborgen select
            filterWorkHistory(); // Pas filter toe
            hideNamePicker();     // Verberg module
        });
        namePickerModule.addEventListener('click', function(event) {
            if (event.target === namePickerModule) { // Klik op de overlay zelf
                hideNamePicker();
            }
        });
    } else {
        console.error("Name picker elementen niet gevonden (Zie DEBUG logs hierboven). Event listener niet toegevoegd.");
    }

    // 3. Sorting Filter Module
    if (sortByModule && sortByHidden && sortingFilterModule) {
        sortByModule.addEventListener("change", function() {
            console.log("Sorteeroptie geselecteerd:", this.value);
            sortByHidden.value = this.value; // Update verborgen select
            filterWorkHistory(); // Herfilter en sorteer opnieuw
            hideSortingFilter(); // Verberg module
        });
        sortingFilterModule.addEventListener('click', function(event) {
            if (event.target === sortingFilterModule) { // Klik op de overlay zelf
                hideSortingFilter();
            }
        });
    } else {
        console.error("Sorting filter elementen niet gevonden (sortByModule, sortBy, of sorting-filter-module)");
    }

    // --- Initieel laden van data ---
    if (document.getElementById("historyDisplay")) {
        showWorkHistory(); // Laad en toon werkgeschiedenis initieel
    }

    if (document.getElementById("personeelOverzichtContainer")) {
        loadPersoneelData(); // Laad personeelsdata als de container bestaat
    }

    // --- Event listener voor het toevoegen van werkuren ---
    const addWorkHoursForm = document.querySelector('form'); // Selecteer het formulier op de werktoevoegen.php pagina
    if (addWorkHoursForm) {
        addWorkHoursForm.addEventListener('submit', addNewWorkHours);
    }
});