<?php

namespace BertW\LaravelLogViewer;

/**
 * Simplified representation of a class with generic attributes.
 */
class Model
{
    /**
     * @var array<string, mixed>
     */
    protected array $attributes = [];

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    /**
     * Get an attribute.
     */
    public function __get(string $value): mixed
    {
        return $this->attributes[$value] ?? null;
    }
}
