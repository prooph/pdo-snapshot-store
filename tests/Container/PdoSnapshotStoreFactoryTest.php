<?php

/**
 * This file is part of prooph/pdo-snapshot-store.
 * (c) 2016-2022 Alexander Miertsch <kontakt@codeliner.ws>
 * (c) 2016-2022 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\SnapshotStore\Pdo\Container;

use PDO;
use PHPUnit\Framework\TestCase;
use Prooph\SnapshotStore\CallbackSerializer;
use Prooph\SnapshotStore\Pdo\Container\PdoSnapshotStoreFactory;
use Prooph\SnapshotStore\Pdo\PdoSnapshotStore;
use ProophTest\SnapshotStore\Pdo\TestUtil;
use Psr\Container\ContainerInterface;

class PdoSnapshotStoreFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_adapter_via_connection_service(): void
    {
        $config['prooph']['pdo_snapshot_store']['default'] = [
            'connection' => 'my_connection',
        ];

        $connection = TestUtil::getConnection();

        $container = $this->prophesize(ContainerInterface::class);

        $container->get('my_connection')->willReturn($connection)->shouldBeCalled();
        $container->get('config')->willReturn($config)->shouldBeCalled();

        $factory = new PdoSnapshotStoreFactory();
        $snapshotStore = $factory($container->reveal());

        $this->assertInstanceOf(PdoSnapshotStore::class, $snapshotStore);
    }

    /**
     * @test
     */
    public function it_still_works_with_deprecated_connection_service_key(): void
    {
        $config['prooph']['pdo_snapshot_store']['default'] = [
            'connection_service' => 'my_connection',
        ];

        $connection = TestUtil::getConnection();

        $container = $this->prophesize(ContainerInterface::class);

        $container->get('my_connection')->willReturn($connection)->shouldBeCalled();
        $container->get('config')->willReturn($config)->shouldBeCalled();

        $factory = new PdoSnapshotStoreFactory();
        $snapshotStore = $factory($container->reveal());

        $this->assertInstanceOf(PdoSnapshotStore::class, $snapshotStore);
        $this->assertArrayHasKey('connection_service', $container->reveal()->get('config')['prooph']['pdo_snapshot_store']['default']);
    }

    /**
     * @test
     */
    public function it_still_works_with_deprecated_connection_service_key_for_config_objects(): void
    {
        $config['prooph']['pdo_snapshot_store']['default'] = [
            'connection_service' => 'my_connection',
        ];

        $connection = TestUtil::getConnection();

        $container = $this->prophesize(ContainerInterface::class);

        $container->get('my_connection')->willReturn($connection)->shouldBeCalled();
        $container->get('config')->willReturn(new \ArrayObject($config))->shouldBeCalled();

        $factory = new PdoSnapshotStoreFactory();
        $snapshotStore = $factory($container->reveal());

        $this->assertInstanceOf(PdoSnapshotStore::class, $snapshotStore);
        $this->assertArrayHasKey('connection_service', $container->reveal()->get('config')['prooph']['pdo_snapshot_store']['default']);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_invalid_container_given(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $eventStoreName = 'custom';
        PdoSnapshotStoreFactory::$eventStoreName('invalid container');
    }

    /**
     * @test
     */
    public function it_gets_serializer_from_container_when_not_instanceof_serializer(): void
    {
        $config['prooph']['pdo_snapshot_store']['default'] = [
            'connection' => 'my_connection',
            'serializer' => 'serializer_servicename',
        ];

        $connection = $this->prophesize(PDO::class);

        $container = $this->prophesize(ContainerInterface::class);

        $container->get('my_connection')->willReturn($connection)->shouldBeCalled();
        $container->get('config')->willReturn($config)->shouldBeCalled();
        $container
            ->get('serializer_servicename')
            ->willReturn(
                new CallbackSerializer(
                    function () {
                    },
                    function () {
                    }
                )
            )
            ->shouldBeCalled();

        $factory = new PdoSnapshotStoreFactory();
        $snapshotStore = $factory($container->reveal());

        $this->assertInstanceOf(PdoSnapshotStore::class, $snapshotStore);
    }
}
