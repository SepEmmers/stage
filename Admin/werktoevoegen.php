<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Werkuren toevoegn</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="css/tailwind.css">
    <link rel="stylesheet" href="css/font.css">
    <script src="js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/adminstart.js"></script>

    <style type="text/tailwindcss">
        @theme {
            /* basis kleuren */
            --color-default-grey: oklch(88.53% 0 0);
            --color-spc-green: oklch(72.13% 0.1721 126.07);
            /* background page */
            --color-background: oklch(95.81% 0 0);
            /* text */
            --color-text-white: #fff;
            /* button toevoegen/aanpassen/verwijderen */
            --color-toevoegenAanpassen: oklch(84.26% 0.2256 129.65);
            --color-verwijderen: oklch(58.3% 0.238666 28.4765);
            /* button terug/uitloggen */
            --color-terug-rood: oklch(63.78% 0.2373 25.44);
            /* admin */
            --color-admin: oklch(67.98% 0.171156 256.1873);
            --color-rooster: oklch(65.67% 0 0);
            --color-achtergrond2: oklch(90.67% 0 0);
        }
    </style>
</head>
<body class="flex flex-col items-center min-h-dvh noto-sans-normal">

    <section class="flex flex-row items-center justify-center dag bg-admin">
        <h2 class="text-white ">Werkuur toevoegen</h2>
    </section>

    <article class="flex flex-col items-center w-full flex-grow relative">

        <div class="w-full min-h-[100px]">
        </div>

        <section class="flex flex-col items-center justify-center w-full md:w-[80%] max-w-[1200px] min-h-[130px] shadow-md noto-sans-bold absolute top-[20px] bg-white">
            <a href="admin.html" class="w-full relative">
                <img src="media library/back arrow.webp" alt="Terug naar vorige pagina" class="w-[45px] h-[45px] absolute top-[-20px] left-[-20px] p-2 bg-terug-rood rounded-full ">
            </a>
            <h1>Werkuur toevoegen</h1>
        </section>

        <section class="flex flex-col items-end gap-16 w-full flex-grow bg-background ">

            <div class="min-h-[50px] w-full"></div>

            <div class="w-full md:w-[80%] md:max-w-[1000px] mx-auto mb-12">

                <form class="flex flex-row justify-center flex-wrap gap-x-20 gap-y-4 w-full">
                    <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                        <label for="naam" class="noto-sans-bold">Naam:</label>
                        <select type="text" id="naam" name="naam" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                            <option value="">Selecteer een naam</option>
                            <?php
                            include "conn.php";

                            if (!$conn) {
                                die("Databaseverbinding mislukt: " . mysqli_connect_error());
                            }

                            $sql = "SELECT pk_personeel, CONCAT(voornaam, ' ', familienaam) AS VolledigeNaam FROM personeel WHERE actief = 0 ORDER BY VolledigeNaam";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row["pk_personeel"] . '">' . $row["VolledigeNaam"] . '</option>';
                                }
                            } else {
                                echo '<option value="">Geen personeel gevonden</option>';
                            }

                            if (isset($conn)) {
                                mysqli_close($conn);
                            }
                            ?>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                        <label for="datum" class="noto-sans-bold">Datum:</label>
                        <input type="date" id="datum" name="datum" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                    </div>
                    <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                        <label for="begintijd" class="noto-sans-bold">Begintijd:</label>
                        <input type="time" id="begintijd" name="begintijd" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                    </div>
                    <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                        <label for="eindtijd" class="noto-sans-bold">Eindtijd:</label>
                        <input type="time" id="eindtijd" name="eindtijd" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                    </div>

                    <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                        <label for="school" class="noto-sans-bold">school:</label>
                        <select type="text" id="school" name="school" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                            <option value="">Selecteer een school</option>
                            <?php
                            include "conn.php";

                            if (!$conn) {
                                die("Databaseverbinding mislukt: " . mysqli_connect_error());
                            }

                            $sql = "SELECT pk_school, schoolnaam FROM school ORDER BY schoolnaam";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row["pk_school"] . '">' . $row["schoolnaam"] . '</option>';
                                }
                            } else {
                                echo '<option value="">Geen school gevonden</option>';
                            }

                            if (isset($conn)) {
                                mysqli_close($conn);
                            }
                            ?>
                        </select>
                    </div>
                    <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                        <label for="Job" class="noto-sans-bold">Job:</label>
                        <select type="text" id="Job" name="Job" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                            <option value="">Selecteer een Job</option>
                            <?php
                            include "conn.php";

                            if (!$conn) {
                                die("Databaseverbinding mislukt: " . mysqli_connect_error());
                            }

                            $sql = "SELECT pk_job, job_naam FROM job ORDER BY job_naam";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row["pk_job"] . '">' . $row["job_naam"] . '</option>';
                                }
                            } else {
                                echo '<option value="">Geen job gevonden</option>';
                            }

                            if (isset($conn)) {
                                mysqli_close($conn);
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="button rounded-md bg-toevoegenAanpassen cursor-pointer mt-8">
                        <img src="media library/plus.png" alt="Toevoegen" class="img-button">
                        <h2>Toevoegen</h2>
                    </button>
                </form>

            </div>

        </section>

    </article>

</body>
</html>