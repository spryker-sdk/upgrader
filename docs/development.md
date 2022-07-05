# Local Development

## Docker

We use docker to prepare a convenient and close-to Spryker CLI development environment.

Directory mount in a container:
- Upgrader source mount to /data
- Some Spryker projects for evaluating or upgrading mount to /data/project
  For this purpose, replace {path_to_project} with a real path in `docker-compose.yml` or `linux-compose.yml` depending on your OS

## MacOS

### Running as a docker container

```bash
mutagen-compose up --build -d
docker exec -it {contaiter_id} bash
cd /data/project
../bin/upgrader {command_name}
```

Note: File synchronization is enabled by the default

## Linux

### Running as docker container

```bash
export DOCKER_BUILDKIT=1 # or configure in daemon.json
export COMPOSE_DOCKER_CLI_BUILD=1

docker-compose -f linux-compose.yml up -d --build
docker exec -it {contaiter_id} bash
cd /data/project
../bin/upgrader {command_name}
```
