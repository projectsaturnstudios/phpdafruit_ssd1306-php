<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Shapes;

/**
 * Rounded box/panel shape configuration
 * 
 * Rectangle with rounded corners for UI panels and containers.
 */
class RoundedBox
{
    public function __construct(
        public int $x,
        public int $y,
        public int $width,
        public int $height,
        public int $radius = 4,
        public bool $filled = false,
        public bool $border = true,
        public int $padding = 2
    ) {
        // Ensure radius doesn't exceed half the smaller dimension
        $maxRadius = (int)(min($width, $height) / 2);
        $this->radius = min($radius, $maxRadius);
    }

    /**
     * Get inner bounds (accounting for padding)
     */
    public function getInnerBounds(): array
    {
        return [
            'x' => $this->x + $this->padding,
            'y' => $this->y + $this->padding,
            'width' => $this->width - ($this->padding * 2),
            'height' => $this->height - ($this->padding * 2)
        ];
    }

    /**
     * Get bounds
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
     * Get corner center coordinates for drawing arcs
     */
    public function getCornerCenters(): array
    {
        return [
            'top_left' => [
                'x' => $this->x + $this->radius,
                'y' => $this->y + $this->radius
            ],
            'top_right' => [
                'x' => $this->x + $this->width - $this->radius - 1,
                'y' => $this->y + $this->radius
            ],
            'bottom_left' => [
                'x' => $this->x + $this->radius,
                'y' => $this->y + $this->height - $this->radius - 1
            ],
            'bottom_right' => [
                'x' => $this->x + $this->width - $this->radius - 1,
                'y' => $this->y + $this->height - $this->radius - 1
            ]
        ];
    }

    /**
     * Create a panel preset
     */
    public static function panel(int $x, int $y, int $width, int $height): self
    {
        return new self($x, $y, $width, $height, 4, false, true, 4);
    }

    /**
     * Create a button preset
     */
    public static function button(int $x, int $y, int $width, int $height): self
    {
        return new self($x, $y, $width, $height, 3, true, true, 2);
    }
}

