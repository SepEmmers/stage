// Wanneer de pagina geladen is, voer de functie Time() en dag() uit
// en voer deze elke seconde uit
//Dit doen we omdat de eerste keer als de functie geladen wordt er een delay is, dit heeft mogelijk te maken met tailwindcss
document.addEventListener('DOMContentLoaded', function() {
  Time();
  // Update elke seconde de tijd
  setInterval(Time, 1000);

  Day();
});





//-------------------functie voor de tijd-------------------//
// Deze functie zorgt ervoor dat de tijd wordt weergegeven op de pagina's
// met 2 cijfers voor de uren en minuten
// De tijd wordt elke seconde geupdate


function Time() {

  // Verkrijg de huidige tijd en formatteer deze naar een string
  // met 2 cijfers voor de uren en minuten
  let now = new Date();
  let hours = now.getHours().toString().padStart(2, '0');
  let minutes = now.getMinutes().toString().padStart(2, '0');
  let seconds = now.getSeconds().toString().padStart(2, '0');
  
  
  // Maak variabelen voor de uren en minuten
  let time = `${hours}:${minutes}:${seconds}`;

  // id zoeken in de html
  let timeDisplay = document.getElementById('timeDisplay');

  // Kijken of het element bestaat anders een error geven
  if (timeDisplay) {
    timeDisplay.textContent = time;
  } else {
    console.error("Element with id 'timeDisplay' not found.");
  }
}




//-------------------functie voor de dag-------------------//
// Deze functie zorgt ervoor dat de dag wordt weergegeven op de pagina's
// met de dag, datum en maand


function Day() {

  // Maken van een nieuwe datum
  let today = new Date();
  
  // id zoeken in de html
  let dagdisplay = document.getElementById('dagdisplay');

  // Kijken of het element bestaat anders een error geven
  if (dagdisplay) { 
    //Formatteer de datum naar een string met de dag, datum en maand
    weekday = today.toLocaleDateString('nl-Nl', {weekday: 'long'});
    dayMonth = today.toLocaleDateString('nl-Nl', {day: 'numeric', month: 'long'});
    dagdisplay.textContent = `${weekday}, ${dayMonth}`;
  } else {
    console.error("Element with id 'dagdisplay' not found.");
  }

}