<?php namespace Maduser\Minimal\Collections;

use Maduser\Minimal\Collections\Contracts\AbstractCollectionInterface;
use Maduser\Minimal\Collections\Contracts\CollectionInterface;
use Maduser\Minimal\Collections\Exceptions\InvalidKeyException;
use Maduser\Minimal\Collections\Exceptions\KeyInUseException;
use Maduser\Minimal\Collections\Exceptions\UnacceptableTypeException;

/**
 * Class Collection
 *
 * @package Maduser\Minimal\Collections
 */
class Collection extends AbstractCollection
{
    public function setItems(array $items): AbstractCollectionInterface
    {
        $this->items = $items;
        return $this;
    }

}