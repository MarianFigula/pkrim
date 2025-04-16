# Web Art Gallery

This repository contains a web application developed as a school project with the theme of a web art gallery.

## Deployment Guide

Follow the steps below to set up and run the application.

### Requirements

- **Docker** is required to create a containerized environment for the application.

### Setup Instructions

1. Create a configuration file for the application by running the following command in the project directory:
```
cp .env.dist .env
```
2. Set up PHP environment:
```
cd php
cp .env.dist .env
# Open .env and add the JWT secret
```
3. Build Docker containers:
```
docker-compose build
```
4. Once the build is complete, start the containers in detached mode:
```
docker-compose up -d
```

5. Running the application
* Open your browser and navigate to http://localhost:3000/. The application should now be live and accessible at this address.