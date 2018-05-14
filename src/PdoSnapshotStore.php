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

namespace Prooph\SnapshotStore\Pdo;

use PDO;
use PDOException;
use Prooph\SnapshotStore\CallbackSerializer;
use Prooph\SnapshotStore\Pdo\Exception\RuntimeException;
use Prooph\SnapshotStore\Serializer;
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

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var bool
     */
    private $disableTransactionHandling;

    /**
     * @var string
     */
    private $vendor;

    public function __construct(
        PDO $connection,
        array $snapshotTableMap = [],
        string $defaultSnapshotTableName = 'snapshots',
        Serializer $serializer = null,
        bool $disableTransactionHandling = false
    ) {
        $this->connection = $connection;
        $this->snapshotTableMap = $snapshotTableMap;
        $this->defaultSnapshotTableName = $defaultSnapshotTableName;
        $this->serializer = $serializer ?: new CallbackSerializer(null, null);
        $this->disableTransactionHandling = $disableTransactionHandling;
        $this->vendor = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    public function get(string $aggregateType, string $aggregateId): ?Snapshot
    {
        $table = $this->getTableName($aggregateType);

        $query = <<<EOT
SELECT * FROM $table WHERE aggregate_id = ? ORDER BY last_version DESC 
EOT;

        $statement = $this->connection->prepare($query);

        try {
            $statement->execute([$aggregateId]);
        } catch (PDOException $exception) {
            // ignore and check error code
        }

        if ($statement->errorCode() !== '00000') {
            throw RuntimeException::fromStatementErrorInfo($statement->errorInfo());
        }

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

    public function save(Snapshot ...$snapshots): void
    {
        if (empty($snapshots)) {
            return;
        }

        $deletes = [];
        $inserts = [];

        foreach ($snapshots as $snapshot) {
            $deletes[$this->getTableName($snapshot->aggregateType())][] = $snapshot->aggregateId();
            $inserts[$this->getTableName($snapshot->aggregateType())][] = $snapshot;
        }

        $statements = [];

        foreach ($deletes as $table => $aggregateIds) {
            $ids = implode(', ', array_fill(0, count($aggregateIds), '?'));
            $deleteSql = <<<EOT
DELETE FROM $table where aggregate_id IN ($ids);
EOT;
            $statement = $this->connection->prepare($deleteSql);
            foreach ($aggregateIds as $position => $aggregateId) {
                $statement->bindValue($position + 1, $aggregateId);
            }

            $statements[] = $statement;
        }

        foreach ($inserts as $table => $snapshots) {
            $allPlaces = implode(', ', array_fill(0, count($snapshots), '(?, ?, ?, ?, ?)'));
            $insertSql = <<<EOT
INSERT INTO $table (aggregate_id, aggregate_type, last_version, created_at, aggregate_root)
VALUES $allPlaces
EOT;
            $statement = $this->connection->prepare($insertSql);
            foreach ($snapshots as $index => $snapshot) {
                $position = $index * 5;
                $statement->bindValue(++$position, $snapshot->aggregateId());
                $statement->bindValue(++$position, $snapshot->aggregateType());
                $statement->bindValue(++$position, $snapshot->lastVersion(), PDO::PARAM_INT);
                $statement->bindValue(++$position, $snapshot->createdAt()->format('Y-m-d\TH:i:s.u'));
                $statement->bindValue(++$position, $this->serializer->serialize($snapshot->aggregateRoot()), PDO::PARAM_LOB);
            }
            $statements[] = $statement;
        }

        if (! $this->disableTransactionHandling) {
            $this->connection->beginTransaction();
        }

        try {
            foreach ($statements as $statement) {
                $statement->execute();
            }
        } catch (PDOException $exception) {
            if (! $this->disableTransactionHandling) {
                $this->connection->rollBack();
            }

            throw RuntimeException::fromStatementErrorInfo($statement->errorInfo());
        }

        if (! $this->disableTransactionHandling) {
            $this->connection->commit();
        }
    }

    public function removeAll(string $aggregateType): void
    {
        $table = $this->getTableName($aggregateType);

        $sql = <<<SQL
DELETE FROM $table WHERE aggregate_type = ?;
SQL;

        $statement = $this->connection->prepare($sql);

        if (! $this->disableTransactionHandling) {
            $this->connection->beginTransaction();
        }

        try {
            $statement->execute([$aggregateType]);
        } catch (PDOException $exception) {
            // ignore and check error code
        }

        if ($statement->errorCode() !== '00000') {
            if (! $this->disableTransactionHandling) {
                $this->connection->rollBack();
            }

            throw RuntimeException::fromStatementErrorInfo($statement->errorInfo());
        }

        if (! $this->disableTransactionHandling) {
            $this->connection->commit();
        }
    }

    private function getTableName(string $aggregateType): string
    {
        if (isset($this->snapshotTableMap[$aggregateType])) {
            $tableName = $this->snapshotTableMap[$aggregateType];
        } else {
            $tableName = $this->defaultSnapshotTableName;
        }

        switch ($this->vendor) {
            case 'pgsql':
                return '"'.$tableName.'"';
            default:
                return "`$tableName`";
        }
    }

    /**
     * @param string|resource $serialized
     * @return object|array
     */
    private function unserializeAggregateRoot($serialized)
    {
        if (is_resource($serialized)) {
            $serialized = stream_get_contents($serialized);
        }

        return $this->serializer->unserialize($serialized);
    }
}
