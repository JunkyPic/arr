<?php

namespace Arr;

/**
 * Class Arr
 * @package Arr
 */
class Arr
{
    /**
     * @var array
     */
    private $arr;

    /**
     * Arr constructor.
     *
     * @param array $array
     * @throws \Exception
     */
    public function __construct(array $array)
    {
        $this->arr = $array;
    }

    /**
     * Recursively walks the array without breaking the iteration with return
     *
     * @return \Generator
     */
    public function walkRecursive(): \Generator {
        foreach (new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->arr)) as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * @param $match
     * @return bool
     */
    public function hasValue($match): bool {
        foreach($this->walkRecursive() as $key => $value) {
            if($value === $match) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $match
     * @return bool
     */
    public function hasKey($match): bool {
        foreach($this->walkRecursive() as $key => $value) {
            if($key === $match) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $item
     * @param bool $toKey
     * @return $this
     */
    public function add($item, $toKey = false): Arr {
        if(false === $toKey) {
            $this->arr[] = $item;
            return $this;
        }

        return new self($this->addK($this->arr, $item, $toKey));
    }

    /**
     * @return array
     */
    public function unique(): array {
        return array_unique($this->arr,SORT_REGULAR);
    }

    /**
     * @param array $matches
     * @return $this
     */
    public function replace(array $matches): Arr
    {
        if (empty($matches)) {
            throw new \InvalidArgumentException('Cannot replace current array values with empty values');
        }

        if (count($matches) !== count($matches, COUNT_RECURSIVE)) {
            throw new \InvalidArgumentException('Only one dimensional associative arrays are allowed');
        }

        $this->arr = $this->replaceValueP($matches, $this->arr);

        return $this;
    }

    /**
     * Counts recursively the elements in the array
     *
     * @return int
     */
    public function countRecursive(): int
    {
        return count($this->arr, COUNT_RECURSIVE);
    }

    /**
     * @return float
     * @throws \Exception
     */
    public function average(): float
    {
        return $this->avg();
    }

    /**
     * Compute the average sum off the values in the array
     *
     * Strings, booleans, etc will be treated as int
     *
     * @return float
     * @throws \Exception
     */
    public function avg(): float
    {
        return (array_sum($this->arr) / $this->count());
    }

    /**
     * Filters an array based on a closure passed as the argument
     *
     * @param \Closure $callback
     *
     * @return Arr
     * @throws \Exception
     */
    public function filter(\Closure $callback): Arr
    {
        return new $this(array_filter($this->arr, $callback, ARRAY_FILTER_USE_BOTH));
    }

    /**
     * @param \Closure $callback
     * @return Arr
     * @throws \Exception
     */
    public function map(\Closure $callback): self
    {
        return new $this(
            array_combine($this->keys(),
                array_map(
                    $callback,
                    $this->arr,
                    $this->keys())
            )
        );
    }

    /**
     * @return int
     */
    public function count() {
        return count($this->arr);
    }

    /**
     * @param $values
     * @return Arr
     */
    public function except($values): self
    {
        if (is_array($values)) {
            if ($this->countRecursive() !== $this->count()) {
                throw new \OutOfBoundsException('Cannot accept a multidimensional array');
            }

            foreach ($values as $value) {
                $this->forget($value);
            }

            return $this;
        } else {
            $this->forget($values);
        }

        return $this;
    }

    /**
     * @param $key
     * @return Arr
     */
    public function forget($key): self
    {
        if(isset($this->arr[$key])) {
            unset($this->arr[$key]);
        }

        return $this;
    }

    /**
     * Flips the array and returns a new instance of this class
     *
     * @return Arr
     */
    public function flip(): self
    {
        if ($this->countRecursive() !== $this->count()) {
            throw new \OutOfBoundsException('The current array is not suitable for flipping. It has keys that are not integers or strings.');
        }

        return new $this(array_flip($this->arr));
    }

    /**
     * Returns the values of the array
     *
     * @return array
     * @throws \Exception
     */
    public function values(): array
    {
        return array_values($this->arr);
    }

    /**
     * Returns the keys of the array
     *
     * @return array|null
     * @throws \Exception
     */
    public function keys(): ?array
    {
        return array_keys($this->arr);
    }

    /**
     * Returns all the values in the array
     *
     * @return array
     */
    public function all(): array
    {
        return $this->arr;
    }

    /**
     * Splits the array into chunks and returns a new instance of
     * $this with $this->arr as the result of the chunking
     *
     * @param $chunk
     *
     * @return Arr
     * @throws \Exception
     */
    public function chunk($chunk): self
    {
        if ((int)$chunk !== $chunk) {
            throw new \InvalidArgumentException(sprintf('Argument must be of type int, %s given.', gettype($chunk)));
        }

        return new $this(array_chunk($this->arr, $chunk, true));
    }

    /**
     * Returns the first element in the array
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function first(): ?mixed
    {
        $array = $this->arr;

        return array_pop(array_reverse($array));
    }

    /**
     * Return the last element in the array
     *
     * @return array|null
     * @throws \Exception
     */
    public function last(): ?array
    {
        return (array_slice($this->arr, -1));
    }

    /**
     * Returns the smallest value of the array
     *
     * @return mixed|null
     * @throws \Exception
     */
    public function minValue(): ?mixed
    {
        return min(array_values($this->arr));
    }

    /**
     * Returns the largest value of the array
     *
     * @throws \Exception
     * @return mixed|null
     */
    public function maxValue(): ?mixed
    {
        return max(array_values($this->arr));
    }

    /**
     * Returns the smallest key of the array
     *
     * @throws \Exception
     * @return mixed|null
     */
    public function minKey(): ?mixed
    {
        return min(array_keys($this->arr));
    }

    /**
     * Returns the largest key of the array
     *
     * @return mixed|null
     */
    public function maxKey(): ?mixed
    {
        return max(array_keys($this->arr));
    }

    /**
     * @return null|number
     */
    public function sum(): ?number
    {
        return array_sum($this->arr);
    }

    /**
     * Walks the array and implodes all values with a given glue
     *
     * @param $glue
     *
     * @return string
     */
    public function implodeRecursive($glue): string
    {
        $result = '';

        foreach($this->walkRecursive() as $value) {
            $result .= $value . $glue;
        }

        return rtrim($result, $glue);
    }

    /**
     * Flattens an array
     *
     * @return array
     */
    public function flatten(): array
    {
        $result = [];

        foreach($this->walkRecursive() as $value) {
            $result[] = $value;
        }

        return $result;
    }

    /**
     * Get every nth element in the array
     *
     * @param $nth
     *
     * @return array
     */
    public function nth($nth): array
    {
        if ((int)$nth !== $nth || (int)$nth == 0 || $nth < 0) {
            throw new \OutOfBoundsException(sprintf('Function argument for "%s" must be of type int, not equal to zero and positive'), __METHOD__);
        }

        $result = [];

        $i = 0;

        foreach ($this->arr as $value) {
            if ($i++ % (int)$nth == 0) {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Clears all the values in the current instance of the array
     * and sets the parameter as the new workable array
     *
     * @param array $array
     *
     * @return Arr
     */
    public function overwrite(array $array): self
    {
        $this->arr = $array;

        return $this;
    }

    /**
     * Json encode the current array
     *
     * @return string
     *
     * @throw \Exception
     */
    public function toJson(): string
    {
        return json_encode($this->arr, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Serialize a json string and returns the result
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return serialize($this->toJson());
    }

///////////
///
/// Internal
///
///////////
    /**
     * @param $array
     * @param $item
     * @param $key
     * @return mixed
     */
    private function addK(&$array, $item, $key): mixed {
        foreach ($array as $keyK => &$value) {
            if (is_array($value)) {
                $this->addK($value, $item, $key);
            } else {
                if($key === $keyK) {
                    $array[$keyK] = [
                        $item,
                        $value,
                    ];
                }
            }
        }

        return $array;
    }

    /**
     * @param $matches
     * @param $array
     * @return mixed
     */
    private function replaceValueP($matches, &$array): ?mixed
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $this->replaceValueP($matches, $value);
            } else {
                foreach ($matches as $mKey => $match) {
                    if ($value === $mKey) {
                        $array[$key] = $match;
                    }
                }
            }
        }

        return $array;
    }
}
