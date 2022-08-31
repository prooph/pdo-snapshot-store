<?php

/**
 * This file is part of prooph/pdo-snapshot-store.
 * (c) 2016-2022 Alexander Miertsch <kontakt@codeliner.ws>
 * (c) 2016-2022 Sascha-Oliver Prolic <saschaprolic@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Prooph\SnapshotStore\Pdo\Exception;

use RuntimeException as PHPRuntimeException;

class RuntimeException extends PHPRuntimeException
{
    public static function fromStatementErrorInfo(array $errorInfo): RuntimeException
    {
        return new self(
            \sprintf(
                "Error %s. \nError-Info: %s",
                $errorInfo[0],
                $errorInfo[2]
            )
        );
    }
}
