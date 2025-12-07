<?php

class BookingRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    private function notifyWebSocket($data)
    {
        $ch = curl_init('http://websocket:3000/broadcast');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Timeout fast so we don't block
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
        curl_exec($ch);
        curl_close($ch);
    }

    public function createBooking($customerName, $phone, $note)
    {
        $today = date('Y-m-d');
        $maxRetries = 20; // Increased retry count
        $attempt = 0;

        do {
            $attempt++;
            try {
                // 1. Get current max queue for today
                $stmt = $this->pdo->prepare("SELECT MAX(queue_number) as max_queue FROM bookings WHERE booking_date = ?");
                $stmt->execute([$today]);
                $row = $stmt->fetch();
                $nextQueue = ($row['max_queue'] ?? 0) + 1;

                // 2. Try to insert
                $sql = "INSERT INTO bookings (booking_date, queue_number, customer_name, phone, note, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt = $this->pdo->prepare($sql);
                $success = $stmt->execute([$today, $nextQueue, $customerName, $phone, $note]);

                if ($success) {
                    $this->notifyWebSocket([
                        'type' => 'NEW_BOOKING',
                        'queue_number' => $nextQueue
                    ]);
                    return $nextQueue;
                }
            } catch (\PDOException $e) {
                // Check if error is Duplicate Entry (Code 23000)
                if ($e->getCode() == '23000') {
                    // Race condition hit!
                    // Jitter: Sleep randomly between 5ms and 25ms to reduce contention
                    usleep(rand(5000, 25000));
                    continue;
                }
                // Other error? Throw it.
                throw $e;
            }
        } while ($attempt < $maxRetries);

        // If we ran out of retries
        return false;
    }

    public function getStatusByQueueOrPhone($query)
    {
        // Search using booking_date = CURDATE()
        $sql = "SELECT queue_number, status FROM bookings WHERE queue_number = ? AND booking_date = CURDATE() LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$query]);
        $booking = $stmt->fetch();

        if (!$booking) {
            $sql = "SELECT queue_number, status FROM bookings WHERE phone = ? AND booking_date = CURDATE() ORDER BY created_at DESC LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$query]);
            $booking = $stmt->fetch();
        }

        if ($booking) {
            $countSql = "SELECT COUNT(*) as ahead FROM bookings WHERE status IN ('WAITING', 'IN_PROGRESS') AND queue_number < ? AND booking_date = CURDATE()";
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
        $stmt = $this->pdo->query("SELECT * FROM bookings WHERE booking_date = CURDATE() ORDER BY queue_number DESC");
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $result = $stmt->execute([$status, $id]);

        if ($result) {
            $stmt = $this->pdo->prepare("SELECT queue_number FROM bookings WHERE id = ?");
            $stmt->execute([$id]);
            $booking = $stmt->fetch();

            $this->notifyWebSocket([
                'type' => 'STATUS_UPDATE',
                'queue_number' => $booking['queue_number'],
                'status' => $status
            ]);
        }
        return $result;
    }
}
