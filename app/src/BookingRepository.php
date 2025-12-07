<?php

class BookingRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function createBooking($customerName, $phone, $note)
    {
        // Find the next queue number for today
        $today = date('Y-m-d');
        $stmt = $this->pdo->prepare("SELECT MAX(queue_number) as max_queue FROM bookings WHERE DATE(created_at) = ?");
        $stmt->execute([$today]);
        $row = $stmt->fetch();
        $nextQueue = ($row['max_queue'] ?? 0) + 1;

        $sql = "INSERT INTO bookings (queue_number, customer_name, phone, note, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute([$nextQueue, $customerName, $phone, $note]);

        if ($success) {
            return $nextQueue;
        }
        return false;
    }

    public function getStatusByQueueOrPhone($query)
    {
        // Try searching by exact queue number first
        $sql = "SELECT queue_number, status FROM bookings WHERE queue_number = ? AND DATE(created_at) = CURDATE() LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$query]);
        $booking = $stmt->fetch();

        if (!$booking) {
            // Try searching by phone
            $sql = "SELECT queue_number, status FROM bookings WHERE phone = ? AND DATE(created_at) = CURDATE() ORDER BY created_at DESC LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$query]);
            $booking = $stmt->fetch();
        }

        if ($booking) {
            // Count how many WAITING or IN_PROGRESS queues are before this one
            $countSql = "SELECT COUNT(*) as ahead FROM bookings WHERE status IN ('WAITING', 'IN_PROGRESS') AND queue_number < ? AND DATE(created_at) = CURDATE()";
            $countStmt = $this->pdo->prepare($countSql);
            $countStmt->execute([$booking['queue_number']]);
            $ahead = $countStmt->fetch()['ahead'];

            return [
                'queue_number' => $booking['queue_number'],
                'status' => $booking['status'],
                'ahead' => $ahead
            ];
        }
        return null;
    }

    public function getAllBookingsToday()
    {
        $stmt = $this->pdo->query("SELECT * FROM bookings WHERE DATE(created_at) = CURDATE() ORDER BY queue_number DESC");
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}
