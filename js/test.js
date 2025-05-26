function ajaxcall() {
  $.ajax({
    url: "API.php",
    type: "POST",
    data: {
      get_registraties: true,
    },
    success: function (data) {
      console.log(data);
      let registraties = JSON.parse(data);

      console.log(registraties + "1");
      const container = document.getElementById("registratiesContainer"); // Haal de container op
      console.log(registraties);

      for (let i = 0; i < registraties.length; i++) {
        console.log(registraties.length);
        console.log(registraties + "3");

        const reg = registraties[i];
        console.log(reg);
        const div = document.createElement("div"); // Creëer een nieuw div-element
        div.classList.add(
          "flex",
          "flex-row",
          "border-solid",
          "border-1",
          "divide-x-1"
        ); // Voeg klassen toe

        const naamP = document.createElement("p"); // Creëer een nieuwe p-element voor de naam
        naamP.classList.add("bg-neutral-200", "px-12", "py-2");
        naamP.textContent = reg.VolledigeNaam; // Stel de tekst in

        const starttijdP = document.createElement("p"); // Creëer een nieuwe p-element voor de starttijd
        starttijdP.classList.add("bg-neutral-200", "px-12", "py-2");
        starttijdP.textContent = reg.Starttijd; // Stel de tekst in

        const eindtijdP = document.createElement("p"); // Creëer een nieuwe p-element voor de eindtijd
        eindtijdP.classList.add("bg-neutral-200", "px-12", "py-2");
        eindtijdP.textContent = reg.Eindtijd; // Stel de tekst in

        div.appendChild(naamP); // Voeg de naam-p toe aan de div
        div.appendChild(starttijdP); // Voeg de starttijd-p toe aan de div
        div.appendChild(eindtijdP); // Voeg de eindtijd-p toe aan de div

        container.appendChild(div); // Voeg de nieuwe div toe aan de container
      }
    },
  });
}

document.addEventListener("DOMContentLoaded", function () {
  ajaxcall();
});

function herlaad() {
  location.reload();
}
