#bin/bash

echo "Start redis service"
docker-compose -f docker/docker-redis.yml up --build -d
echo "Redis ran successfully"
