<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Effects;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Scrolling text effect
 * 
 * Text scrolls in from one side to another with configurable
 * speed and direction.
 */
class ScrollingText implements TextEffect
{
    private int $direction;
    private int $distance;

    public const DIRECTION_LEFT = 0;
    public const DIRECTION_RIGHT = 1;
    public const DIRECTION_UP = 2;
    public const DIRECTION_DOWN = 3;

    /**
     * @param int $direction Scroll direction (use class constants)
     * @param int $distance Total distance to scroll in pixels
     */
    public function __construct(int $direction = self::DIRECTION_LEFT, int $distance = 128)
    {
        $this->direction = $direction;
        $this->distance = $distance;
    }

    public function render(SSD1306Display $display, string $text, int $x, int $y, float $progress): void
    {
        $progress = max(0, min(1, $progress));
        
        // Calculate current position based on progress
        $offset = (int)($this->distance * $progress);
        
        $renderX = $x;
        $renderY = $y;
        
        switch ($this->direction) {
            case self::DIRECTION_LEFT:
                $renderX = $x + $this->distance - $offset;
                break;
            case self::DIRECTION_RIGHT:
                $renderX = $x - $this->distance + $offset;
                break;
            case self::DIRECTION_UP:
                $renderY = $y + $this->distance - $offset;
                break;
            case self::DIRECTION_DOWN:
                $renderY = $y - $this->distance + $offset;
                break;
        }
        
        // Render text at calculated position
        $display->setCursor($renderX, $renderY);
        foreach (str_split($text) as $char) {
            $display->write(ord($char));
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

