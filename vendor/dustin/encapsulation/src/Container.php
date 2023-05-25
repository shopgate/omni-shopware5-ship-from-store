<?php

namespace Dustin\Encapsulation;

/**
 * Holds a list of elements without keys.
 */
class Container implements \Countable, \IteratorAggregate, \JsonSerializable
{
    use IteratorTrait;

    use JsonSerializableTrait;

    public const ASCENDING = true;

    public const DESCENDING = false;

    private array $elements = [];

    /**
     * Optionally initializes a container with elements.
     *
     * @param mixed[] $elements An optional array holding initial elements
     */
    public function __construct(array $elements = [])
    {
        $this->add(...$elements);
    }

    /**
     * Takes all elements from several containers and merges them into a new one.
     *
     * @return self a new Container instance holding all elements from the given containers
     */
    public static function merge(self ...$containers): self
    {
        $new = new static();

        foreach ($containers as $container) {
            $new->add(...$container->toArray());
        }

        return $new;
    }

    /**
     * Converts the container into an array.
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return array_values($this->elements);
    }

    /**
     * Creates a copy of the container.
     *
     * This function does not clone object elements.
     */
    public function copy(): self
    {
        return new static($this->elements);
    }

    /**
     * Adds one or more elements to the container.
     *
     * @param mixed ...$elements One or more elements to add to the container
     *
     * @throws \InvalidArgumentException
     */
    public function add(...$elements): self
    {
        foreach ($elements as $element) {
            $this->validateType($element);
            $this->elements[] = $element;
        }

        return $this;
    }

    /**
     * Clears the container and removes all elements.
     */
    public function clear(): self
    {
        $this->elements = [];

        return $this;
    }

    /**
     * Returns the element at the given position or null if not available.
     *
     * @return mixed|null The element at $position or null
     */
    public function getAt(int $position)
    {
        return array_values($this->elements)[$position] ?? null;
    }

    /**
     * Returns the amount of elements in the container.
     *
     * Counting the elements of a container is also possible with PHP's {@see} \count function.
     */
    public function count(): int
    {
        return count($this->elements);
    }

    /**
     * Returns wether the container has no elements.
     */
    public function isEmpty(): bool
    {
        return empty($this->elements);
    }

    /**
     * Applies the callback to the elements of the container.
     *
     * @see \array_map()
     *
     * @return self A new Container instance holding the result
     */
    public function map(callable $callable): self
    {
        return new static(array_map($callable, array_values($this->elements)));
    }

    /**
     * Iteratively reduces the container's elements to a single value using a callback function.
     *
     * @see \array_reduce()
     *
     * @param mixed $initial
     *
     * @return mixed
     */
    public function reduce(callable $callable, $initial = null)
    {
        return array_reduce($this->elements, $callable, $initial);
    }

    /**
     * Filters the container's elements using a callback function.
     *
     * @see \array_filter()
     *
     * @return self A new Container instance holding the result
     */
    public function filter(?callable $callable = null): self
    {
        // callback param is only nullable in PHP8
        return $callable === null ?
            new static(array_filter($this->elements)) :
            new static(array_filter($this->elements, $callable));
    }

    /**
     * Extracts a slice of the container's elements.
     *
     * @see \array_slice()
     *
     * @return self A new Container instance holding the result
     */
    public function slice(int $offset, ?int $length = null): self
    {
        return new static(array_slice(array_values($this->elements), $offset, $length));
    }

    /**
     * Removes a portion of the elements and replace it with something else.
     *
     * @see \array_splice()
     *
     * @return self The container itself
     */
    public function splice(int $offset, ?int $length = null, $replacement = []): self
    {
        array_splice($this->elements, $offset, $length, $replacement);

        return $this;
    }

    /**
     * Removes duplicate elements.
     *
     * @see \array_unique()
     *
     * @return self A new Container instance holding the result
     */
    public function unique(int $flags = SORT_STRING): self
    {
        return new static(array_values(array_unique($this->elements, $flags)));
    }

