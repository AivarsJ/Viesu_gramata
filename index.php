<?php
// 1. Savienojums ar datubāzi
$serveris = "localhost";
$datubaze = "viesu_gramata";
$lietotajs = "root";
$parole = "";

try {
    $pdo = new PDO(
        "mysql:host=$serveris;dbname=$datubaze;charset=utf8mb4",
        $lietotajs,
        $parole
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    echo "Savienojums ar datubāzi veiksmīgs!<br>";

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("Neizdevās savienoties ar datubāzi.");
}

// 2. Parādām servera laiku
echo "Servera laiks: " . date("H:i:s") . "<br>";

// 3. Saņemam HTML formas datus
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $vards = trim($_POST["fname"] ?? "");
    $zina = trim($_POST["fremark"] ?? "");

    if ($vards === "" || $zina === "") {
        exit("Kļūda: jāaizpilda abi lauki!");
    }

    echo "PHP saņēma datus!<br>";
    echo "Vārds: " . htmlspecialchars($vards, ENT_QUOTES, "UTF-8");
    echo "<br>";
    echo "Ziņa: " . htmlspecialchars($zina, ENT_QUOTES, "UTF-8");
    echo "<br>";

    // 4. Saglabājam datus datubāzē
    try {
        $sql = "INSERT INTO viesu_gramata
                (vards, zina)
                VALUES (:vards, :zina)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":vards" => $vards,
            ":zina" => $zina
        ]);

        echo "Dati veiksmīgi saglabāti datubāzē!";

    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo "Kļūda: datus neizdevās saglabāt.";
    }

} else {
    echo "Dati vēl nav saņemti.";
}

	// 4. Viesu grāmatas ierakstu attēlošana pārlūkā
echo "<h2>Viesu grāmatas ieraksti</h2>";

$sql = "SELECT id, vards, zina
        FROM Viesu_gramata
        ORDER BY id ASC";

$stmt = $pdo->query($sql);
$ieraksti = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>ID</th><th>Vārds</th><th>Ziņa</th></tr>";

foreach ($ieraksti as $ieraksts) {
    echo "<tr>";
    echo "<td>" . (int)$ieraksts["id"] . "</td>";
    echo "<td>" . htmlspecialchars($ieraksts["vards"], ENT_QUOTES, "UTF-8") . "</td>";
    echo "<td>" . htmlspecialchars($ieraksts["zina"], ENT_QUOTES, "UTF-8") . "</td>";
    echo "</tr>";
}

echo "</table>";
?>