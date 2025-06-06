#!/bin/bash
set -e

# Go to the project root (one level up from scripts/)
cd "$(dirname "$0")/.."

PROJECT_NAME=wecreate-front

# Build Docker images
docker-compose -p "$PROJECT_NAME" -f docker/docker-compose.yml build

# Start containers in detached mode
docker-compose -p "$PROJECT_NAME" -f docker/docker-compose.yml up -d

echo "Symfony app is building and running at http://localhost:8000"