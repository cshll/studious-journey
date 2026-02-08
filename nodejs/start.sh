#!/bin/sh

# Attempt to pre-boot bus route generator
sleep 10
node generate_routes.js
node server.js
