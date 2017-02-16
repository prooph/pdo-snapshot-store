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

namespace ProophTest\Pdo\SnapshotStore\Container;

use PHPUnit\Framework\TestCase;
use Prooph\Pdo\SnapshotStore\Container\PdoSnapshotStoreFactory;
use Prooph\Pdo\SnapshotStore\PdoSnapshotStore;
use ProophTest\Pdo\SnapshotStore\TestUtil;
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
    public function it_creates_adapter_via_connection_options(): void
    {
        $config['prooph']['pdo_snapshot_store']['custom'] = [
            'connection_options' => TestUtil::getConnectionParams(),
        ];

        $container = $this->prophesize(ContainerInterface::class);

        $container->get('config')->willReturn($config)->shouldBeCalled();

        $snapshotStoreName = 'custom';
        $snapshotStore = PdoSnapshotStoreFactory::$snapshotStoreName($container->reveal());

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
}
