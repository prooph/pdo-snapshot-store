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

namespace Prooph\SnapshotStore\Pdo;

final class DefaultSerializerStrategy implements SerializerStrategy
{
    /**
     * callable
     */
    private $serializer = 'serialize';

    /**
     * callable
     */
    private $deserialize = 'unserialize';

    public function __construct(?callable $serializer, ?callable $deserialize)
    {
        if ($serializer && $deserialize) {
            $this->serializer  = $serializer;
            $this->deserialize = $deserialize;
        }
    }

    /**
     * @param object|array $data
     * @return string
     */
    public function serialize($data): string
    {
        return call_user_func_array($this->serializer, [$data]);
    }

    /**
     * @param string $serialized
     * @return object|array
     */
    public function deserialize(string $serialized)
    {
        return call_user_func_array($this->deserialize, [$serialized]);
    }
}
