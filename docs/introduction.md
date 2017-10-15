# Overview

PDO implementation of snapshot store

## Installation

```bash
composer require prooph/pdo-snapshot-store
```

## Setup

The PDO SnapshotStore is currently tested with 3 backends, MariaDB, MySQL and Postgres.

In order to use it, you need have a database and create one (or multitple) snapshot tables.

For MySQL and MariaDB see: `scripts/mysql_snapshot_table.sql`
For Postgres see: `scripts/postgres_snapshot_table.sql`

## Disable transaction handling

You can configure the snapshot store to disable transaction handling completely. In order to do this, set the last parameter
in the constructor to true (or configure your interop config factory accordingly, key is `disable_transaction_handling`).

Enabling this feature will disable all transaction handling and you have to take care yourself to start, commit and rollback
transactions.

Note: This could lead to problems using the snapshot store, if you did not manage to handle the transaction handling accordingly.
This is your problem and we will not provide any support for problems you encounter while doing so.
