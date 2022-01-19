# Upgrader tool
​
The Upgrader tool simplifies keeping projects up-to-date with Spryker releases.
​
The upgrade flow consists of the following steps:
- [Evaluation](#evaluation)
- [Update](#update)
  ​
## Evaluation
​
At this step, the upgrader checks the project against a set of rules. The main goal of this tool is to identify if the following parts of the project are compliant with the rules:
- Codebase
- Configuration
- Security
- Infrastructure
  ​
### Evaluating projects via the SDK
​
To evaluate a project, execute the following command from its directory:
```bash
bin/console analyze:php:code-compliance --format=yaml
```
​
- The command generates a YAML report at `{project_directory}/reports/`.
  ​
  To view a previously generated report, run the following command:
  ​
```
bin/console analyze:php:code-compliance-report
```
​
This command returns the content of a previously generated report. Alternatively, you can view a report via an editor.
​
## Update
​
The Upgrader updates projects via one of the following approaches:
- Composer update — uses `composer update` command; this is the default strategy that's describes below.
- Release group(in development) — uses release groups.
  ​
  At this step, the Upgrader updates the project to the latest version as follows:
1. Checks if the target branch has been created on the remote repository. If the branch exists, the process stops.
2. Checks if there are no uncommitted changes in the project. If there are uncommitted changes, the process stops.
3. Updates Spryker modules and dependent libraries, including third-party ones.
4. Commits the changes in `composer.json` and `composer.lock` files.
5. Pushes the changes to the remote repository.
6. Create a PR in the remote repository.
   ​
### Adding GitHub configuration for the update step
​
To enable the Upgrader to execute this step, apply the following configuration:
​
* Add a GitHub access token to the project repository with the permissions to push branches and create PRs:
  ​
```bash
export GITHUB_ACCESS_TOKEN=<GITHUB_TOCKEN>
```
​
* Add the organization name owning the repository of the project you want to update:
```bash
export GITHUB_ORGANIZATION=<ORGANIZATION>
```
​
* Add the repository name of the project you want to update:
```bash
export GITHUB_REPOSITORY=<REPOSITORY>
```
​
### Updating projects via the SDK
​
To update a project, run the following command from its directory:
```bash
bin/console upgradability:php:upgrade
```
​
## Installation
​
For Spryker SDK installation instructions, see [Spryker SDK](https://github.com/spryker-sdk/sdk#installation)
