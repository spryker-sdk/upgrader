# Upgrader

[![Build Status](https://github.com/spryker-sdk/brancho/workflows/CI/badge.svg?branch=master)](https://github.com/spryker-sdk/brancho/actions?query=workflow%3ACI+branch%3Amaster)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.3-8892BF.svg)](https://php.net/)

Upgrader is a tool that helps to update Spryker modules.

## Installation

It is recommended that you install Upgrader globally:

```
composer global require spryker-sdk/upgrader
```

If you want it in your project go into your project folder and run:

```
composer require spryker-sdk/upgrader --dev
```

## Documentation

### Configuration

Prepare required environment variables:

```
export GITHUB_ACCESS_TOKEN=<GITHUB_TOCKEN> # GitHub access token to project repository with permission to push branches and create PRs.
export GITHUB_ORGANIZATION=<ORGANIZATION> # Free string.
export GITHUB_REPOSITORY=<REPOSITORY> # Free string.
```

### Commands

Upgrade command:
1. Performs check that "is exists uncommitted changes in a project", if exist - the process will stop.
2. Upgrades Spryker packages.
3. Commits there changes in composer.json and composer lock files.
4. Push changes and creates PR.

```
$HOME/.composer/vendor/bin/upgrader upgrade
```

### Available exceptions

Both of the bottom exceptions, mean that Upgrade cannot automatically install the next update and the developer should do it manually.

```
Release group contains major changes.
Release group contains changes on project level.
```
