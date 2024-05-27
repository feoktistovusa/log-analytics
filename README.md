# Symfony Log Processor

This project processes log files and provides an endpoint to count log entries based on various filters.

## Setup and Run

### Requirements

- Docker
- Docker Compose

### Installation

### Build and run the Docker containers:

```bash
docker-compose up --build
```

### Run the migrations:
```bash
docker exec -it symfony_app bash

php bin/console doctrine:migrations:migrate
```

### Process the log file:
```bash
php bin/console app:process-log logs.log
```


Access the `/count` endpoint with appropriate query parameters: