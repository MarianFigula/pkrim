# Web Art Gallery - School Project

This repository contains a web application developed as a school project with the theme of a web art gallery.

## Deployment Guide

Follow the steps below to set up and run the application.

### Requirements

- **Docker** is required to create a containerized environment for the application.

### Setup Instructions

1. Create a configuration file for the application by adding the following lines to the `.env` file in the project directory:
```
REACT_APP_SERVER_URL=http://localhost:8000/
```
2. Build Docker containers:
```
docker-compose build
```
3. Once the build is complete, start the containers in detached mode:
```
docker-compose up -d
```

4. Running the application 
* Open your browser and navigate to http://localhost:3000/. The application should now be live and accessible at this address.