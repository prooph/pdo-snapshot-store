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

namespace ProophTest\SnapshotStore\Pdo;

use PDO;
use PHPUnit\Framework\TestCase;
use Prooph\SnapshotStore\Pdo\PdoSnapshotStore;
use Prooph\SnapshotStore\Snapshot;

class PdoSnapshotStoreTest extends TestCase
{
    /**
     * @var PdoSnapshotStore
     */
    private $snapshotStore;

    /**
     * @var PDO
     */
    private $connection;

    /**
     * @test
     */
    public function it_saves_and_reads()
    {
        $aggregateType = 'baz';
        $aggregateRoot = new \stdClass();
        $aggregateRoot->foo = 'bar';

        $time = (string) microtime(true);
        if (false === strpos($time, '.')) {
            $time .= '.0000';
        }

        $now = \DateTimeImmutable::createFromFormat('U.u', $time);

        $snapshot = new Snapshot($aggregateType, 'id', $aggregateRoot, 1, $now);

        $this->snapshotStore->save($snapshot);

        $snapshot = new Snapshot($aggregateType, 'id', $aggregateRoot, 2, $now);

        $this->snapshotStore->save($snapshot);

        $this->assertNull($this->snapshotStore->get($aggregateType, 'invalid'));

        $readSnapshot = $this->snapshotStore->get($aggregateType, 'id');

        $this->assertEquals($snapshot, $readSnapshot);

        $statement = $this->connection->prepare('SELECT * FROM snapshots');

        $statement->execute();
        $snapshots = $statement->fetchAll();

        $this->assertCount(1, $snapshots);
    }

    /**
     * @test
     */
    public function it_saves_multiple_snapshots_and_removes_them()
    {
        $aggregateRoot1 = new \stdClass();
        $aggregateRoot1->foo = 'bar';

        $aggregateRoot2 = ['foo' => 'baz'];

        $time = (string) microtime(true);
        if (false === strpos($time, '.')) {
            $time .= '.0000';
        }

        $now = \DateTimeImmutable::createFromFormat('U.u', $time);

        $snapshot1 = new Snapshot('object', 'id_one', $aggregateRoot1, 1, $now);

        $snapshot2 = new Snapshot('array', 'id_two', $aggregateRoot2, 2, $now);

        $snapshot3 = new Snapshot('array', 'id_three', $aggregateRoot2, 1, $now);

        $this->snapshotStore->save($snapshot1, $snapshot2, $snapshot3);

        $this->assertEquals($snapshot1, $this->snapshotStore->get('object', 'id_one'));
        $this->assertEquals($snapshot2, $this->snapshotStore->get('array', 'id_two'));
        $this->assertEquals($snapshot3, $this->snapshotStore->get('array', 'id_three'));

        $this->snapshotStore->removeAll('array');

        $this->assertEquals($snapshot1, $this->snapshotStore->get('object', 'id_one'));
        $this->assertNull($this->snapshotStore->get('array', 'id_two'));
        $this->assertNull($this->snapshotStore->get('array', 'id_three'));
    }

    /**
     * @test
     */
    public function it_returns_early_when_no_snapshots_given()
    {
        $pdo = $this->prophesize(PDO::class);
        $pdo->beginTransaction()->shouldNotBeCalled();

        $snapshotStore = new PdoSnapshotStore($pdo->reveal());

        $snapshotStore->save();
    }

    /**
     * @test
     */
    public function it_uses_custom_snapshot_table_map()
    {
        $this->createTable('bar');

        $aggregateType = 'foo';
        $aggregateRoot = new \stdClass();
        $aggregateRoot->foo = 'bar';
        $time = (string) microtime(true);

        if (false === strpos($time, '.')) {
            $time .= '.0000';
        }

        $now = \DateTimeImmutable::createFromFormat('U.u', $time);

        $snapshot = new Snapshot($aggregateType, 'id', $aggregateRoot, 1, $now);

        $this->snapshotStore->save($snapshot);

        $sql = <<<EOT
SELECT * from bar LIMIT 1;
EOT;

        $statement = $this->connection->prepare($sql);
        $statement->execute();

        $this->assertNotNull($statement->fetch(\PDO::FETCH_ASSOC));
    }

    protected function setUp(): void
    {
        $this->connection = TestUtil::getConnection();

        switch (TestUtil::getDatabaseVendor()) {
            case 'pdo_mysql':
                $this->connection->exec(file_get_contents(__DIR__ . '/../scripts/mysql_snapshot_table.sql'));
                break;
            case 'pdo_pgsql':
                $this->connection->exec(file_get_contents(__DIR__ . '/../scripts/postgres_snapshot_table.sql'));
                break;
            default:
                throw new \RuntimeException('Invalid database vendor');
        }

        $this->snapshotStore = new PdoSnapshotStore($this->connection, ['foo' => 'bar'], 'snapshots');
    }

    protected function tearDown(): void
    {
        $statement = $this->connection->prepare('TRUNCATE snapshots');
        $statement->execute();
        $statement = $this->connection->prepare('TRUNCATE bar');
        $statement->execute();
    }

    protected function createTable(string $name)
    {
        switch (TestUtil::getDatabaseVendor()) {
            case 'pdo_mysql':
                $sql = file_get_contents(__DIR__ . '/../scripts/mysql_snapshot_table.sql');
                break;
            case 'pdo_pgsql':
                $sql = file_get_contents(__DIR__ . '/../scripts/postgres_snapshot_table.sql');
                break;
            default:
                throw new \RuntimeException('Invalid database vendor');
        }

        $sql = str_replace('snapshots', $name, $sql);
        $this->connection->exec($sql);
    }
}
