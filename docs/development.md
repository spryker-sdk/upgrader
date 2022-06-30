# Local Development


## MacOS

### Running as docker container

`mutagen-compose up --build -d`

Note: File synchronization is enabled by the default


## Linux

### Running as docker container

```bash
export DOCKER_BUILDKIT=1 # or configure in daemon.json
export COMPOSE_DOCKER_CLI_BUILD=1

docker-compose -f linux-compose.yml up -d --build
```
