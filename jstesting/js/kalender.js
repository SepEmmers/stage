//====================Kalender========================
// Dit is een kalender die de dagen en werkuren van de werknemers weergeeft.
// Ik heb kalender gemaakt omdat dit makelijker respnsive is dan een tabel.

let vandaag = new Date();
let dag = vandaag.getDate();
// Maand is 0-11, dus voeg 1 toe
// Zondder dit zou de maand januari 0 zijn, februari 1, etc.
let maand = vandaag.getMonth();
let jaar = vandaag.getFullYear();

//display van de maanden en jaren
function displayMaandJaar() {
  document.getElementById("displayYear").innerHTML = jaar;
  let maandNamen = [
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
  let maandNaam = maandNamen[maand];
  document.getElementById("maandDisplay").innerHTML = maandNaam;
}

displayMaandJaar(); // Altijd 1 keer genereren display

// Functie om het aantal dagen in een maand te bepalen
// https://stackoverflow.com/questions/1184334/get-number-days-in-a-specified-month-using-javascript
function getDaysInMonth(year, month) {
    return new Date(year, month + 1, 0).getDate();
}


//========================------==============================
// Functie om de kalender te genereren
function genereerKalender() {

    let dagenInMaanden = getDaysInMonth(jaar, maand);

    // We moeten nu de eerste dag van de maand bepalen
    // Dit is nodig om de juiste opmaak van de kalender te krijgen

    // Dit geeft de eerste dag van de maand terug (0-6, 0 is zondag, 1 is maandag, etc.)
    // We doen maand - 1 omdat de maand in JavaScript 0-indexed is
    // De .getDay()-methode van het Date-object retourneert een getal dat de dag van de week vertegenwoordigt.
    // 0 is zondag, 1 is maandag, enzovoort.
    // Het resultaat is dus een getal dat de eerste dag van de maand vertegenwoordigt,
    let eersteDag = new Date(jaar, maand, 1).getDay(); // 0 is zondag, 1 is maandag
    console.log(eersteDag); // Dit is de eerste dag van de maand (0-6)

    //----------------------------------------------------
    // We hebben nu de eerste dag van de maand en het aantal dagen in de maand
    // We hallen een id op van de html pagina en maken een dag teller aan
    // We gaan nu de kalender opbouwen met een for loop
    let showKalender = document.getElementById("showKalender");
    let html =
    '<div class="flex flex-row divide-x-[1px] divide-kalender-day border-b-[1px] border-kalender-day">';
    let dagTeller = 0; // Houd de dag van de week bij om de rijen te sluiten

    // Aanpassing voor maandag als eerste dag
  if (eersteDag === 0) {
    eersteDag = 7; // Zondag wordt 7 om het na zaterdag te plaatsen
  }

    // Voeg lege cellen toe voor de eerste week
    for (let i = 1; i < eersteDag; i++) {
        html += `<button class="flex justify-center items-center p-kalender py-[10px] md:py-2 bg-kalender-legencellen"><p></p></button>`; // Lege cel
        dagTeller++;
    }

    // Voeg de dagen toe aan de kalender
    for (let i = 1; i <= dagenInMaanden; i++) {
        // Gebruik een closure om de juiste waarde van 'i' vast te leggen
        (function (dagNummer) {
            html += `<button onclick="alertDag(${dagNummer})" class="flex justify-center items-center p-kalender py-[10px] md:py-2"><p>${dagNummer}</p></button>`;
        })(i);
    
        // telt de dagen op
        dagTeller++;

        if (dagTeller === 7) { // Sluit de rij af op zondag
            html += "</div>";
            if (i < dagenInMaanden) { // Begin een nieuwe rij, tenzij het de laatste dag is
                html +=
            '<div class="flex flex-row divide-x-[1px] divide-kalender-day border-b-[1px] border-kalender-day">';
        }
        dagTeller = 0;  // Reset de teller
        }
    }

    // Voeg lege cellen toe voor de laatste week
    // Dit is nodig als de maand niet op zondag eindigt
    while (dagTeller < 7 && dagTeller > 0) {
        html += `<button class="flex justify-center items-center p-kalender py-[10px] md:py-2 bg-kalender-legencellen"><p></p></button>`; // Lege cel
        dagTeller++;
    }

    html += "</div>";
    showKalender.innerHTML = html;
}



genereerKalender(); // 1 keer kalender genereren kalender generatie



//========================------==============================
// Functie om de alert weer te geven
// Dit is een alert die de datum weergeeft van de dag die je hebt aangeklikt
// Dit is een custom alert die ik heb gemaakt omdat de standaard alert niet mooi is
function alertDag(dagNummer) {

    // Dit maakt een new date aan voor te laten zien welke dag het is
    // We doen dit met de globale variabelen jaar, maand
    let datum = new Date(jaar, maand, dagNummer);
    let datumDagNaam = datum.getDay();
    let datumDag = datum.getDate();
    let datumMaand = datum.getMonth();
    let datumJaar = datum.getFullYear();
    let dagNamen = [
        "Zondag",
        "Maandag",
        "Dinsdag",
        "Woensdag",
        "Donderdag",
        "Vrijdag",
        "Zaterdag",
    ];
    let dagNaam = dagNamen[datumDagNaam];

    // Dit is de alert die we gaan weergeven
    let customAlert = document.getElementById("customAlert");

    // Maak div voor achtergrond van de alert
    let html ='<div class="fixed top-0 left-0 w-full h-full bg-black/50 flex items-center justify-center z-10">';
    // div met de alert zelf
    html +='<div class="bg-white w-full max-w-[500px] min-h-[200px] rounded-xl flex flex-col items-center justify-center gap-4">';
    // Dit is de tekst die we weergeven in de alert
    html += `<h2 class="text-center">${dagNaam}, ${datumDag}/${datumMaand + 1}/${datumJaar} </h2>`;
    html += '<button onclick="sluitAlert()" class="text-center">OK</button>';
    // Sluit de divs af
    html += "</div>";
    html += "</div>";
    customAlert.innerHTML = html;
}

// Functie om de alert te sluiten
// Dit sluit de alert als je op de knop drukt
function sluitAlert() {
document.getElementById("customAlert").innerHTML = "";
}


//========================------==============================
// Functies voor de pijltjes
// Dit zijn de functies voor de pijltjes die de maand veranderen
function vorigeMaand() {
    maand--;
    // Als de maand kleiner is dan 0, gaan we naar de vorige jaar en zetten de maand op 11 (december)
    if (maand < 0) {
        maand = 11;
        jaar--;
    }
    displayMaandJaar();
    genereerKalender();
}

function volgendeMaand() {
    maand++;
    // Als de maand groter is dan 11, gaan we naar de volgende jaar en zetten de maand op 0 (januari)
    if (maand > 11) {
        maand = 0;
        jaar++;
    }
    displayMaandJaar();
    genereerKalender();
}


//========================------==============================
// Functie voor selecteer maand
// Dit is een functie die de maand selecteert als je op de knop drukt


