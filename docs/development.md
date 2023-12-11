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
docker exec -it -u spryker upgrader_upgrader-sdk_1 bash
cd /data/project
../bin/upgrader {command_name}
```

## Configuring upgrader settings

All the predefined sittings you can find in `.env` and `.env.dev`.
For you local configs that contain the specific or private data like ACCESS_TOKEN you must create `.env.dev.local` that is in gitignore.
To find the options that should be set in `.env.dev.local` you can find in `.env.dev` with empty values.

The priorities of configuration sources look like this:
```
export ENV_VAR=... > .env.dev.local > .env.dev > .env
```
It means that the left hand settings can override other from the right side.

More about symfony .env https://symfony.com/doc/current/configuration.html#configuring-environment-variables-in-env-files

### How to run upgrader

Populate placeholders `<..>` with yours data

```bash
#Configure

cd /data/project && \
git remote set-url origin https://<account-name>:<gh-auth-token>@github.com/<account-name>/<repository-name>.git && \
composer global config -g github-oauth.github.com <gh-auth-token> && \
git config --global user.email "<user-email>" && \
git config --global user.name "<user-name>"

#Run upgrader

../bin/upgrader upgradability:php:upgrade -vvv
```
