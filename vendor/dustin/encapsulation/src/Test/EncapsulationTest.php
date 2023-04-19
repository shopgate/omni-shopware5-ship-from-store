<?php

namespace Dustin\Encapsulation\Test;

use Dustin\Encapsulation\Container;
use Dustin\Encapsulation\Encapsulation;
use Dustin\Encapsulation\Exception\NotAllowedFieldException;
use Dustin\Encapsulation\Exception\NotAnArrayException;
use PHPUnit\Framework\TestCase;

class AllowedFieldsEncapsulation extends Encapsulation
{
    public function getAllowedFields(): ?array
    {
        return ['foo', 'bar'];
    }
}

class EncapsulationTest extends TestCase
{
    public function testInitialization(): Encapsulation
    {
        $encapsulation = new Encapsulation(['foo' => 'foo']);
        $data = $encapsulation->toArray();

        $this->assertSame($data, ['foo' => 'foo']);

        return $encapsulation;
    }

    /**
     * @depends testInitialization
     */
    public function testSetAndGet(Encapsulation $encapsulation)
    {
        $encapsulation->set('bar', 'bar');

        $foo = $encapsulation->get('foo');
        $bar = $encapsulation->get('bar');

        $this->assertSame($foo, 'foo');
        $this->assertSame($bar, 'bar');
        $this->assertSame(
            $encapsulation->toArray(),
            ['foo' => 'foo', 'bar' => 'bar']
        );
    }

    public function testHasUnsetAndGetFields()
    {
        $encapsulation = new Encapsulation(['foo' => 'foo', 'bar' => 'bar']);

        $this->assertTrue($encapsulation->has('foo'));

        $encapsulation->unset('foo');
        $this->assertFalse($encapsulation->has('foo'));

        $encapsulation->set('foo', null);
        $this->assertTrue($encapsulation->has('foo'));

        $this->assertSame(
            $encapsulation->getFields(),
            ['bar', 'foo']
        );
    }

    public function testSetListAndGetList()
    {
        $encapsulation = new Encapsulation(['foo' => 'foo', 'bar' => 'bar', 'alice' => 'bob']);
        $fooAndBar = $encapsulation->getList(['foo', 'bar']);

        $this->assertSame($fooAndBar, ['foo' => 'foo', 'bar' => 'bar']);

        $encapsulation->setList([
            'foo' => 'fooFoo',
            'hello' => 'world',
        ]);

        $data = $encapsulation->toArray();

        $this->assertSame(
            $data,
            [
                'foo' => 'fooFoo',
                'bar' => 'bar',
                'alice' => 'bob',
                'hello' => 'world',
            ]
        );
    }

    public function testAdd()
    {
        $encapsulation = new Encapsulation();

        $encapsulation->add('snacks', 'nuts');

        $this->assertSame($encapsulation->get('snacks'), ['nuts']);

        $encapsulation->set('drinks', ['beer']);
        $encapsulation->add('drinks', 'cola');

        $this->assertSame(
            $encapsulation->get('drinks'),
            ['beer', 'cola']
        );

        $encapsulation->addList('snacks', ['dry meat', 'chips']);

        $this->assertSame(
            $encapsulation->get('snacks'),
            ['nuts', 'dry meat', 'chips']
        );

        $encapsulation->set('movie', 'Alien');

        $this->expectException(NotAnArrayException::class);

        $encapsulation->add('movie', 'Predator');
    }

    public function testAddToContainer()
    {
        $encapsulation = new Encapsulation([
            'list' => new Container(),
            'emptyList' => null,
        ]);

        $encapsulation->add('list', 'foo');

        $this->assertEquals(
            $encapsulation->get('list'),
            new Container(['foo'])
        );
    }

    public function testAllowedFields()
    {
        $encapsulation = new AllowedFieldsEncapsulation();

        $encapsulation->set('foo', 'foo');

        $this->expectException(NotAllowedFieldException::class);

        $encapsulation->set('hello', 'world');
    }

    public function testArrayAccess()
    {
        $encapsulation = new Encapsulation(['pi' => 3.14159]);

        $this->assertTrue(isset($encapsulation['pi']));
        $this->assertTrue($encapsulation->has('pi'));
        $this->assertSame($encapsulation['pi'], $encapsulation->get('pi'));
        $this->assertSame($encapsulation['pi'], 3.14159);
        $this->assertFalse(isset($encapsulation['eulerian number']));

        $encapsulation['eulerian number'] = 2.71828;
        $this->assertTrue(isset($encapsulation['eulerian number']));
        $this->assertTrue($encapsulation->has('eulerian number'));
        $this->assertSame($encapsulation['eulerian number'], 2.71828);
        $this->assertSame($encapsulation['eulerian number'], $encapsulation->get('eulerian number'));

        unset($encapsulation['pi']);
        $this->assertFalse(isset($encapsulation['pi']));
        $this->assertFalse($encapsulation->has('pi'));

        $this->expectException(\RuntimeException::class);
        $encapsulation[] = 'some Value';
    }

    public function testEmpty()
    {
        $encapsulation = new Encapsulation();

        $this->assertTrue($encapsulation->isEmpty());

        $encapsulation->set('foo', 'foo');
        $this->assertFalse($encapsulation->isEmpty());

        $encapsulation->unset('foo');
        $this->assertTrue($encapsulation->isEmpty());
    }

    public function testIteration()
    {
        $encapsulation = new Encapsulation(['foo' => 'foo', 'bar' => 'bar']);
        $data = [];

        foreach ($encapsulation as $field => $value) {
            $data[$field] = $value;
        }

        $this->assertSame($encapsulation->toArray(), $data);
    }

    public function testSerialization()
    {
        $encapsulation = new Encapsulation(['foo' => 'foo', 'bar' => 'bar']);
        $serialized = serialize($encapsulation);

        $newEncapsulation = unserialize($serialized);

        $this->assertSame($encapsulation->toArray(), $newEncapsulation->toArray());
        $this->assertSame(get_class($encapsulation), get_class($newEncapsulation));

        $json = json_encode($encapsulation);
        $data = json_decode($json, true);

        $this->assertSame($data, ['foo' => 'foo', 'bar' => 'bar']);
    }
}
