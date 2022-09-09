# Prooph PDO Snapshot Store

[![Continuous Integration](https://github.com/prooph/pdo-snapshot-store/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/prooph/pdo-snapshot-store/actions/workflows/continuous-integration.yml)
[![Coverage Status](https://coveralls.io/repos/prooph/pdo-snapshot-store/badge.svg?branch=master&service=github)](https://coveralls.io/github/prooph/pdo-snapshot-store?branch=master)
[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/prooph/improoph)

## Important

This library will receive support until December 31, 2019 and will then be deprecated.

For further information see the official announcement here: [https://www.sasaprolic.com/2018/08/the-future-of-prooph-components.html](https://www.sasaprolic.com/2018/08/the-future-of-prooph-components.html)

## Overview

PDO implementation of snapshot store

## Installation

You can install prooph/pdo-snapshot-store via composer by adding `"prooph/pdo-snapshot-store": "^1.0"` as requirement to your composer.json.

## Upgrade

If you come from version 1.4.0 you are advised to manually update the table schema to fix an omitted primary key. You can issue the following statements or drop the snapshot table, recreate them from the provided scripts and restart projections.

MySql

```sql
ALTER TABLE `snapshots` DROP INDEX `ix_aggregate_id`, ADD PRIMARY KEY(`aggregate_id`);
```

Postgres

```sql
ALTER TABLE "snapshots" DROP CONSTRAINT "snapshots_aggregate_id_key", ADD PRIMARY KEY ("aggregate_id");
```

## Support

- Ask questions on Stack Overflow tagged with [#prooph](https://stackoverflow.com/questions/tagged/prooph).
- File issues at [https://github.com/prooph/pdo-snapshot-store/issues](https://github.com/prooph/pdo-snapshot-store/issues).
- Say hello in the [prooph gitter](https://gitter.im/prooph/improoph) chat.

## Contribute

Please feel free to fork and extend existing or add new plugins and send a pull request with your changes!
To establish a consistent code quality, please provide unit tests for all your changes and may adapt the documentation.

## License

Released under the [New BSD License](LICENSE).
