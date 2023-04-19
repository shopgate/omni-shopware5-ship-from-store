<?php

namespace Dustin\Encapsulation\Test;

use Dustin\Encapsulation\AbstractObjectMapping;
use Dustin\Encapsulation\Encapsulation;
use Dustin\Encapsulation\NestedEncapsulation;
use Dustin\Encapsulation\ObjectMapping;
use PHPUnit\Framework\TestCase;

class EncapsulationMap extends AbstractObjectMapping
{
    protected function getType(): string
    {
        return Encapsulation::class;
    }
}

class ObjectMappingTest extends TestCase
{
    public function testAbstractObjectMap()
    {
        $mapping = new EncapsulationMap(['first' => new Encapsulation()]);
        $mapping->set('second', new Encapsulation());

        $this->expectException(\InvalidArgumentException::class);

        $mapping->set('third', new NestedEncapsulation());
    }

    public function testObjectMap()
    {
        $map = ObjectMapping::create(Encapsulation::class);

        $map->set('first', new Encapsulation());

        $this->expectException(\InvalidArgumentException::class);

        $map->set('second', new NestedEncapsulation());
    }

    public function testObjectMapConstructor()
    {
        $this->expectException(\RuntimeException::class);

        $map = new ObjectMapping();
    }
}
