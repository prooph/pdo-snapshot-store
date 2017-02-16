<?php
/**
 * This file is part of the prooph/pdo-snapshot-store.
 * (c) 2016-2017 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2017 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\Pdo\SnapshotStore\Container;

use Interop\Config\ConfigurationTrait;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\RequiresConfigId;
use PDO;
use Prooph\Pdo\SnapshotStore\PdoSnapshotStore;
use Psr\Container\ContainerInterface;

class PdoSnapshotStoreFactory implements ProvidesDefaultOptions, RequiresConfigId
{
    use ConfigurationTrait;

    /**
     * @var string
     */
    private $configId;

    /**
     * @var array
     */
    private $driverSchemeAliases = [
        'pdo_mysql' => 'mysql',
        'pdo_pgsql' => 'pgsql',
    ];

    private $driverSchemeSeparators = [
        'pdo_mysql' => ';',
        'pdo_pgsql' => ' ',
    ];

    /**
     * Creates a new instance from a specified config, specifically meant to be used as static factory.
     *
     * In case you want to use another config key than provided by the factories, you can add the following factory to
     * your config:
     *
     * <code>
     * <?php
     * return [
     *     PdoSnapshotStore::class => [PdoSnapshotStoreFactory::class, 'service_name'],
     * ];
     * </code>
     *
     * @throws \InvalidArgumentException
     */
    public static function __callStatic(string $name, array $arguments): PdoSnapshotStore
    {
        if (! isset($arguments[0]) || ! $arguments[0] instanceof ContainerInterface) {
            throw new \InvalidArgumentException(
                sprintf('The first argument must be of type %s', ContainerInterface::class)
            );
        }

        return (new static($name))->__invoke($arguments[0]);
    }

    public function __invoke(ContainerInterface $container): PdoSnapshotStore
    {
        $config = $container->get('config');
        $config = $this->options($config, $this->configId);

        if (isset($config['connection_service'])) {
            $connection = $container->get($config['connection_service']);
        } else {
            $separator = $this->driverSchemeSeparators[$config['connection_options']['driver']];
            $dsn = $this->driverSchemeAliases[$config['connection_options']['driver']] . ':';
            $dsn .= 'host=' . $config['connection_options']['host'] . $separator;
            $dsn .= 'port=' . $config['connection_options']['port'] . $separator;
            $dsn .= 'dbname=' . $config['connection_options']['dbname'] . $separator;
            $dsn = rtrim($dsn);
            $user = $config['connection_options']['user'];
            $password = $config['connection_options']['password'];
            $connection = new PDO($dsn, $user, $password);
        }

        return new PdoSnapshotStore(
            $connection,
            $config['snapshot_table_map'],
            $config['default_snapshot_table_name']
        );
    }

    public function __construct(string $configId = 'default')
    {
        $this->configId = $configId;
    }

    public function dimensions(): iterable
    {
        return ['prooph', 'pdo_snapshot_store'];
    }

    public function defaultOptions(): iterable
    {
        return [
            'connection_options' => [
                'driver' => 'pdo_mysql', // or use pdo_pgsql
                'user' => 'root',
                'password' => '',
                'host' => '127.0.0.1',
                'dbname' => 'snapshot_store',
                'port' => 3306, // or use 5432 for pgsql (default)
            ],
            'snapshot_table_map' => [],
            'default_snapshot_table_name' => 'snapshots',
        ];
    }
}
