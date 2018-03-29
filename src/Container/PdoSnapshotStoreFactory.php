<?php
/**
 * This file is part of the prooph/pdo-snapshot-store.
 * (c) 2016-2018 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2018 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\SnapshotStore\Pdo\Container;

use Interop\Config\ConfigurationTrait;
use Interop\Config\ProvidesDefaultOptions;
use Interop\Config\RequiresConfigId;
use Interop\Config\RequiresMandatoryOptions;
use Prooph\SnapshotStore\CallbackSerializer;
use Prooph\SnapshotStore\Pdo\PdoSnapshotStore;
use Prooph\SnapshotStore\Serializer;
use Psr\Container\ContainerInterface;

class PdoSnapshotStoreFactory implements ProvidesDefaultOptions, RequiresConfigId, RequiresMandatoryOptions
{
    use ConfigurationTrait;

    /**
     * @var string
     */
    private $configId;

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

        /** repair legacy connection_service config key*/
        (function (&$config): void {
            foreach ($this->dimensions() as $dimension) {
                $config = &$config[$dimension];
            }

            $config = &$config[$this->configId] ?? [];

            if (! isset($config['connection']) && isset($config['connection_service'])) {
                $config['connection'] = $config['connection_service'];
            }
        })($config);

        $config = $this->options($config, $this->configId);

        $connection = $container->get($config['connection']);
        $serializer = $config['serializer'] instanceof Serializer ? $config['serializer'] : $container->get($config['serializer']);

        return new PdoSnapshotStore(
            $connection,
            $config['snapshot_table_map'],
            $config['default_snapshot_table_name'],
            $serializer,
            $config['disable_transaction_handling']
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

    public function mandatoryOptions(): iterable
    {
        return [
            'connection',
        ];
    }

    public function defaultOptions(): iterable
    {
        return [
            'snapshot_table_map' => [],
            'default_snapshot_table_name' => 'snapshots',
            'serializer' => new CallbackSerializer(null, null),
            'disable_transaction_handling' => false,
        ];
    }
}
