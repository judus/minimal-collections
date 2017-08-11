<?php

namespace Maduser\Minimal\Collections\Tests;

use Maduser\Minimal\Collections\Collection;
use Maduser\Minimal\Collections\Exceptions\KeyInUseException;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function testAdd()
    {
        $collection = new Collection();
        $collection->add('dummy');

        $result = $collection->getArray();
        $expected = ['dummy'];

        $this->assertEquals($result, $expected);
    }

    public function testAddWithKey()
    {
        $collection = new Collection();
        $collection->add('dummy', 'test');

        $result = $collection->getArray();
        $expected = ['test' => 'dummy'];

        $this->assertEquals($result, $expected);
    }

    public function testAddWithKeyAndOverwrite()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test');
        $collection->add('dummy2', 'test', true);

        $result = $collection->getArray();
        $expected = ['test' => 'dummy2'];

        $this->assertEquals($result, $expected);
    }

    /**
     * @expectedException \Maduser\Minimal\Collections\Exceptions\KeyInUseException
     */
    public function testAddThrowsKeyExists()
    {
        $collection = new Collection();
        $collection->add('dummy', 'test');
        $collection->add('dummy', 'test');
    }

    /**
     * @expectedException \Maduser\Minimal\Collections\Exceptions\InvalidKeyException
     */
    public function testAddThrowsInvalidKeyWhenObject()
    {
        $dummy = new \stdClass();
        $collection = new Collection();
        $collection->add('dummy', $dummy);
    }

    /**
     * @expectedException \Maduser\Minimal\Collections\Exceptions\InvalidKeyException
     */
    public function testAddThrowsInvalidKeyWhenArray()
    {
        $collection = new Collection();
        $collection->add('dummy', ['dummy']);
    }

    /**
     * @expectedException \Maduser\Minimal\Collections\Exceptions\InvalidKeyException
     */
    public function testAddThrowsInvalidKeyWhenEmptyArray()
    {
        $collection = new Collection();
        $collection->add('dummy', []);
    }

    /**
     * @expectedException \Maduser\Minimal\Collections\Exceptions\InvalidKeyException
     */
    public function testValidateKeyWithArray()
    {
        $collection = new Collection();
        $collection->validateKey([]);
    }

    /**
     * @expectedException \Maduser\Minimal\Collections\Exceptions\InvalidKeyException
     */
    public function testValidateKeyWithObject()
    {
        $collection = new Collection();
        $collection->validateKey(new \stdClass());
    }

    public function testDelete()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test1');
        $collection->add('dummy2', 'test2');

        $collection->delete('test1');

        $result = $collection->getArray();
        $expected = ['test2' => 'dummy2'];

        $this->assertEquals($result, $expected);
    }

    public function testGet()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test1');
        $collection->add('dummy2', 'test2');
        $collection->add('dummy3', 'test3');

        $value = $collection->get('test2');

        $this->assertEquals($value, 'dummy2');
    }

    /**
     * @expectedException \Maduser\Minimal\Collections\Exceptions\InvalidKeyException
     */
    public function testGetThrowsInvalidKey()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test1');
        $collection->add('dummy2', 'test2');
        $collection->add('dummy3', 'test3');

        $collection->get('dummy');
    }

    public function testCount()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test1');
        $collection->add('dummy2', 'test2');
        $collection->add('dummy3', 'test3');

        $result = $collection->count();

        $this->assertEquals($result, 3);
    }

    public function testHasItemsIsTrue()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test1');

        $this->assertTrue($collection->hasItems());
    }

    public function testHasItemsIsFalse()
    {
        $collection = new Collection();

        $this->assertFalse($collection->hasItems());
    }

    public function testExistsHasValue()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test1');

        $this->assertEquals($collection->exists('test1'), 'dummy1');
    }

    public function testExistsIsNull()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test1');

        $this->assertNull($collection->exists('dummy'));
    }

    public function testFilter()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test1');
        $collection->add('dummy2', 'test2');
        $collection->add('dummy3', 'test3');

        $newCollection = $collection->filter(function ($value, $key) {
            return $value != 'dummy2';
        });

        $collectionArray = $newCollection->getArray();
        $assertArray = ['dummy2'];

        $this->assertEquals($collectionArray, $assertArray);
    }

    public function testFilterWithKeepKeys()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test1');
        $collection->add('dummy2', 'test2');
        $collection->add('dummy3', 'test3');

        $newCollection = $collection->filter(function ($value, $key) {
            return $value != 'dummy2';
        }, true);

        $collectionArray = $newCollection->getArray();
        $assertArray = ['test2' => 'dummy2'];

        $this->assertEquals($collectionArray, $assertArray);
    }

    public function testExtractFromArray()
    {
        $collection = new Collection();
        $collection->add(['key1' => 'dummy1.1', 'key2' => 'dummy1.2'], 'test1');
        $collection->add(['key1' => 'dummy2.1', 'key2' => 'dummy2.2'], 'test2');
        $collection->add(['key1' => 'dummy3.1', 'key2' => 'dummy3.2'], 'test3');

        $result = $collection->extract('key2');
        $expected = ['dummy1.2', 'dummy2.2', 'dummy3.2'];

        $this->assertEquals($result, $expected);
    }

    public function testToExtractFromCollection()
    {
        $collection = new Collection();
        $collection->add(new Collection([
            'key1' => 'dummy1.1',
            'key2' => 'dummy1.2'
        ]), 'test1');
        $collection->add(new Collection([
            'key1' => 'dummy2.1',
            'key2' => 'dummy2.2'
        ]), 'test2');
        $collection->add(new Collection([
            'key1' => 'dummy3.1',
            'key2' => 'dummy3.2'
        ]), 'test3');

        $result = $collection->extract('key2');
        $expected = ['dummy1.2', 'dummy2.2', 'dummy3.2'];

        $this->assertEquals($result, $expected);
    }


    public function testToExtractFromObject()
    {
        $obj1 = new \stdClass();
        $obj1->key1 = 'dummy1.1';
        $obj1->key2 = 'dummy1.2';

        $obj2 = new \stdClass();
        $obj2->key1 = 'dummy2.1';
        $obj2->key2 = 'dummy2.2';

        $obj3 = new \stdClass();
        $obj3->key1 = 'dummy3.1';
        $obj3->key2 = 'dummy3.2';

        $collection = new Collection();
        $collection->add($obj1, 'test1');
        $collection->add($obj2, 'test2');
        $collection->add($obj3, 'test3');

        $result = $collection->extract('key2');
        $expected = ['dummy1.2', 'dummy2.2', 'dummy3.2'];

        $this->assertEquals($result, $expected);
    }


    public function testFirst()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test1');
        $collection->add('dummy2', 'test2');
        $collection->add('dummy3', 'test3');

        $result = $collection->first();
        $expected = 'dummy1';

        $this->assertEquals($result, $expected);
    }

    public function testGetArray()
    {
        $collection = new Collection();
        $collection->add('dummy1', 'test1');
        $collection->add('dummy2', 'test2');
        $collection->add('dummy3', 'test3');

        $result = $collection->getArray();
        $expected = [
            'test1' => 'dummy1',
            'test2' => 'dummy2',
            'test3' => 'dummy3'
        ];

        $this->assertEquals($result, $expected);
    }

    public function testToArray()
    {
        $collection = new Collection();
        $collection->add(new Collection([
            'key1' => 'dummy1.1',
            'key2' => 'dummy1.2'
        ]), 'test1');
        $collection->add(new Collection([
            'key1' => 'dummy2.1',
            'key2' => 'dummy2.2'
        ]), 'test2');
        $collection->add(new Collection([
            'key1' => 'dummy3.1',
            'key2' => 'dummy3.2'
        ]), 'test3');

        $result = $collection->toArray();
        $expected = [
            'test1' => ['key1' => 'dummy1.1', 'key2' => 'dummy1.2'],
            'test2' => ['key1' => 'dummy2.1', 'key2' => 'dummy2.2'],
            'test3' => ['key1' => 'dummy3.1', 'key2' => 'dummy3.2']
        ];

        $this->assertEquals($result, $expected);
    }

    public function testToString()
    {
        $collection = new Collection();
        $collection->add(new Collection([
            'key1' => 'dummy1.1',
            'key2' => 'dummy1.2'
        ]), 'test1');
        $collection->add(new Collection([
            'key1' => 'dummy2.1',
            'key2' => 'dummy2.2'
        ]), 'test2');
        $collection->add(new Collection([
            'key1' => 'dummy3.1',
            'key2' => 'dummy3.2'
        ]), 'test3');

        $result = (string)$collection;
        $expected = [
            'test1' => ['key1' => 'dummy1.1', 'key2' => 'dummy1.2'],
            'test2' => ['key1' => 'dummy2.1', 'key2' => 'dummy2.2'],
            'test3' => ['key1' => 'dummy3.1', 'key2' => 'dummy3.2']
        ];

        $this->assertEquals($result, json_encode($expected));
    }

}
