#!/bin/bash

# Zero-Downtime Deployment Simulation Script
# This script simulates a rolling update by scaling up services, validting health, and removing old containers.

echo "Starting Zero-Downtime Deployment..."

# 1. Pull latest images (if we were using a registry)
# docker-compose pull

# 2. Rebuild images (Process: Building New Version)
echo "Building new images..."
docker-compose build app1 app2

# 3. Scale Up: Start new instances alongside old ones to ensure no gap
# Note: In standard docker-compose without Swarm, 'up -d' will recreate containers if configuration changed.
# To simulate zero-downtime, we rely on NGINX retrying upstream (if configured) or standard rolling update strategies.
# Only Swarm/K8s does true zero-downtime easily. Here we use --scale to add more power first.

echo "Scaling up workers..."
docker-compose up -d --scale app1=2 --scale app2=2 --no-recreate

echo "Waiting for health checks (10s)..."
sleep 10

# 4. Reload NGINX to recognize new IPs
echo "Reloading Load Balancer..."
docker-compose exec nginx nginx -s reload

# 5. Scale Down: Remove likely 'old' containers (This is imperfect in Compose but illustrative)
# In reality, 'docker-compose up -d' with 'restart: always' does a reasonable job for small apps.
# For this script, we'll just return to standard scale.
echo "Returning to standard scale (Rolling replacement)..."
docker-compose up -d --scale app1=1 --scale app2=1

# 6. Final Reload
docker-compose exec nginx nginx -s reload

echo "Deployment Complete!"
