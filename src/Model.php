<?php

namespace BertW\LaravelLogViewer;

/**
 * Simplified representation of a class with generic attributes.
 */
class Model
{
    /**
     * @var array
     */
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Get an attribute.
     *
     * @param string $value
     * @return mixed
     */
    public function __get($value)
    {
        return $this->attributes[$value] ?? null;
    }
}
