<?php
// Dziennik ocen zapisany w pliku tekstowym
// Wykorzystane funkcje: $_POST, fopen(), fwrite(), fclose(), file_exists()

$plik = "oceny.txt";
$blad = "";

/* ---------- CZĘŚĆ 1: zapis danych z formularza ---------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // pobranie danych metodą POST (trim usuwa zbędne spacje na brzegach)
    $uczen = trim($_POST["uczen"] ?? "");
    $ocena = (int) ($_POST["ocena"] ?? 0);
    $przedmiot = trim($_POST["przedmiot"] ?? "");

    // usunięcie znaków nowej linii i separatora "|", żeby nie zepsuć formatu pliku
    $uczen = preg_replace('/[\r\n|]+/', ' ', $uczen);
    $przedmiot = preg_replace('/[\r\n|]+/', ' ', $przedmiot);

    // walidacja
    if ($uczen === "" || $przedmiot === "") {
        $blad = "Wypełnij pola: imię i nazwisko oraz przedmiot.";
    } elseif ($ocena < 1 || $ocena > 6) {
        $blad = "Ocena musi być liczbą od 1 do 6.";
    } else {
        // tryb "a" (append): dopisuje na końcu pliku, nie kasuje poprzednich wpisów,
        // a gdy plik nie istnieje - tworzy go automatycznie
        $uchwyt = fopen($plik, "a") or die("Nie można otworzyć pliku do zapisu!");
        fwrite($uchwyt, $uczen . " | " . $przedmiot . " | ocena: " . $ocena . PHP_EOL);
        fclose($uchwyt);

        // przekierowanie, aby odświeżenie strony (F5) nie dopisało wpisu ponownie
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dziennik ocen</title>
</head>
<body>

<h1>Dziennik ocen</h1>

<form method="post" action="">
    <label for="uczen">Imię i nazwisko ucznia</label>
    <input type="text" id="uczen" name="uczen" required>

    <label for="ocena">Ocena</label>
    <select id="ocena" name="ocena">
        <?php for ($i = 1; $i <= 6; $i++): ?>
            <option value="<?= $i ?>"><?= $i ?></option>
        <?php endfor; ?>
    </select>

    <label for="przedmiot">Przedmiot</label>
    <input type="text" id="przedmiot" name="przedmiot" required>

    <input type="submit" value="Zapisz ocenę">
</form>

<?php if ($blad !== ""): ?>
    <p class="blad"><?= htmlspecialchars($blad) ?></p>
<?php endif; ?>

<!-- ---------- CZĘŚĆ 2: odczyt danych ---------- -->
<h2>Zapisane oceny</h2>

<?php
if (file_exists($plik)) {
    // tryb "r": otwarcie do odczytu
    $uchwyt = fopen($plik, "r") or die("Nie można otworzyć pliku do odczytu!");

    // odczyt linia po linii aż do końca pliku
    while (!feof($uchwyt)) {
        $linia = trim(fgets($uchwyt));
        if ($linia !== "") {
            echo htmlspecialchars($linia) . "<br>\n";
        }
    }

    fclose($uchwyt);
} else {
    echo "Brak zapisanych ocen.";
}
?>

</body>
</html>
