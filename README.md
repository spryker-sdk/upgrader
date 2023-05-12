# Upgrader
[![Build Status](https://github.com/spryker-sdk/upgrader/workflows/CI/badge.svg?branch=master)](https://github.com/spryker-sdk/upgrader/actions?query=workflow%3ACI+branch%3Amaster)
[![codecov](https://codecov.io/gh/spryker-sdk/upgrader/branch/master/graph/badge.svg?token=AVljwSGALQ)](https://codecov.io/gh/spryker-sdk/upgrader)
[![Latest Stable Version](https://poser.pugx.org/spryker-sdk/upgrader/v/stable.svg)](https://packagist.org/packages/spryker-sdk/upgrader)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.4-8892BF.svg)](https://php.net/)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat)](https://phpstan.org/)

## Upgrader

The Upgrader tool simplifies keeping projects up-to-date with Spryker releases.

​
The Upgrader updates projects via one of the following approaches:
- Release group(default) — uses Spryker release app as data provider.
- Composer update — uses `composer update` command; this is the default strategy that's describes below.
  ​
  At this step, the Upgrader updates the project to the latest version as follows:
1. Checks if the target branch has been created on the remote repository. If the branch exists, the process stops.
2. Checks if there are no uncommitted changes in the project. If there are uncommitted changes, the process stops.
3. Updates Spryker modules and dependent libraries, including third-party ones.
4. Triggers [Integrator](https://github.com/spryker-sdk/integrator) for adjust project classes.
5. Commits the changed files.
6. Pushes the changes to the remote repository.
7. Create a PR in the remote repository.


### Supported strategies:
### Composer strategy
* To enable Composer update strategy:
```bash
export UPGRADE_STRATEGY=composer
```
#### Composer update strategy
Composer install|update uses two strategies.
By default Composer updates lock file with packages being downloaded.

* To enable the update of `composer.lock` file without packages being downloaded:
```bash
export COMPOSER_NO_INSTALL=true
```

### Release App strategy
In the strategy, Upgrader uses Spryker release app as a data provider to fetch data about Major, Minor, and Patch releases.
Only Minor and Paths could be automatically applied, for now. In case when some major release is available, Upgrader put info about to PR description.

* To enable Release group strategy (default):
```bash
export UPGRADE_STRATEGY=release-app
```

In the strategy, Upgrader contains aggregate (default) release group requiring processor and sequential processor (one by one release group).

#### Sequential release group processor
* To enable Sequential release group processor:
```bash
export RELEASE_GROUP_PROCESSOR=sequential
```

Sequential release group processor contains threshold, by default 30 release groups for one Upgrader start.
* To change the threshold:
```bash
export THRESHOLD_RELEASE_GROUP=<number>
```

#### Aggregate release group processor
* To enable aggregate release group processor:
```bash
export RELEASE_GROUP_PROCESSOR=aggregate
```

#### Soft thresholds:

Soft major threshold, by default 0.

* To change soft major threshold:
```bash
export SOFT_THRESHOLD_MAJOR=<number>
```

Soft minor threshold, by default 10.
​
* To change soft minor threshold:
```bash
export SOFT_THRESHOLD_MINOR=<number>
```
​
Soft bugfix threshold, by default 30.
​
* To change soft patch threshold:
```bash
export SOFT_THRESHOLD_PATCH=<number>
```
### Adding GitHub configuration for the update step
​
To enable the Upgrader to execute this step, apply the following configuration:
​
* GitHub is default source code provider. If you want manually define it, use the next environment variable:
  ​
```bash
export SOURCE_CODE_PROVIDER=github
```
​
* Add a GitHub access token to the project repository with the permissions to push branches and create PRs:
  ​
```bash
export ACCESS_TOKEN=<GITHUB_TOKEN>
```
​
* Add the organization name owning the repository of the project you want to update:
```bash
export ORGANIZATION_NAME=<ORGANIZATION>
```
​
* Add the repository name of the project you want to update:
```bash
export REPOSITORY_NAME=<REPOSITORY>
```
### Adding GitLab configuration for the update step
​
To enable the Upgrader to execute this step, apply the following configuration:
​
* Enable GitLab source code provider:
  ​
```bash
export SOURCE_CODE_PROVIDER=gitlab
```
​
* Add a GitLab access token to the project repository with the permissions to push branches and create PRs:
  ​
```bash
export ACCESS_TOKEN=<GITLAB_TOKEN>
```
​
* Add the project id of the project you want to update:
```bash
export PROJECT_ID=<PROJECT_ID>
```
​
* Optional. For self-hosted source provider add url of your provider:
```bash
export SOURCE_CODE_PROVIDER_URL=<https://git.yourdomain.com>
```
### Adding Azure configuration for the update step
​
To enable the Upgrader to execute this step, apply the following configuration:
​
* Enable Azure source code provider:
  ​
```bash
export SOURCE_CODE_PROVIDER=azure
```
​
* Add a Azure access token to the project repository with the permissions to push branches and create PRs:
  ​
```bash
export ACCESS_TOKEN=<AZURE_TOKEN>
```
​
* Add the organization name owning the repository of the project you want to update::
  ​
```bash
export ORGANIZATION_NAME=<ORGANIZATION_NAME>
```
​
* Add a project name or project id you want to update:
  ​
```bash
export PROJECT_NAME=<PROJECT_NAME>
# OR
export PROJECT_ID=<PROJECT_ID>
```
​
* Add a project's repository name or repository id:
  ​
```bash
export REPOSITORY_NAME=<REPOSITORY_NAME>
# OR
export REPOSITORY_ID=<REPOSITORY_ID>
```
​
### Reporting configuration
​
* Optional. Defines execution environment for report statistics:
```bash
export APP_ENV=sprykerci
```
​
* Optional. Enable the report sending functionality. it’s disabled by default.
```bash
export REPORTING_ENABLED=true
```
​
* Optional. Secure token for remote server request authorization in report sending process.
```bash
export REPORT_SEND_AUTH_TOKEN=<TOKEN>
```
​
### Updating projects via the SDK
​
To update a project, run the following command from its directory:
```bash
bin/console upgradability:php:upgrade
```

## Enable project change integration
​
* Turn on integrator trigger after package update step
```bash
export INTEGRATOR_ENABLED=true
```

## Evaluator

[Evaluator documentation](docs/evaluator.md)


## Installation
​
For Spryker SDK installation instructions, see [Spryker SDK](https://github.com/spryker-sdk/sdk#installation)
