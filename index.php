<?php
// Savienojums ar datubāzi
try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=viesu_gramata;charset=utf8mb4",
        "root",
        ""
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("Neizdevās savienoties ar datubāzi.");
}

// Saņemam un saglabājam formas datus
$kluda = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $vards = trim($_POST["fname"] ?? "");
    $zina = trim($_POST["fremark"] ?? "");

    if ($vards === "" || $zina === "") {
        $kluda = "Lūdzu, aizpildiet abus laukus!";
    } else {
        try {
            $sql = "INSERT INTO Viesu_gramata
                    (vards, zina)
                    VALUES (:vards, :zina)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":vards" => $vards,
                ":zina" => $zina
            ]);

            // Pāradresācija novērš atkārtotu datu ievietošanu, atsvaidzinot lapu
            header("Location: index.php?saglabats=1");
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $kluda = "Datus neizdevās saglabāt.";
        }
    }
}

// SELECT - visi saglabātie ieraksti
$sql = "SELECT id, vards, zina
        FROM Viesu_gramata
        ORDER BY id ASC";

$stmt = $pdo->query($sql);
$ieraksti = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <title>Solar Cargo viesu grāmata</title>

<!-- Pārlūka, formas un tabulas noformējums -->
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
            line-height: 1.5;
        }

        label {
            display: block;
            margin-top: 12px;
        }

        input {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
        }

        button {
            margin-top: 16px;
            padding: 10px 20px;
            cursor: pointer;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #999;
            padding: 10px;
            text-align: left;
            overflow-wrap: anywhere;
        }

<!-- Veiksmīgas ievades vai kļūdu paziņojumu attēlošana -->
        .success {
            background: #e5f5e5;
            padding: 12px;
            color: #176b27;
        }

        .error {
            background: #ffe5e5;
            padding: 12px;
            color: #a00000;
        }
    </style>
</head>

<body>

<h1>Solar Cargo 20. gadadienas svinību viesu grāmata</h1>

<p>
Laipni lūdzam Solar Cargo 20. gadadienas svinību
pasākumā. Pirms turpināt dalību pasākumā,
lūdzam reģistrēties Viesu grāmatā.
</p>

<h2>Viesu grāmatas forma</h2>

<?php if (isset($_GET["saglabats"])
          && $_GET["saglabats"] === "1"): ?>
    <p class="success">
        Paldies! Jūsu reģistrācija ir veiksmīga,
        un ziņa ir saglabāta.
    </p>
<?php endif; ?>

<?php if ($kluda !== ""): ?>
    <p class="error">
        <?= htmlspecialchars($kluda, ENT_QUOTES, "UTF-8") ?>
    </p>
<?php endif; ?>

<!-- Pārlūka formas datu saņemšanas kods -->
<form action="index.php" method="post">

    <label for="fname">Vārds:</label>
    <input
        type="text"
        id="fname"
        name="fname"
        maxlength="100"
        required
    >

    <label for="fremark">Ziņa:</label>
    <input
        type="text"
        id="fremark"
        name="fremark"
        maxlength="255"
        required
    >

    <button type="submit">Saglabāt</button>

</form>

<!-- Iepriekšējo reģistrāciju attēlošana pārlūkā -->
<h2>Viesu grāmatas ieraksti</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Vārds</th>
            <th>Ziņa</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($ieraksti as $ieraksts): ?>
            <tr>
                <td><?= (int)$ieraksts["id"] ?></td>

                <td>
                    <?= htmlspecialchars(
                        $ieraksts["vards"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars(
                        $ieraksts["zina"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>