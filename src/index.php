<?php
declare(strict_types=1);

$pdo = new PDO(
    'mysql:host=mysql;dbname=korka;charset=utf8mb4',
    'korka',
    'korka',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

// создаём таблицу
$pdo->exec("
    CREATE TABLE IF NOT EXISTS counters (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(64) NOT NULL UNIQUE,
        value INT NOT NULL DEFAULT 0
    )
");

// инициализируем счётчики
$names = ['apples', 'bananas', 'oranges'];

$stmt = $pdo->prepare("
    INSERT IGNORE INTO counters (name, value)
    VALUES (:name, 0)
");

foreach ($names as $name) {
    $stmt->execute(['name' => $name]);
}

// обработка клика
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inc'])) {
    $name = $_POST['inc'];

    $stmt = $pdo->prepare("
        UPDATE counters
        SET value = value + 1
        WHERE name = :name
    ");
    $stmt->execute(['name' => $name]);

    // редирект — чтобы не было повторного POST
    header('Location: /counters.php');
    exit;
}

// получаем данные
$counters = $pdo->query("
    SELECT name, value
    FROM counters
    ORDER BY id
")->fetchAll();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>PHP Counters</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            max-width: 500px;
            margin: 40px auto;
        }
        .counter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        button {
            padding: 6px 14px;
            font-size: 14px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h1>📊 Счётчики (MySQL)</h1>

<?php foreach ($counters as $c): ?>
    <form method="post" class="counter">
        <strong><?= htmlspecialchars($c['name']) ?></strong>
        <span><?= $c['value'] ?></span>
        <button type="submit" name="inc" value="<?= htmlspecialchars($c['name']) ?>">
            +1
        </button>
    </form>
<?php endforeach; ?>

<p>🔄 Обнови страницу — значения сохраняются</p>
<a href="/profile.php">Другая страница</a>

</body>
</html>

