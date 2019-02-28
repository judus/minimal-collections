<?php

namespace Maduser\Minimal\Collections;

use Maduser\Minimal\Collections\Contracts\AbstractCollectionInterface;
use Maduser\Minimal\Collections\Contracts\CollectionApiInterface;
use Maduser\Minimal\Collections\Exceptions\InvalidKeyException;
use Maduser\Minimal\Collections\Exceptions\KeyInUseException;
use Maduser\Minimal\Collections\Exceptions\UnacceptableTypeException;

class AbstractCollectionApi implements AbstractCollectionInterface
{
    use IteratorTrait;
    use ArrayAccessTrait;

    /**
     * @var array
     */
    protected $acceptedTypes = [];

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @return array
     */
    public function getAcceptedTypes(): array
    {
        return $this->acceptedTypes;
    }

    /**
     * @param array $acceptedTypes
     *
     * @return AbstractCollectionInterface
     */
    public function setAcceptedTypes(array $acceptedTypes): AbstractCollectionInterface
    {
        $this->acceptedTypes = $acceptedTypes;

        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     *
     * @return AbstractCollectionInterface
     * @throws InvalidKeyException
     * @throws KeyInUseException
     * @throws UnacceptableTypeException
     * @throws \ReflectionException
     */
    public function setItems(array $items): AbstractCollectionInterface
    {
        foreach ($items as $key => $value) {
            $this->add($value, $key);
        }

        return $this;
    }

    /**
     * @param      $value
     * @param null $key
     *
     * @param bool $overwrite
     *
     * @return AbstractCollectionInterface
     * @throws InvalidKeyException
     * @throws KeyInUseException
     * @throws UnacceptableTypeException
     * @throws \ReflectionException
     */
    public function add(
        $value,
        $key = null,
        $overwrite = false
    ): AbstractCollectionInterface {
        $this->validateKey($key);

        if (count($this->getAcceptedTypes()) > 0) {
            $this->validateType($value);
        }

        if ($key == null) {
            $this->items[] = $value;
        } else {
            if (isset($this->items[$key]) && !$overwrite) {
                throw new KeyInUseException("Collection key '" . $key . "' is already in use.");
            } else {
                $this->items[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * @param $key
     *
     * @throws InvalidKeyException
     */
    public function delete($key): AbstractCollectionInterface
    {
        $this->validateKey($key);

        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        } else {
            throw new InvalidKeyException("Collection key '" . $key . "' does not exist.");
        }

        return $this;
    }

    /**
     * @param $key
     *
     * @throws InvalidKeyException
     */
    public function validateKey($key)
    {
        if (is_array($key)) {
            throw new InvalidKeyException("Can not use array as key name.");
        }

        if (is_object($key)) {
            throw new InvalidKeyException("Can not use object as key name.");
        }

        if (!is_null($key) && (!is_string($key) || is_int($key))) {
            throw new InvalidKeyException("Collection key '" . $key . "' is not a valid key name.");
        }
    }

    /**
     * @param $value
     *
     * @return bool
     * @throws UnacceptableTypeException
     * @throws \ReflectionException
     */
    public function validateType($value)
    {
        if (! is_object($value) && ! is_string($value)) {
            throw new UnacceptableTypeException("Only subclasses of one of \"" .
                implode('", "',
                    $this->getAcceptedTypes()) . "\", the given value is not an object");
        }

        foreach ($this->getAcceptedTypes() as $type) {
            $reflection = new \ReflectionClass($value);
            if ($reflection->isSubclassOf($type)) {
                return true;
            }
        }

        $className = is_string($value) ? $value : get_class($value);

        throw new UnacceptableTypeException("Only subclasses of one of \"" .
            implode('", "',
                $this->getAcceptedTypes()) . "\", given \"" . $className . "\" ");
    }

    /**
     * @param $key
     *
     * @return mixed
     * @throws InvalidKeyException
     */
    public function get($key)
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        } else {
            throw new InvalidKeyException("Collection key '" . $key . "' does not exist.");
        }
    }

    /**
     * @param null $key
     *
     * @return int
     */
    public function count($key = null)
    {
        if (!is_null($key) && isset($this->items[$key])) {
            return count($this->items[$key]);
        } else {
            return count($this->items);
        }
    }

    /**
     * @return bool
     */
    public function hasItems(): bool
    {
        return $this->count() > 0;
    }

    /**
     * @param $offset
     *
     * @return bool
     */
    public function has($offset)
    {
        return $this->offsetExists($offset);
    }

    /**
     * @param string $name
     * @param null   $else
     *
     * @return mixed|null
     */
    public function exists(string $name, $else = null)
    {
        return isset($this->items[$name]) ?
            $this->items[$name] : $else;
    }

    /**
     * @param \Closure $closure
     *
     * @return AbstractCollectionInterface
     */
    public function each(\Closure $closure): AbstractCollectionInterface
    {
        $container = new static();

        $index = -1;
        foreach ($this->items as $key => $item) {
            $container->add($closure($key, $item, $index++), $key);
        }

        return $container;
    }

    /**
     * @param \Closure $closure
     * @param bool     $keepKeys
     *
     * @return Collection
     * @throws InvalidKeyException
     * @throws KeyInUseException
     * @throws UnacceptableTypeException
     * @throws \ReflectionException
     */
    public function filter(\Closure $closure, $keepKeys = false): AbstractCollectionInterface
    {
        $class = get_called_class();
        $collection = new $class();

        $i = 0;
        foreach ($this->items as $key => $value) {
            if (!$closure($value, $key, $i++)) {
                $keepKeys || $key = null;
                $collection->add($value, $key);
            }
        }

        return $collection;
    }

    /**
     * @param $key
     *
     * @return array
     * @throws InvalidKeyException
     */
    public function extract($key)
    {
        $extracted = [];

        foreach ($this->items as $item) {
            foreach (func_get_args() as $key) {
                if ($item instanceof AbstractCollectionInterface) {
                    $extracted[] = $item->get($key);
                } else {
                    if (is_object($item)) {
                        $extracted[] = $item->{$key};
                    } else {
                        $extracted[] = $item[$key];
                    }
                }
            }
        }

        return $extracted;
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return reset($this->items);
    }

    /**
     * @return array
     */
    public function getArray(): array
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $items = [];
        foreach ($this->items as $key => $item) {
            if (is_object($item) && method_exists($item, 'toArray')) {
                $items[$key] = $item->toArray();
            } else {
                $items[$key] = $item;
            }
        }

        return $items;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $items = [];
        foreach ($this->items as $key => $item) {
            if ($item instanceof CollectionApiInterface) {
                $items[$key] = $item->getArray();
            } else {
                if (is_object($item) && method_exists($item, 'toArray')) {
                    $items[$key] = $item->toArray();
                } else {
                    $items[$key] = $item;
                }
            }
        }

        return json_encode($items);
    }

}