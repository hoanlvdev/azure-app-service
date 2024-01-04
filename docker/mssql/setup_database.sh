#!/usr/bin/env bash
# Wait for database to startup 
sleep 20
./opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P Splus@2024 -i /docker-entrypoint-initdb.d/setup.sql