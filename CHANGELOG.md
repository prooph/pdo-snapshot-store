# Changelog

## [v1.6.0](https://github.com/prooph/pdo-snapshot-store/tree/v1.6.0)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.5.2...v1.6.0)

**Implemented enhancements:**

- dropped travis in favor of github actions via laminas/laminas-continu… [\#33](https://github.com/prooph/pdo-snapshot-store/pull/33) ([basz](https://github.com/basz))
- php8 compatibility [\#32](https://github.com/prooph/pdo-snapshot-store/pull/32) ([basz](https://github.com/basz))

**Merged pull requests:**

- Change copyright [\#31](https://github.com/prooph/pdo-snapshot-store/pull/31) ([codeliner](https://github.com/codeliner))

## [v1.5.2](https://github.com/prooph/pdo-snapshot-store/tree/v1.5.2) (2019-04-26)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.5.1...v1.5.2)

**Implemented enhancements:**

- Correctly escape schema namespaced table name in the PostgreSQL. [\#30](https://github.com/prooph/pdo-snapshot-store/pull/30) ([ghettovoice](https://github.com/ghettovoice))

**Closed issues:**

- PdoSnapshotStoreFactory is trying to modificate 'config' object [\#28](https://github.com/prooph/pdo-snapshot-store/issues/28)
- Can't stop snapshotter when it's running [\#26](https://github.com/prooph/pdo-snapshot-store/issues/26)

**Merged pull requests:**

- remove docheader script [\#29](https://github.com/prooph/pdo-snapshot-store/pull/29) ([basz](https://github.com/basz))
- Update cs headers [\#27](https://github.com/prooph/pdo-snapshot-store/pull/27) ([basz](https://github.com/basz))

## [v1.5.1](https://github.com/prooph/pdo-snapshot-store/tree/v1.5.1) (2018-05-16)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.5.0...v1.5.1)

**Fixed bugs:**

- Quote table names in shapshot store queries [\#25](https://github.com/prooph/pdo-snapshot-store/pull/25) ([fritz-gerneth](https://github.com/fritz-gerneth))
- adds PKs to schema's [\#24](https://github.com/prooph/pdo-snapshot-store/pull/24) ([basz](https://github.com/basz))

**Merged pull requests:**

- Don't remove deprecated config option [\#22](https://github.com/prooph/pdo-snapshot-store/pull/22) ([sandrokeil](https://github.com/sandrokeil))
- Issues/19 [\#21](https://github.com/prooph/pdo-snapshot-store/pull/21) ([basz](https://github.com/basz))

## [v1.5.0](https://github.com/prooph/pdo-snapshot-store/tree/v1.5.0) (2018-04-15)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.4.0...v1.5.0)

**Implemented enhancements:**

- Consistent connection key [\#19](https://github.com/prooph/pdo-snapshot-store/issues/19)

**Fixed bugs:**

- snapshot have no index \(postgres\) [\#23](https://github.com/prooph/pdo-snapshot-store/issues/23)

**Closed issues:**

- snapshots are created over and over again Postgres [\#20](https://github.com/prooph/pdo-snapshot-store/issues/20)

## [v1.4.0](https://github.com/prooph/pdo-snapshot-store/tree/v1.4.0) (2017-12-17)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.3.0...v1.4.0)

**Implemented enhancements:**

- test php 7.2 on travis [\#18](https://github.com/prooph/pdo-snapshot-store/pull/18) ([prolic](https://github.com/prolic))

**Merged pull requests:**

- Restructure docs [\#17](https://github.com/prooph/pdo-snapshot-store/pull/17) ([codeliner](https://github.com/codeliner))

## [v1.3.0](https://github.com/prooph/pdo-snapshot-store/tree/v1.3.0) (2017-07-30)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.2.0...v1.3.0)

**Implemented enhancements:**

- Flag / Method to turn off transaction handling [\#15](https://github.com/prooph/pdo-snapshot-store/issues/15)
- disable transaction handling [\#16](https://github.com/prooph/pdo-snapshot-store/pull/16) ([prolic](https://github.com/prolic))

## [v1.2.0](https://github.com/prooph/pdo-snapshot-store/tree/v1.2.0) (2017-05-30)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.1.2...v1.2.0)

**Implemented enhancements:**

- Catch PDOExceptions and check error codes [\#14](https://github.com/prooph/pdo-snapshot-store/pull/14) ([dragosprotung](https://github.com/dragosprotung))

## [v1.1.2](https://github.com/prooph/pdo-snapshot-store/tree/v1.1.2) (2017-05-12)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.1.1...v1.1.2)

**Fixed bugs:**

- Fixed aggregate root serialized value binding [\#13](https://github.com/prooph/pdo-snapshot-store/pull/13) ([dragosprotung](https://github.com/dragosprotung))

## [v1.1.1](https://github.com/prooph/pdo-snapshot-store/tree/v1.1.1) (2017-04-06)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.1.0...v1.1.1)

**Merged pull requests:**

- Missed a typo [\#12](https://github.com/prooph/pdo-snapshot-store/pull/12) ([basz](https://github.com/basz))

## [v1.1.0](https://github.com/prooph/pdo-snapshot-store/tree/v1.1.0) (2017-04-06)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.0.0...v1.1.0)

**Merged pull requests:**

- Add flexible serializer [\#11](https://github.com/prooph/pdo-snapshot-store/pull/11) ([basz](https://github.com/basz))

## [v1.0.0](https://github.com/prooph/pdo-snapshot-store/tree/v1.0.0) (2017-03-30)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.0.0-beta2...v1.0.0)

**Implemented enhancements:**

- Change SnapshotStore Interface [\#5](https://github.com/prooph/pdo-snapshot-store/issues/5)
- Use camel-case PDO =\> Pdo [\#3](https://github.com/prooph/pdo-snapshot-store/issues/3)
- remove connection options setup in factory, add docs [\#10](https://github.com/prooph/pdo-snapshot-store/pull/10) ([prolic](https://github.com/prolic))
- Add possibility to remove all snapshots by aggregate type [\#8](https://github.com/prooph/pdo-snapshot-store/pull/8) ([prolic](https://github.com/prolic))
- change snapshot store interfaces [\#6](https://github.com/prooph/pdo-snapshot-store/pull/6) ([prolic](https://github.com/prolic))
- new snapshot store repo [\#4](https://github.com/prooph/pdo-snapshot-store/pull/4) ([prolic](https://github.com/prolic))
- simplify query [\#2](https://github.com/prooph/pdo-snapshot-store/pull/2) ([prolic](https://github.com/prolic))
- add implementation and tests [\#1](https://github.com/prooph/pdo-snapshot-store/pull/1) ([prolic](https://github.com/prolic))

**Merged pull requests:**

- fix namespace organization [\#9](https://github.com/prooph/pdo-snapshot-store/pull/9) ([prolic](https://github.com/prolic))
- update to use psr\container [\#7](https://github.com/prooph/pdo-snapshot-store/pull/7) ([basz](https://github.com/basz))

## [v1.0.0-beta2](https://github.com/prooph/pdo-snapshot-store/tree/v1.0.0-beta2) (2017-01-12)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/v1.0.0-beta1...v1.0.0-beta2)

## [v1.0.0-beta1](https://github.com/prooph/pdo-snapshot-store/tree/v1.0.0-beta1) (2016-12-13)

[Full Changelog](https://github.com/prooph/pdo-snapshot-store/compare/e1159674fc127c5a9463802ce35c07c22f14734f...v1.0.0-beta1)



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
