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
    public function __get(string $name): mixed
    {
        return $this->attributes[$name] ?? null;
    }

    /**
     * Set an attribute.
     */
    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Check if an attribute is set.
     */
    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }
}
