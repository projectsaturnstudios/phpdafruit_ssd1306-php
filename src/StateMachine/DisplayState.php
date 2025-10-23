<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\StateMachine;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Abstract base class for display states
 * 
 * Represents a distinct visual/functional state of the display
 * with lifecycle methods for entering, updating, exiting, and rendering.
 */
abstract class DisplayState
{
    protected SSD1306Display $display;
    protected array $data = [];

    public function __construct(SSD1306Display $display)
    {
        $this->display = $display;
    }

    /**
     * Called when entering this state
     *
     * @param array $context Data passed from previous state or transition
     * @return void
     */
    abstract public function enter(array $context = []): void;

    /**
     * Called every frame while in this state
     *
     * @param float $deltaTime Time elapsed since last update (seconds)
     * @return void
     */
    abstract public function update(float $deltaTime): void;

    /**
     * Called when exiting this state
     *
     * @return array Data to pass to next state
     */
    abstract public function exit(): array;

    /**
     * Render the current state to the display
     *
     * @return void
     */
    abstract public function render(): void;

    /**
     * Get state name for identification
     *
     * @return string
     */
    public function getName(): string
    {
        $className = get_class($this);
        $parts = explode('\\', $className);
        return end($parts);
    }

    /**
     * Set state data
     *
     * @param string $key Data key
     * @param mixed $value Data value
     * @return self
     */
    public function setData(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Get state data
     *
     * @param string $key Data key
     * @param mixed $default Default value if key not found
     * @return mixed
     */
    public function getData(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Check if state has data key
     *
     * @param string $key Data key
     * @return bool
     */
    public function hasData(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Get all state data
     *
     * @return array
     */
    public function getAllData(): array
    {
        return $this->data;
    }

    /**
     * Clear all state data
     *
     * @return self
     */
    public function clearData(): self
    {
        $this->data = [];
        return $this;
    }
}

