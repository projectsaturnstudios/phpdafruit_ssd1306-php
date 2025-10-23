<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Effects;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Interface for text effects
 * 
 * Text effects provide animated or styled text rendering
 * with progress-based control for smooth animations.
 */
interface TextEffect
{
    /**
     * Render the text effect
     *
     * @param SSD1306Display $display The display to render on
     * @param string $text The text to render
     * @param int $x Starting X coordinate
     * @param int $y Starting Y coordinate
     * @param float $progress Animation progress (0.0 to 1.0)
     * @return void
     */
    public function render(SSD1306Display $display, string $text, int $x, int $y, float $progress): void;

    /**
     * Reset the effect to its initial state
     *
     * @return void
     */
    public function reset(): void;

    /**
     * Check if the effect is complete
     *
     * @param float $progress Current progress
     * @return bool True if effect has completed
     */
    public function isComplete(float $progress): bool;
}

