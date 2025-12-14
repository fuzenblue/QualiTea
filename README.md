# 🍵 Qualitea - High Performance Queue Management System

**Qualitea** is a robust, clean, and scalable web application for managing tea shop orders and queues. Built with performance and concurrency in mind, it leverages a containerized microservices-like architecture to ensure reliability under high load.

## 🏗 System Architecture

The system mimics a production-grade environment using Docker Compose:

*   **Load Balancer**: NGINX (Reverse Proxy) balances traffic using Round-Robin strategy.
*   **Application**: 2x PHP-FPM Instances (`app1`, `app2`) handling business logic (Stateless).
*   **Database**: MySQL 8.0 with persistent storage and connection health checks.
*   **Real-time**: Node.js WebSocket Server for instant queue status updates.
*   **Monitoring Stack**:
    *   **Prometheus**: Time-series database for metrics collection.
    *   **Grafana**: Visualization dashboard for system performance.
    *   **Node Exporter**: Hardware and OS metrics collector.
*   **Management**: Adminer for database GUI.

## 🚀 Features

*   **Customer Facing**:
    *   📋 **Menu**: Browse available teas.
    *   🎫 **Booking**: Get a queue number (Auto-reset daily).
    *   🔍 **Track**: Real-time status checking (Wait time/Queues ahead).
*   **Admin Facing**:
    *   🛠 **Dashboard**: Manage queue status (Call, Done, Cancel).
*   **DevOps & Observability**:
    *   📊 **Full-Stack Monitoring**: Real-time CPU, RAM, and Network metrics via Grafana.
    *   ✅ **Race Condition Proof**: Handles 2000+ concurrent requests without duplicate queues.
    *   ✅ **Zero-Downtime Ready**: Stateless application design.

## 🛠 Prerequisites

*   Docker & Docker Compose

## ⚡️ Quick Start

1.  **Clone & Start**:
    ```bash
    # Start all services (App + DB + Monitoring)
    docker-compose up -d --build
    ```

2.  **Access the Dashboard**:
    *   **Main App**: [http://localhost:8080](http://localhost:8080)
    *   **Grafana (Monitoring)**: [http://localhost:3001](http://localhost:3001)
        *   *Default Creds*: `admin` / `admin`
    *   **Prometheus**: [http://localhost:9090](http://localhost:9090)
    *   **Adminer (DB GUI)**: [http://localhost:8081](http://localhost:8081)
        *   *Creds*: System: `MySQL`, Server: `db`, User: `user`, Pass: `password`, DB: `qualitea_db`

## 📊 Setting Up Monitoring

To view the system metrics in Grafana:

1.  Go to **[http://localhost:3001](http://localhost:3001)**.
2.  **Add Data Source**: Select **Prometheus** -> URL: `http://prometheus:9090` -> Save & Test.
3.  **Import Dashboard**:
    *   Go to **Dashboards** > **Import**.
    *   Use ID **1860** (Node Exporter Full) to load a comprehensive system overview.

## 🧪 Testing & Verification

### Load Testing with k6
We use **k6** to simulate high traffic. You can observe the impact on the system in real-time via Grafana.

```powershell
# Run load test via Docker
docker run --rm --network qualitea_qualitea_net -v "d:\qualitea\tests:/src" grafana/k6 run /src/stress-test.js

# Or run locally (if k6 is installed)
k6 run -e BASE_URL=http://localhost:8080 tests/stress-test.js
```

**What to watch on Grafana:**
*   **CPU Usage**: Should spike during the booking phase.
*   **Network I/O**: Should increase correlating with the requests per second.

## 📂 Project Structure

```text
qualitea/
├── app/                 # PHP Application Code
├── db/                  # Database Init Scripts
├── monitoring/          # Monitoring Configuration
│   └── prometheus/      # Prometheus.yml
├── nginx/               # NGINX Configuration
├── tests/               # k6 Load Test Scripts
├── websocket/           # Node.js WebSocket Server
├── scripts/             # Deployment Scripts
└── docker-compose.yml   # Orchestration
```

---
*Generated for Qualitea Project*
