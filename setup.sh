#!/bin/bash

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ ! -f Dockerfile ]; then
  cp Dockerfile.example Dockerfile
fi

if [ ! -f debezium-postgres/Dockerfile ]; then
  cp debezium-postgres/Dockerfile.example debezium-postgres/Dockerfile
fi

if [ ! -f docker-compose.yml ]; then
  cp docker-compose.yml.example docker-compose.yml
fi

if [ ! -f nginx/conf.d/app.conf ]; then
  cp nginx/conf.d/app.conf.example nginx/conf.d/app.conf
fi

if [ ! -f php/local.ini ]; then
  cp php/local.ini.example php/local.ini
fi