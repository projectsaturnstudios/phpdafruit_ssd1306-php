<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Effects;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Marquee text effect
 * 
 * Text scrolls continuously in a loop, perfect for long messages
 * that need to fit in a small space.
 */
class MarqueeText implements TextEffect
{
    private int $speed;
    private int $padding;

    /**
     * @param int $speed Pixels per progress unit
     * @param int $padding Space between loop iterations in pixels
     */
    public function __construct(int $speed = 2, int $padding = 20)
    {
        $this->speed = $speed;
        $this->padding = $padding;
    }

    public function render(SSD1306Display $display, string $text, int $x, int $y, float $progress): void
    {
        // Normalize progress to loop (0-1 repeating)
        $loopProgress = fmod($progress, 1.0);
        
        // Calculate text width (approximate)
        $textWidth = strlen($text) * 6; // 6 pixels per char
        $totalWidth = $textWidth + $this->padding;
        
        // Calculate scroll position
        $offset = (int)($totalWidth * $loopProgress);
        
        // Render text at scrolling position
        $renderX = $x - $offset;
        
        $display->setCursor($renderX, $y);
        foreach (str_split($text) as $char) {
            $display->write(ord($char));
        }
        
        // Render second copy for seamless loop
        if ($renderX + $textWidth < $display->getDisplayWidth()) {
            $display->setCursor($renderX + $totalWidth, $y);
            foreach (str_split($text) as $char) {
                $display->write(ord($char));
            }
        }
    }

    public function reset(): void
    {
        // No state to reset
    }

    public function isComplete(float $progress): bool
    {
        // Marquee never completes - it loops forever
        return false;
    }
}

