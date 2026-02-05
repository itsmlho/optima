<?php
$host = 'localhost';
$dbname = 'optima_ci';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>Database Views:</h2>";
    $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'");
    $views = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<pre>";
    print_r($views);
    echo "</pre>";

    echo "<h2>Views containing 'inventory_attachment':</h2>";
    foreach ($views as $view) {
        $stmt = $pdo->query("SHOW CREATE VIEW `{$view}`");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (stripos($result['Create View'], 'inventory_attachment') !== false || 
            stripos($result['Create View'], 'ia_attachment') !== false) {
            echo "<h3>View: {$view}</h3>";
            echo "<pre>" . htmlspecialchars($result['Create View']) . "</pre>";
            echo "<hr>";
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
