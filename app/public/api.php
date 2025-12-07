<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/TeaRepository.php';
require_once __DIR__ . '/../src/helpers.php';

try {
    $pdo = get_db_connection();
    $repo = new TeaRepository($pdo);
    $teas = $repo->getAllTeas();
    json_response(['data' => $teas]);
} catch (PDOException $e) {
    json_response(['error' => $e->getMessage()], 500);
}
