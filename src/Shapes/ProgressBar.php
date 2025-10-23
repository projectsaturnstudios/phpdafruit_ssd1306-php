<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Shapes;

/**
 * Progress bar shape configuration
 * 
 * Value object for configuring progress bar appearance and behavior.
 */
class ProgressBar
{
    public const STYLE_SOLID = 'solid';
    public const STYLE_SEGMENTED = 'segmented';
    public const STYLE_ROUNDED = 'rounded';
    
    public const ORIENTATION_HORIZONTAL = 'horizontal';
    public const ORIENTATION_VERTICAL = 'vertical';

    public function __construct(
        public int $x,
        public int $y,
        public int $width,
        public int $height,
        public float $percent = 0.0,
        public string $style = self::STYLE_SOLID,
        public string $orientation = self::ORIENTATION_HORIZONTAL,
        public bool $showPercent = false,
        public bool $border = true,
        public int $segments = 10,
        public int $cornerRadius = 2
    ) {
        $this->percent = max(0, min(100, $percent));
    }

    /**
     * Get the filled width/height based on percentage
     */
    public function getFilledSize(): int
    {
        if ($this->orientation === self::ORIENTATION_HORIZONTAL) {
            return (int)(($this->width - 2) * ($this->percent / 100));
        } else {
            return (int)(($this->height - 2) * ($this->percent / 100));
        }
    }

    /**
     * Get bounds as array
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
     * Create a horizontal progress bar
     */
    public static function horizontal(int $x, int $y, int $width, int $height, float $percent): self
    {
        return new self($x, $y, $width, $height, $percent, self::STYLE_SOLID, self::ORIENTATION_HORIZONTAL);
    }

    /**
     * Create a vertical progress bar
     */
    public static function vertical(int $x, int $y, int $width, int $height, float $percent): self
    {
        return new self($x, $y, $width, $height, $percent, self::STYLE_SOLID, self::ORIENTATION_VERTICAL);
    }
}

