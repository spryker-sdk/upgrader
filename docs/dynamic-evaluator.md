# Dynamic evaluator

Dynamic evaluator is a middleware for checking project code after any updates during upgrader execution.

## Event model

Upgrader raises lifecycle events during processing release groups.

All the event classes you can find in `src/Upgrade/Application/Strategy/ReleaseApp/Processor/Event` folder.

- `PRE_PROCESSOR` - triggered before starting processing release groups
- `PRE_REQUIRE` - triggered before composer require action
- `POST_REQUIRE` - triggered after composer require and integrator action
- `POST_PROCESSOR` - triggered after processing release groups

Basically the Dynamic evaluator checkers are subscribed on these events and inject the violations into the generic upgrader response.


## Checkers

### Broken php files checker

#### Goal
To provide the list of the broken php files with the errors after applying `composer require`  and integrator commands.

#### Trigger point
`POST_REQUIRE` post require processor event

#### Output format
The PR description table with the following columns:

- `Composer command` - preceded composer command
- `Project file(s)` - project files with corresponding errors

Example output:

| Composer command                                                                                     | Project file(s) |
|------------------------------------------------------------------------------------------------------|-----------------|
| 'composer' 'require' 'spryker/sales:11.34.1' '--no-scripts' '--no-plugins' '--with-all-dependencies' | <b>src/Pyz/Glue/ProductPricesRestApi/ProductPricesRestApiDependencyProvider.php</b><br>Return type void of method Pyz\Glue\ProductPricesRestApi\ProductPricesRestApiDependencyProvider::getRestProductPricesAttributesMapperPlugins() is not covariant with return type array of method Spryker\Glue\ProductPricesRestApi\ProductPricesRestApiDependencyProvider::getRestProductPricesAttributesMapperPlugins().<br><br><b>src/Pyz/Zed/DataExport/DataExportDependencyProvider.php</b><br>Return type void of method Pyz\Zed\DataExport\DataExportDependencyProvider::getDataEntityExporterPlugins() is not covariant with return type array of method Spryker\Zed\DataExport\DataExportDependencyProvider::getDataEntityExporterPlugins().<br> |

### Project classes extends the updated core classes checker

#### Goal
To provide the list of the project class files which extend the updated spryker packages.

#### Trigger point
`POST_REQUIRE` post require processor event

#### Output format
The PR description table with the following columns:

- `Package` - the spryker updated package
- `Release` - the spryker release link
- `Classes that overrides the core private API` - the project class files which extend the updated spryker class

Example:

| Package                 | Release | Project file(s) |
|-------------------------|---------|-----------------|
| **spryker/zed-request** | [3.19.0](https://github.com/spryker/zed-request/releases/tag/3.19.0) | src/Pyz/Client/ZedRequest/ZedRequestDependencyProvider.php |
|  **spryker/price**      | [5.7.0](https://github.com/spryker/price/releases/tag/5.7.0) | src/Pyz/Client/Price/PriceDependencyProvider.php<br>src/Pyz/Shared/PriceUS/PriceConfig.php |
