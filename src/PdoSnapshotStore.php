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

namespace Prooph\Pdo\SnapshotStore;

use PDO;
use Prooph\SnapshotStore\Snapshot;
use Prooph\SnapshotStore\SnapshotStore;

final class PdoSnapshotStore implements SnapshotStore
{
    /**
     * @var PDO
     */
    private $connection;

    /**
     * Custom sourceType to snapshot mapping
     *
     * @var array
     */
    private $snapshotTableMap;

    /**
     * @var string
     */
    private $defaultSnapshotTableName;

    public function __construct(
        PDO $connection,
        array $snapshotTableMap = [],
        string $defaultSnapshotTableName = 'snapshots'
    ) {
        $this->connection = $connection;
        $this->snapshotTableMap = $snapshotTableMap;
        $this->defaultSnapshotTableName = $defaultSnapshotTableName;
    }

    public function get(string $aggregateType, string $aggregateId): ?Snapshot
    {
        $table = $this->getTableName($aggregateType);

        $query = <<<EOT
SELECT * FROM $table WHERE aggregate_id = ? ORDER BY last_version DESC 
EOT;

        $statement = $this->connection->prepare($query);
        $statement->execute([$aggregateId]);

        $result = $statement->fetch(\PDO::FETCH_OBJ);

        if (! $result) {
            return null;
        }

        return new Snapshot(
            $aggregateType,
            $aggregateId,
            $this->unserializeAggregateRoot($result->aggregate_root),
            (int) $result->last_version,
            \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.u', $result->created_at, new \DateTimeZone('UTC'))
        );
    }

    public function save(Snapshot $snapshot): void
    {
        $table = $this->getTableName($snapshot->aggregateType());

        $this->connection->beginTransaction();

        $delete = <<<EOT
DELETE FROM $table WHERE aggregate_id = ?
EOT;

        $statement = $this->connection->prepare($delete);
        $statement->execute([
            $snapshot->aggregateId(),
        ]);

        $insert = <<<EOT
INSERT INTO $table (aggregate_id, aggregate_type, last_version, created_at, aggregate_root)
VALUES (?, ?, ?, ?, ?);
EOT;

        $statement = $this->connection->prepare($insert);
        $statement->execute([
            $snapshot->aggregateId(),
            $snapshot->aggregateType(),
            $snapshot->lastVersion(),
            $snapshot->createdAt()->format('Y-m-d\TH:i:s.u'),
            serialize($snapshot->aggregateRoot()),
        ]);

        $this->connection->commit();
    }

    private function getTableName(string $aggregateType): string
    {
        if (isset($this->snapshotTableMap[$aggregateType])) {
            $tableName = $this->snapshotTableMap[$aggregateType];
        } else {
            $tableName = $this->defaultSnapshotTableName;
        }

        return $tableName;
    }

    /**
     * @param string|resource $serialized
     * @return object
     */
    private function unserializeAggregateRoot($serialized)
    {
        if (is_resource($serialized)) {
            $serialized = stream_get_contents($serialized);
        }

        return unserialize($serialized);
    }
}