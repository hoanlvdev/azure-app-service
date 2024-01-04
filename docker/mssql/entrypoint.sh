#!/usr/bin/env bash
set -m
./opt/mssql/bin/sqlservr & ./docker-entrypoint-initdb.d/setup_database.sh
fg