<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Effects;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Typewriter text effect
 * 
 * Text appears character by character like a typewriter,
 * with optional cursor indicator.
 */
class TypewriterText implements TextEffect
{
    private bool $showCursor;

    /**
     * @param bool $showCursor Whether to show a blinking cursor
     */
    public function __construct(bool $showCursor = false)
    {
        $this->showCursor = $showCursor;
    }

    public function render(SSD1306Display $display, string $text, int $x, int $y, float $progress): void
    {
        $progress = max(0, min(1, $progress));
        
        $textLength = strlen($text);
        $charsToShow = (int)($textLength * $progress);
        
        // Render visible characters
        $visibleText = substr($text, 0, $charsToShow);
        
        $display->setCursor($x, $y);
        foreach (str_split($visibleText) as $char) {
            $display->write(ord($char));
        }
        
        // Show cursor if enabled and not complete
        if ($this->showCursor && $progress < 1.0) {
            $display->write(ord('_'));
        }
    }

    public function reset(): void
    {
        // No state to reset
    }

    public function isComplete(float $progress): bool
    {
        return $progress >= 1.0;
    }
}

