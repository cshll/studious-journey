# St Alphonsus Primary School

How to start the server:
- First download Docker Desktop on Windows: https://www.docker.com/products/docker-desktop/
- Install it as well as WSL 2 (if asked).
- Open Docker Desktop.
- Navigate to this project folder where 'docker-dompose.yml' is.
- Right-click and open in terminal.
- Run this command below:
```
docker-compose up -d --build --force-recreate
```
- If it doesn't work run the below:
```
docker compose up -d --build --force-recreate
```
- Verify it's up using:
```
docker ps
```
- Visit localhost on your browser for the site and localhost:8080 for phpMyAdmin.
This can also be done on Linux (**run using sudo**) using the above commands in a terminal (if docker is installed).
