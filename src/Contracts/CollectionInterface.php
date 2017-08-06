<?php
/**
 * CollectionInterface.php
 * 7/30/17 - 5:02 PM
 *
 * PHP version 7
 *
 * @package    @package_name@
 * @author     Julien Duseyau <julien.duseyau@gmail.com>
 * @copyright  2017 Julien Duseyau
 * @license    https://opensource.org/licenses/MIT
 * @version    Release: @package_version@
 *
 * The MIT License (MIT)
 *
 * Copyright (c) Julien Duseyau <julien.duseyau@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Maduser\Minimal\Collections\Contracts;

/**
 * Interface CollectionInterface
 *
 * @package Maduser\Minimal\Collections
 */
interface CollectionInterface
{
    /**
     * @param      $obj
     * @param null $key
     *
     * @param bool $overwrite
     *
     * @return CollectionInterface
     */
    public function add(
        $obj,
        $key = null,
        $overwrite = false
    ) : CollectionInterface;

    /**
     * @param $key
     *
     * @throws InvalidKeyException
     */
    public function delete($key);

    /**
     * @param $key
     *
     * @return mixed
     * @throws InvalidKeyException
     */
    public function get($key);

    /**
     * @param null $key
     *
     * @return int
     */
    public function count($key = null);

    /**
     * @return bool
     */
    public function hasItems() : bool;

    /**
     * @param string $name
     * @param null   $else
     *
     * @return mixed
     */
    public function exists(string $name, $else = null);

    /**
     * @param \Closure $closure
     * @param bool     $keepKeys
     *
     * @return mixed
     */
    public function filter(\Closure $closure, $keepKeys = false);

    /**
     * @param $key
     *
     * @return mixed
     */
    public function extract($key);

    /**
     * @return mixed
     */
    public function first();

    /**
     * @return array
     */
    public function getArray();

    /**
     * Return the current element
     *
     * @link  http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current();

    /**
     * Move forward to next element
     *
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next();

    /**
     * Return the key of the current element
     *
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key();

    /**
     * Checks if current position is valid
     *
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *        Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid();

    /**
     * Rewind the Iterator to the first element
     *
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind();

    /**
     * @return mixed
     */
    public function toArray();
}