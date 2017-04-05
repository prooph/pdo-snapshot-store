# Interop Factories

Instead of providing a module, a bundle, a bridge or similar framework integration prooph/event-store ships with `interop factories`.

## Factory-Driven Creation

The concept behind these factories (see `src/Container` folder) is simple but powerful. It allows us to provide you with bootstrapping logic for the event store and related components
without the need to rely on a specific framework. However, the factories have three requirements.

### Requirements

1. Your Inversion of Control container must implement the [PSR Container interface](https://github.com/php-fig/container).
2. [interop-config](https://github.com/sandrokeil/interop-config) must be installed
3. The application configuration should be registered with the service id `config` in the container.

*Note: Don't worry, if your environment doesn't provide these requirements, you can
always bootstrap the components by hand. Just look at the factories for inspiration in this case.*

### PdoSnapshotStoreFactory

Sample configuration:

```php
[
    'prooph' => [
        'pdo_snapshot_store' => [
            'default' => [
                'connection_service' => 'my_pdo_connection', //<-- service name of your pdo connection
                'default_snapshot_table_name' => 'snapshots', //<-- table name of your snapshots, defaults to `snapshots`
                'snapshot_table_map' => [ //<-- Array with key = aggregate type, value = snapshot table name, defaults to empty array
                    'My\AggregateOne' => 'one_snapshots',
                    'My\AggregteTwo' => 'two_snapshots',
                ],
                'serializer' => 'My\Serializer' //<-- optional, service name of a custom serializer
            ],
        ],
    ],
]
```
