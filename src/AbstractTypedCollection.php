<?php

namespace Maduser\Minimal\Collections;

use Maduser\Minimal\Collections\Contracts\AbstractCollectionInterface;
use Maduser\Minimal\Collections\Contracts\TypedCollectionInterface;
use Maduser\Minimal\Collections\Exceptions\InvalidKeyException;
use Maduser\Minimal\Collections\Exceptions\KeyInUseException;
use Maduser\Minimal\Collections\Exceptions\UnacceptableTypeException;

class AbstractTypedCollection extends AbstractCollectionApi implements TypedCollectionInterface
{
    /**
     * @param array|null $items
     *
     * @return mixed
     */
    public static function create(array $items = null): TypedCollectionInterface
    {
        $class = get_called_class();

        return new $class($items);
    }

    /**
     * Collection constructor.
     *
     * @param array $items
     *
     * @throws InvalidKeyException
     * @throws KeyInUseException
     * @throws UnacceptableTypeException
     * @throws \ReflectionException
     */
    public function __construct(array $items = null)
    {
        is_null($items) || $this->setItems($items);
    }
}