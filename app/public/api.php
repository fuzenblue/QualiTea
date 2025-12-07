<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/TeaRepository.php';
require_once __DIR__ . '/../src/BookingRepository.php';
require_once __DIR__ . '/../src/helpers.php';

try {
    $pdo = get_db_connection();
    $teaRepo = new TeaRepository($pdo);
    $bookingRepo = new BookingRepository($pdo);

    $method = $_SERVER['REQUEST_METHOD'];
    $path = trim($_SERVER['PATH_INFO'] ?? '', '/');

    // Route: GET /api/teas
    if ($method === 'GET' && $path === 'teas') {
        $teas = $teaRepo->getAllTeas();
        json_response($teas);
    }

    // Route: POST /api/bookings
    elseif ($method === 'POST' && $path === 'bookings') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['customer_name']) || !isset($input['phone'])) {
            json_response(['error' => 'Missing required fields'], 400);
        }
        $queueNumber = $bookingRepo->createBooking($input['customer_name'], $input['phone'], $input['note'] ?? '');
        if ($queueNumber) {
            json_response(['queue_number' => $queueNumber, 'message' => 'Booking created']);
        } else {
            json_response(['error' => 'Failed to create booking'], 500);
        }
    }

    // Route: GET /api/queue-status
    elseif ($method === 'GET' && $path === 'queue-status') {
        $query = $_GET['queue_number'] ?? $_GET['phone'] ?? null;
        if (!$query) {
            json_response(['error' => 'Missing queue_number or phone'], 400);
        }
        $status = $bookingRepo->getStatusByQueueOrPhone($query);
        if ($status) {
            json_response($status);
        } else {
            json_response(['error' => 'Queue not found'], 404);
        }
    }

    // Route: GET /api/admin/bookings
    elseif ($method === 'GET' && $path === 'admin/bookings') {
        $bookings = $bookingRepo->getAllBookingsToday();
        json_response($bookings);
    }

    // Route: POST /api/admin/update-status
    elseif ($method === 'POST' && $path === 'admin/update-status') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!isset($input['id']) || !isset($input['status'])) {
            json_response(['error' => 'Missing required fields'], 400);
        }
        $bookingRepo->updateStatus($input['id'], $input['status']);
        json_response(['message' => 'Status updated']);
    } else {
        json_response(['error' => 'Not Found', 'path' => $path], 404);
    }
} catch (Exception $e) {
    json_response(['error' => $e->getMessage()], 500);
}
