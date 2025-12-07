# 🍵 Qualitea - High Performance Queue Management System

**Qualitea** is a robust, clean, and scalable web application for managing tea shop orders and queues. Built with performance and concurrency in mind, it leverages a containerized microservices-like architecture to ensure reliability under high load.

## 🏗 System Architecture

The system mimics a production-grade environment using Docker Compose:

*   **Load Balancer**: NGINX (Reverse Proxy) balances traffic using Round-Robin strategy.
*   **Application**: 2x PHP-FPM Instances (`app1`, `app2`) handling business logic (Stateless).
*   **Database**: MySQL 8.0 with persistent storage and connection health checks.
*   **Real-time**: Node.js WebSocket Server for instant queue status updates.
*   **Management**: Adminer for database GUI.

## 🚀 Features

*   **Customer Facing**:
    *   📋 **Menu**: Browse available teas.
    *   🎫 **Booking**: Get a queue number (Auto-reset daily).
    *   🔍 **Track**: Real-time status checking (Wait time/Queues ahead).
*   **Admin Facing**:
    *   🛠 **Dashboard**: Manage queue status (Call, Done, Cancel).
*   **Technical Highlights**:
    *   ✅ **Race Condition Proof**: Handles 2000+ concurrent requests without duplicate queues.
    *   ✅ **Zero-Downtime Ready**: Stateless application design.
    *   ✅ **Real-time Updates**: Status changes reflect instantly via WebSockets.

## 🛠 Prerequisites

*   Docker & Docker Compose

## ⚡️ Quick Start

1.  **Clone & Start**:
    ```bash
    # Start all services
    docker-compose up -d --build
    ```

2.  **Access the Application**:
    *   **Main App**: [http://localhost:8080](http://localhost:8080)
    *   **Adminer (DB GUI)**: [http://localhost:8081](http://localhost:8081)
        *   *Creds*: System: `MySQL`, Server: `db`, User: `user`, Pass: `password`, DB: `qualitea_db`

## 🧪 Testing & Verification

### Load Testing
We include a **k6** script to simulate high traffic (2000 RPS).

```powershell
docker run --rm --network qualitea_qualitea_net -v d:\qualitea\tests:/src grafana/k6 run /src/stress-test.js
```

### Real-time Test
1.  Open **Check Status** page in Browser A.
2.  Open **Admin Dashboard** in Browser B.
3.  Update status in Admin -> Browser A updates automatically!

## 📂 Project Structure

```text
qualitea/
├── app/                 # PHP Application Code
│   ├── public/          # Public facing files (HTML, JS, API Entry)
│   └── src/             # Backend Logic (Repositories, DB)
├── db/                  # Database Init Scripts
├── nginx/               # NGINX Configuration
├── tests/               # k6 Load Test Scripts
├── websocket/           # Node.js WebSocket Server
├── scripts/             # Deployment Scripts
└── docker-compose.yml   # Orchestration
```

## 📝 Design Decisions

*   **Why specific `queue_number` logic?**
    *   Added `Retry Loop` with `Jitter` to handle high concurrency collisions on the Unique Constraint.
*   **Why WebSocket?**
    *   To reduce polling load on the server and provide a better UX.
*   **Why Healthchecks?**
    *   To prevent the "Connection Refused" error effectively during container startup.

---
*Generated for Qualitea Project - Version 1.0*
