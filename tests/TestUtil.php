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

namespace ProophTest\SnapshotStore\Pdo;

use PDO;

abstract class TestUtil
{
    /**
     * List of URL schemes from a database URL and their mappings to driver.
     */
    private static $driverSchemeAliases = [
        'pdo_mysql' => 'mysql',
        'pdo_pgsql' => 'pgsql',
    ];

    private static $driverSchemeSeparators = [
        'pdo_mysql' => ';',
        'pdo_pgsql' => ' ',
    ];

    public static function getConnection(): PDO
    {
        $connectionParams = self::getConnectionParams();

        $separator = self::$driverSchemeSeparators[$connectionParams['driver']];

        $dsn = self::$driverSchemeAliases[$connectionParams['driver']] . ':';
        $dsn .= 'host=' . $connectionParams['host'] . $separator;
        $dsn .= 'port=' . $connectionParams['port'] . $separator;
        $dsn .= 'dbname=' . $connectionParams['dbname'] . $separator;
        $dsn = \rtrim($dsn);

        return new PDO($dsn, $connectionParams['user'], $connectionParams['password'], $connectionParams['options']);
    }

    public static function getDatabaseName(): string
    {
        if (! self::hasRequiredConnectionParams()) {
            throw new \RuntimeException('No connection params given');
        }

        return \getenv('db_name');
    }

    public static function getDatabaseVendor(): string
    {
        if (! self::hasRequiredConnectionParams()) {
            throw new \RuntimeException('No connection params given');
        }

        return \getenv('db_type');
    }

    public static function getConnectionParams(): array
    {
        if (! self::hasRequiredConnectionParams()) {
            throw new \RuntimeException('No connection params given');
        }

        return self::getSpecifiedConnectionParams();
    }

    private static function hasRequiredConnectionParams(): bool
    {
        $env = \getenv();

        return isset(
            $env['db_type'],
            $env['db_username'],
            $env['db_password'],
            $env['db_host'],
            $env['db_name'],
            $env['db_port']
        );
    }

    private static function getSpecifiedConnectionParams(): array
    {
        return [
            'driver' => \getenv('db_type'),
            'user' => \getenv('db_username'),
            'password' => \getenv('db_password'),
            'host' => \getenv('db_host'),
            'dbname' => \getenv('db_name'),
            'port' => \getenv('db_port'),
            'options' => [PDO::ATTR_ERRMODE => (int) \getenv('DB_ATTR_ERRMODE')],
        ];
    }
}
