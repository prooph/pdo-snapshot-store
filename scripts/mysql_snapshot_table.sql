CREATE TABLE `snapshots` (
  `aggregate_id` VARCHAR(150) NOT NULL,
  `aggregate_type` VARCHAR(150) NOT NULL,
  `last_version` INT(11) NOT NULL,
  `created_at` CHAR(26) NOT NULL,
  `aggregate_root` BLOB,
  PRIMARY KEY (`aggregate_id`),
  UNIQUE KEY `ix_aggregate_id` (`aggregate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
