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
            'connection_service' => 'my_connection',
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
            'connection_service' => 'my_connection',
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
