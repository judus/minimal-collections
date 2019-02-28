<?php

namespace Maduser\Minimal\Collections;

use Maduser\Minimal\Collections\Contracts\AbstractCollectionInterface;
use Maduser\Minimal\Collections\Contracts\CollectionInterface;
use Maduser\Minimal\Collections\Exceptions\InvalidKeyException;
use Maduser\Minimal\Collections\Exceptions\KeyInUseException;
use Maduser\Minimal\Collections\Exceptions\UnacceptableTypeException;

class AbstractCollection extends AbstractCollectionApi implements CollectionInterface
{
    /**
     * @param array|null $items
     * @param array|null $acceptedTypes
     *
     * @return mixed
     */
    public static function create(
        array $items = null,
        array $acceptedTypes = null
    ): CollectionInterface {
        $class = get_called_class();

        return new $class($items, $acceptedTypes);
    }

    /**
     * Collection constructor.
     *
     * @param array $items
     * @param array $acceptedTypes
     *
     * @throws InvalidKeyException
     * @throws KeyInUseException
     * @throws UnacceptableTypeException
     * @throws \ReflectionException
     */
    public function __construct(
        array $items = null,
        array $acceptedTypes = null
    ) {
        is_null($acceptedTypes) || $this->setAcceptedTypes($acceptedTypes);
        is_null($items) || $this->setItems($items);
    }
}