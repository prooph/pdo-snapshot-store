<?php
/**
 * This file is part of the prooph/pdo-snapshot-store.
 * (c) 2016-2016 prooph software GmbH <contact@prooph.de>
 * (c) 2016-2016 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ProophTest\PDO\SnapshotStore;

use PDO;
use PHPUnit_Framework_TestCase as TestCase;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\Snapshot\Snapshot;
use Prooph\PDO\SnapshotStore\PDOSnapshotStore;

class PDOSnapshotStoreTest extends TestCase
{
    /**
     * @var PDOSnapshotStore
     */
    private $snapshotStore;

    /**
     * @var PDO
     */
    private $connection;

    /**
     * @test
     */
    public function it_saves_and_reads_using_mysql()
    {
        $aggregateType = AggregateType::fromString('baz');
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
    public function it_uses_custom_snapshot_table_map()
    {
        $this->createTable('bar');

        $aggregateType = AggregateType::fromString('foo');
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

        $this->snapshotStore = new PDOSnapshotStore($this->connection, ['foo' => 'bar'], 'snapshots');
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
