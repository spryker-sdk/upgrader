## Evaluator

!Only for internal usage

At this step, the upgrader checks the project against a set of rules. The main goal of this tool is to identify if the following parts of the project are compliant with the rules:
- Codebase
- Configuration
- Security
- Infrastructure

### Evaluating projects via the SDK

To evaluate a project, execute the following command from its directory:
```bash
bin/console analyze:php:code-compliance
```
The command generates a YAML report at `{project_directory}/reports/`.

Available options:
- `--module (-m)` - module filtration option. It is used to specify the modules for evaluation.
  Example `-m 'Pyz.ProductStorage'` where `Pyz` is namespace and `ProductStorage` is module name.

- `-v` - by increasing the verbosity level you will get more information about the error.

​
To view a previously generated report, run the following command:
​
```
bin/console analyze:php:code-compliance-report
```
​
This command returns the content of a previously generated report. Alternatively, you can view a report via an editor.
​


### Define custom prefixes for core entity names

When evaluator checks project-level code entities for existing and potential matches with the core ones, it skips the entities that have the `Pyz` prefix in their name. Such entities are considered unique and will not conflict with core entities in the future because there will never be an entity with the `Pyz` prefix in the core.

When solving upgradability issues, you can use the `Pyz` prefix to make your entities unique. To use custom prefixes, do the following:

1. Create `/tooling.yaml`
2. Define `Pyz` and needed custom prefixes. For example, define `Zyp` as a custom prefix:

```yaml
evaluator:
  prefixes:
    - Pyz
    - Zyp
```

Now the evaluator will not consider entities prefixed with `Zyp` as not unique.

### Skip some rules for this project

When some rule is not mandatory for your project, please add it to the `ignore list`.

The messages for the `ignored` rules will still be in CLI output and a report, but they will not be a reason for producing exit code 1 from the `analyze:php:code-compliance` command.

1. Create `/tooling.yaml`
2. For example, add `NotUnique:Constant` check to ignore list:

```yaml
evaluator:
  rules:
    ignore:
      - NotUnique:Constant
```
