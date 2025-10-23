<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Shapes;

/**
 * Gauge/dial shape configuration
 * 
 * Circular or arc-based gauge with needle indicator.
 */
class Gauge
{
    public const STYLE_FULL_CIRCLE = 'full';
    public const STYLE_HALF_CIRCLE = 'half';
    public const STYLE_ARC = 'arc';

    public function __construct(
        public int $cx,
        public int $cy,
        public int $radius,
        public float $value,
        public float $min = 0.0,
        public float $max = 100.0,
        public string $style = self::STYLE_HALF_CIRCLE,
        public float $startAngle = 180.0,
        public float $endAngle = 360.0,
        public bool $showTicks = true,
        public int $tickCount = 5,
        public bool $showValue = false
    ) {
        $this->value = max($this->min, min($this->max, $value));
    }

    /**
     * Get the angle for the current value
     */
    public function getValueAngle(): float
    {
        $range = $this->max - $this->min;
        $percent = ($this->value - $this->min) / $range;
        $angleRange = $this->endAngle - $this->startAngle;
        
        return $this->startAngle + ($angleRange * $percent);
    }

    /**
     * Convert value to percentage
     */
    public function getPercent(): float
    {
        $range = $this->max - $this->min;
        return (($this->value - $this->min) / $range) * 100;
    }

    /**
     * Get needle endpoint coordinates
     */
    public function getNeedlePoint(): array
    {
        $angle = deg2rad($this->getValueAngle());
        $needleLength = $this->radius - 5; // Slightly shorter than radius
        
        return [
            'x' => $this->cx + (int)($needleLength * cos($angle)),
            'y' => $this->cy + (int)($needleLength * sin($angle))
        ];
    }

    /**
     * Get bounds
     */
    public function getBounds(): array
    {
        return [
            'x' => $this->cx - $this->radius,
            'y' => $this->cy - $this->radius,
            'width' => $this->radius * 2,
            'height' => $this->radius * 2
        ];
    }
}

