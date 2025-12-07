<?php

class BookingRepository {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createBooking($customerName, $teaId, $bookingDate) {
        $sql = "INSERT INTO bookings (customer_name, tea_id, booking_date) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$customerName, $teaId, $bookingDate]);
    }

    public function getAllBookings() {
        $stmt = $this->pdo->query("SELECT * FROM bookings");
        return $stmt->fetchAll();
    }
}
