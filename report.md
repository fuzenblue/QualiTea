# Qualitea Project Report

## 1. Project Overview
**Qualitea** is a scalable web-based queue management system for a tea shop. It is designed to handle high concurrency, ensure data integrity, and provide real-time updates to users. The project architecture is containerized using Docker, leveraging NGINX as a load balancer, multiple PHP application instances, and a MySQL database.

## 2. Features Implemented
*   **Infrastructure**:
    *   Docker Compose orchestration.
    *   NGINX Load Balancer with Upstream (Round Robin) to `app1` and `app2`.
    *   MySQL 8.0 Database.
    *   Separate Frontend (HTML/JS) and Backend (PHP API).
*   **Core Functionality**:
    *   Tea Menu Display.
    *   Queue Booking System (Running number generation).
    *   Queue Status Checking.
    *   Admin Dashboard for queue management.
*   **Advanced Features**:
    *   Real-time Queue Updates via **WebSocket** (Node.js).
    *   **Healthchecks** for database connection stability.
    *   **Logging & Monitoring**: Custom NGINX log format for performance tracking.
    *   **Load Testing**: Automated stress tests using **k6** (targeting 2000 RPS).
    *   **Adminer** integration for easier database management.

## 3. Notable Challenges & Solutions

### 3.1. Database Connection Refused (Startup Race Condition)
*   **Problem**: When starting the stack (`docker-compose up`), the PHP application attempted to connect to MySQL before the database was fully initialized, resulting in `SQLSTATE[HY000] [2002] Connection refused`.
*   **Solution**:
    *   Implemented a **Healthcheck** in the `db` service definition in `docker-compose.yml` (`mysqladmin ping`).
    *   Added `depends_on: condition: service_healthy` to the PHP application services. This ensures the app containers only start after the database reports it is healthy.

### 3.2. Duplicate Queue Numbers (Race Condition)
*   **Problem**: Under high load (Stress Testing with k6), multiple requests successfully claimed the same queue number (e.g., multiple users got queue #233).
*   **Root Cause**: The read-modify-write cycle (`SELECT MAX` then `INSERT`) was not atomic. Multiple processes read the same "current max" value simultaneously.
*   **Solution**:
    1.  **Database Constraint**: added a `UNIQUE KEY (booking_date, queue_number)` to the `bookings` table. This prevents duplicate inserts at the database level.
    2.  **Retry Logic with Jitter**: Implemented a `try-catch` block in `BookingRepository.php`. If a `Duplicate Entry (23000)` error occurs:
        *   The system waits for a random time (Jitter: 5ms - 25ms).
        *   It retries the process (fetch new max -> insert) up to 20 times.
        *   This successfully eliminated duplicates and reduced contention.

### 3.3. Incorrect "Ahead" Count
*   **Problem**: Users reported seeing "280 queues ahead" while holding queue #233.
*   **Root Cause**: This was a symptom of the Duplicate Queue Numbers issue. The count query included all the duplicate entries from previous numbers, inflating the "ahead" count beyond the actual queue number.
*   **Solution**: Fixed automatically by resolving issue 3.2. Once queue numbers are unique, the count logic (`queue_number < current`) works correctly.

## 4. Technical Stack
*   **Backend**: PHP 8.2 (FPM), MySQL 8.0
*   **Frontend**: Vanilla HTML/JavaScript
*   **Real-time**: Node.js WebSocket
*   **Server**: NGINX (Reverse Proxy & Load Balancer)
*   **Testing**: Grafana k6

## 5. How to Run
```powershell
# Start the system
docker-compose up -d --build

# Run Load Test
docker run --rm --network qualitea_qualitea_net -v d:\qualitea\tests:/src grafana/k6 run /src/stress-test.js
```
