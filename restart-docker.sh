#!/bin/bash

# Get container names from docker-compose ps
container_names=$(docker-compose ps --services)

# Restart each container
for container in $container_names; do
  echo "Restarting container: $container"
  docker restart $container
  echo "$container restarted successfully!"
done

echo "All containers restarted successfully!"
