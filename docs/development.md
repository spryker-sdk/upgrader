# Local Development

## Docker

We use docker for prepare convenient and close to Spryker CLI development environmetn.

Directory mount in cointainer:
- Upgrader source mount to /data
- Some Spryker project for evaluating or upgradind mount to /data/project

## MacOS

### Running as docker container

```bash
mutagen-compose up --build -d
docker exec -it {contaiter_id} bash
bin/upgrader {command_name}
```

Note: File synchronization is enabled by the default

## Linux

### Running as docker container

```bash
export DOCKER_BUILDKIT=1 # or configure in daemon.json
export COMPOSE_DOCKER_CLI_BUILD=1

docker-compose -f linux-compose.yml up -d --build
docker exec -it {contaiter_id} bash
bin/upgrader {command_name}
```
