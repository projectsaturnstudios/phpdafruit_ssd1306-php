<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\UI;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Abstract base class for dashboard widgets
 * 
 * Widgets are reusable UI components that can be placed in layouts
 * and render specific content (text, graphs, icons, progress, etc.)
 */
abstract class Widget
{
    protected int $x = 0;
    protected int $y = 0;
    protected int $width = 0;
    protected int $height = 0;
    protected bool $visible = true;
    protected array $data = [];

    public function __construct(
        protected SSD1306Display $display,
        int $x = 0,
        int $y = 0,
        int $width = 0,
        int $height = 0
    ) {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Render the widget to the display
     *
     * @return void
     */
    abstract public function render(): void;

    /**
     * Update widget state (called each frame)
     *
     * @param float $deltaTime Time since last update in seconds
     * @return void
     */
    public function update(float $deltaTime): void
    {
        // Override in subclasses if needed
    }

    /**
     * Set widget position
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @return self
     */
    public function setPosition(int $x, int $y): self
    {
        $this->x = $x;
        $this->y = $y;
        return $this;
    }

    /**
     * Set widget size
     *
     * @param int $width Width in pixels
     * @param int $height Height in pixels
     * @return self
     */
    public function setSize(int $width, int $height): self
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * Set widget bounds
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $width Width
     * @param int $height Height
     * @return self
     */
    public function setBounds(int $x, int $y, int $width, int $height): self
    {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * Get widget bounds
     *
     * @return array{x: int, y: int, width: int, height: int}
     */
    public function getBounds(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'width' => $this->width,
            'height' => $this->height
        ];
    }

    /**
     * Set widget visibility
     *
     * @param bool $visible Visibility state
     * @return self
     */
    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * Check if widget is visible
     *
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Show widget
     *
     * @return self
     */
    public function show(): self
    {
        $this->visible = true;
        return $this;
    }

    /**
     * Hide widget
     *
     * @return self
     */
    public function hide(): self
    {
        $this->visible = false;
        return $this;
    }

    /**
     * Set widget data
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
     * Get widget data
     *
     * @param string $key Data key
     * @param mixed $default Default value
     * @return mixed
     */
    public function getData(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Get position
     *
     * @return array{x: int, y: int}
     */
    public function getPosition(): array
    {
        return ['x' => $this->x, 'y' => $this->y];
    }

    /**
     * Get size
     *
     * @return array{width: int, height: int}
     */
    public function getSize(): array
    {
        return ['width' => $this->width, 'height' => $this->height];
    }
}

