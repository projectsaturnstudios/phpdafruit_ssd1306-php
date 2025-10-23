<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Concerns;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Interface for all renderable components
 * 
 * Ensures consistent API across all display components
 * (widgets, shapes, effects, states, etc.)
 */
interface Renderable
{
    /**
     * Render the component to the display
     *
     * @return void
     */
    public function render(): void;

    /**
     * Get component bounds
     *
     * @return array{x: int, y: int, width: int, height: int}
     */
    public function getBounds(): array;

    /**
     * Set component visibility
     *
     * @param bool $visible Visibility state
     * @return self
     */
    public function setVisible(bool $visible): self;

    /**
     * Check if component is visible
     *
     * @return bool
     */
    public function isVisible(): bool;
}

