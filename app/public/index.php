<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/TeaRepository.php';

try {
    $pdo = get_db_connection();
    $repo = new TeaRepository($pdo);
    $teas = $repo->getAllTeas();
} catch (PDOException $e) {
    $error = "Connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qualitea Shop</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; }
        .tea-card { border: 1px solid #ddd; padding: 1rem; margin-bottom: 1rem; border-radius: 8px; }
        .price { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Welcome to Qualitea</h1>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php else: ?>
        <div class="tea-list">
            <?php foreach ($teas as $tea): ?>
                <div class="tea-card">
                    <h2><?php echo htmlspecialchars($tea['name']); ?></h2>
                    <p><?php echo htmlspecialchars($tea['description']); ?></p>
                    <p class="price">$<?php echo htmlspecialchars($tea['price']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>
