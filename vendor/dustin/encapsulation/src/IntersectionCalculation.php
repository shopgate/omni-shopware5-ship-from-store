<?php

namespace Dustin\Encapsulation;

use Dustin\Encapsulation\Exception\UncomparableException;

/**
 * Calculates intersections and differences between two encapsulations.
 */
class IntersectionCalculation
{
    /**
     * Returns all fields which both encapsulations have in common.
     *
     * @return string[] An array holding the common fields
     */
    public static function getFieldIntersection(EncapsulationInterface $a, EncapsulationInterface $b): array
    {
        return array_values(
            \array_intersect(
                array_values($a->getFields()),
                array_values($b->getFields())
            )
        );
    }

    /**
     * Returns all fields which differ in $a from $b.
     *
     * @return string[] An array holding all fields which appear in $a but not in $b
     */
    public static function getFieldDifference(EncapsulationInterface $a, EncapsulationInterface $b): array
    {
        return array_values(
            \array_diff(
                array_values($a->getFields()),
                array_values($b->getFields())
            )
        );
    }

    /**
     * Creates the intersection between two encapsulations.
     *
     * Creates a new encapsulation representing the intersection between two encapsulations.
     * Intersections will be created in-depth.
     * No distinction is made between stored arrays and encapsulations. Both types will be compared together and result in a new encapsulation holding the intersection.
     *
     * @return EncapsulationInterface An encapsulation representing the in-depth intersection between $a and $b
     *
     * @throws UncomparableException Thrown if a value is not supported for comparison (Not an encapsulation)
     */
    public static function getIntersection(AbstractEncapsulation $a, AbstractEncapsulation $b): EncapsulationInterface
    {
        $intersectionFields = static::getFieldIntersection($a, $b);
        $intersection = new NestedEncapsulation();

        /** @var string $field */
        foreach ($intersectionFields as $field) {
            $value = $a->get($field);
            $compareValue = $b->get($field);

            if (!static::isComparable($value)) {
                throw new UncomparableException($value);
            }

            if (!static::isComparable($compareValue)) {
                throw new UncomparableException($compareValue);
            }

            if (\is_scalar($value) || $value === null) {
                if ($value === $compareValue) {
                    $intersection->set($field, $value);
                }

                continue;
            }

            if ($compareValue === null) {
                continue;
            }

            $value = is_array($value) ? new Encapsulation($value) : $value;
            $compareValue = is_array($compareValue) ? new Encapsulation($compareValue) : $compareValue;
            $intersectedData = static::getIntersection($value, $compareValue);

            if (!empty($intersectedData->toArray())) {
                $intersection->set($field, $intersectedData);
            }
        }

        return $intersection;
    }

    /**
     * Creates the difference of an encapsulation compared to another one.
     *
     * Creates a new encapsulation representing all data from the first encapsulation which differ from the second one.
     * Differences will be in-depth.
     * No distinction is made between arrays and encapsulations. Both types will be compared together and result in a new encapsulation holding the difference.
     *
     * @return EncapsulationInterface An encapsulation representing the difference from $a to $b
     *
     * @throws UncomparableException Thrown if a value is not supported for comparison (Not an encapsulation)
     */
    public static function getDifference(AbstractEncapsulation $a, AbstractEncapsulation $b): EncapsulationInterface
    {
        $difference = new NestedEncapsulation();

        /** @var string $field */
        foreach ($a->getFields() as $field) {
            $value = $a->get($field);

            if (!static::isComparable($value)) {
                throw new UncomparableException($value);
            }

            if (!$b->has($field)) {
                if (is_scalar($value) || $value === null) {
                    $difference->set($field, $value);
                    continue;
                }

                $value = is_array($value) ? new Encapsulation($value) : $value;
                $difference->set($field, static::getDifference($value, new Encapsulation()));
                continue;
            }

            $compareValue = $b->get($field);

            if (!static::isComparable($compareValue)) {
                throw new UncomparableException($compareValue);
            }

            if (\is_scalar($value) || $value == null) {
                if ($value !== $compareValue) {
                    $difference->set($field, $value);
                }

                continue;
            }

            $value = is_array($value) ? new Encapsulation($value) : $value;
            $compareData = is_array($compareValue) || $compareValue === null ? new Encapsulation((array) $compareValue) : $compareValue;
            $differencedData = static::getDifference($value, $compareData);

            if (!empty($differencedData->toArray()) || $compareValue === null) {
                $difference->set($field, $differencedData);
            }
        }

        return $difference;
    }

    /**
     * Returns wether a value is comparable.
     *
     * Comparable values are scalar values, arrays, null and objects beeing instance of {@see} AbstractEncapsulation.
     *
     * @return bool False if the value is a resource or an object and not instance of {@see} AbstractEncapsulation. True otherwise.
     */
    public static function isComparable($value): bool
    {
        return !(
            \is_resource($value) ||
            (is_object($value) && !($value instanceof AbstractEncapsulation))
        );
    }
}
