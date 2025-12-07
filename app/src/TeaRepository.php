<?php

class TeaRepository {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllTeas() {
        $stmt = $this->pdo->query("SELECT * FROM teas");
        return $stmt->fetchAll();
    }

    public function getTeaById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM teas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