    /**
     * Shifts the element off the beginning.
     *
     * @see \array_shift()
     *
     * @return mixed|null The shifted element or null
     */
    public function shift()
    {
        return array_shift($this->elements);
    }

    /**
     * Prepends one or more elements to the beginning of the container's elements.
     *
     * @see \array_unshift()
     *
     * @param mixed ...$elements
     *
     * @return self The container itself
     */
    public function unshift(...$elements): self
    {
        array_unshift($this->elements, ...$elements);

        return $this;
    }

    /**
     * Pops the element off the end.
     *
     * @see \array_pop()
     *
     * @return mixed|null The last element or null
     */
    public function pop()
    {
        return array_pop($this->elements);
    }

    /**
     * Replaces elements from passed arrays into the container.
     *
     * @see \array_replace()
     *
     * @return self A new Container instance holding the result
     */
    public function replace(array ...$arrays): self
    {
        return new static(array_replace($this->elements, ...$arrays));
    }

    /**
     * Applies a user supplied function to every element.
     *
     * @see \array_walk()
     *
     * @param mixed|null $arg
     *
     * @return self The container itself
     */
    public function walk(callable $callable, $arg = null): self
    {
        array_walk($this->elements, $callable, $arg);

        return $this;
    }

    /**
     * Returns a container with elements in reverse order.
     *
     * @see \array_reverse()
     *
     * @return self A new Container instance holding the result
     */
    public function reverse(): self
    {
        return new static(array_reverse($this->elements));
    }

    /**
     * Searches the container for a given value and returns the first corresponding position if successful.
     *
     * @see \array_search()
     *
     * @param mixed $needle
     *
     * @return int|false The position of the searched element or false if not found
     */
    public function search($needle, bool $strict = false): ?int
    {
        return array_search($needle, array_values($this->elements), $strict);
    }

    /**
     * Checks if a value exists in the container.
     *
     * @see \in_array()
     *
     * @param mixed $value
     *
     * @return bool Wether the value exists or not
     */
    public function has($value): bool
    {
        return in_array($value, $this->elements);
    }

    /**
     * Sorts the elements of the container.
     *
     * Sorts all elements ascending, descending or by a callback function.
     *
     * @param callable|null $callable  An optional callback which is used to sort elements by a user-defined comparison function ({@see} \usort())
     * @param bool          $direction True for ascending and false for descending. Will be ignored if a callback is available
     * @param int           $flags     Flags for array sort functions {@see} \sort() and {@see} \rsort()
     *
     * @return self The container itself
     */
    public function sort(?callable $callable = null, bool $direction = self::ASCENDING, int $flags = SORT_REGULAR): self
    {
        if ($callable !== null) {
            usort($this->elements, $callable);
        } elseif ($direction) {
            sort($this->elements, $flags);
        } else {
            rsort($this->elements, $flags);
        }

        return $this;
    }

    /**
     * Splits the container into chunks.
     *
     * @see \array_chunk()
     *
     * @return Container[] An array of new Container instances each representing one chunk
     */
    public function chunk(int $length): array
    {
        return array_map(function (array $chunk) {
            return new static($chunk);
        }, array_chunk($this->elements, $length));
    }

    /**
     * @ignore
     */
    public function __serialize(): array
    {
        return array_map('serialize', array_values($this->elements));
    }

    /**
     * @ignore
     */
    public function __unserialize(array $data): void
    {
        $this->elements = array_values(array_map('unserialize', $data));
    }

    /**
     * Returns an optional class for valid elements.
     *
     * Can be overwritten to return a class name.
     * Adding elements to the container which are not instances of the class will then throw an exception.
     *
     * @return string|null A class name or null
     */
    protected function getAllowedClass(): ?string
    {
        return null;
    }

    private function validateType($element)
    {
        $class = $this->getAllowedClass();

        if ($class === null) {
            return;
        }

        if (!is_object($element) || !($element instanceof $class)) {
            $type = is_object($element) ? get_class($element) : gettype($element);

            throw new \InvalidArgumentException(sprintf('Container can only hold %s got %s', $class, $type));
        }
    }
}
