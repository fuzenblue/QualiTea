# 🍵 Qualitea - Enterprise Queue Management System

**Qualitea** is a high-performance, containerized web application designed for scalability and reliability. It features a complete **DevOps stack** including Monitoring, Logging, Alerting, and CI/CD, making it a production-ready template.

## 🏗 System Architecture

The system is built on a Microservices-like architecture orchestrated by Docker Compose:

*   **Core Services**:
    *   **Load Balancer**: NGINX (Reverse Proxy & Load Balancing).
    *   **Application**: 2x PHP-FPM Replicas (Stateless).
    *   **Database**: MySQL 8.0 with Healthchecks.
    *   **Caching**: Redis (available for session/query caching).
    *   **Real-time**: Node.js WebSocket Server.

*   **Observability Stack (LGTM)**:
    *   **Loki**: Centralized Log Aggregation.
    *   **Grafana**: Unified Dashboard for Metrics & Logs.
    *   **Prometheus**: Metrics Collection.
    *   **Promtail**: Log Helper (Ships Docker logs to Loki).
    *   **Node Exporter**: Hardware Monitoring.
    *   **Alertmanager**: Handle system alerts.

*   **Integrations**:
    *   **Line Messaging API**: Real-time critical alerts sent push notifications to Line App.

## 🚀 Features

*   ✅ **High Concurrency**: Handles 2000+ RPS without race conditions.
*   ✅ **Real-time Updates**: Queue status updates instantly via WebSockets.
*   ✅ **Full Observability**: View Logs and Metrics in a single dashboard.
*   ✅ **Alerting**: Get notified via Line when the server is down or under high load.
*   ✅ **CI/CD**: Automated testing pipeline with GitHub Actions.

## 🛠 Prerequisites

1.  Docker & Docker Compose
2.  Line Messaging API Token (Optional, for alerts)

## ⚡️ Quick Start

### 1. Configuration
Create a `.env` file in the root directory:
```properties
# Line Messaging API Configuration
LINE_CHANNEL_ACCESS_TOKEN=your_long_lived_token_here
LINE_USER_ID=your_user_id_here
```

### 2. Start System
```bash
docker-compose up -d --build
```
*Wait ~30 seconds for all healthchecks to pass.*

### 3. Access Interfaces
| Service | URL | Default Creds |
|---------|-----|---------------|
| **Main App** | [http://localhost:8080](http://localhost:8080) | - |
| **Grafana** | [http://localhost:3001](http://localhost:3001) | `admin` / `admin` |
| **Prometheus** | [http://localhost:9090](http://localhost:9090) | - |
| **B GUI** | [http://localhost:8081](http://localhost:8081) | User: `user` / Pass: `password` |

## 📊 Monitoring & Logging Guide

### Setting up Grafana
1.  **Login** to Grafana (admin/admin).
2.  **Add Data Sources**:
    *   **Prometheus**: URL `http://prometheus:9090`
    *   **Loki**: URL `http://loki:3100` (for Logs)
3.  **Import Dashboards**:
    *   ID **1860** (Node Exporter Full) -> Select Prometheus datasource.

### Viewing Logs
1.  Go to **Explore** sidebar menu.
2.  Select **Loki** as the source.
3.  Query: `{container="qualitea_app1"}` to see PHP logs, or `{job="docker"}` for all.

## 🧪 Testing

### Load Testing (k6)
Simulate 2000 concurrent users to test auto-scaling and monitoring.
```powershell
docker run --rm --network qualitea_qualitea_net -v "d:\qualitea\tests:/src" grafana/k6 run /src/stress-test.js
```

### CI/CD Pipeline
This project includes a **GitHub Actions** workflow (`.github/workflows/ci-cd.yml`) that:
1.  Builds Docker images.
2.  Starts the stack.
3.  Runs k6 stress tests automatically on every Push.

## 📂 Project Structure

```text
qualitea/
├── app/                 # Application Source
├── db/                  # Database Migration
├── monitoring/          # Observability Configs
│   ├── alertmanager/    # Alert Rules
│   ├── line_bridge/     # Line Notification Service
│   ├── loki/            # Logging Config
│   └── prometheus/      # Metrics Config
├── nginx/               # Load Balancer Config
├── tests/               # Stress Tests
├── .github/             # CI/CD Workflows
└── docker-compose.yml   # Stack Definition
```

---
*Developed for High-Performance & Reliability*
