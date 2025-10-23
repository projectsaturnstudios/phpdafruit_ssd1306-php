<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Math;

/**
 * 2D Vector mathematics
 * 
 * Provides vector operations for 2D graphics transformations,
 * physics calculations, and animation paths.
 */
class Vector2D
{
    public function __construct(
        public float $x = 0.0,
        public float $y = 0.0
    ) {}

    /**
     * Add another vector to this one
     */
    public function add(Vector2D $other): Vector2D
    {
        return new Vector2D($this->x + $other->x, $this->y + $other->y);
    }

    /**
     * Subtract another vector from this one
     */
    public function subtract(Vector2D $other): Vector2D
    {
        return new Vector2D($this->x - $other->x, $this->y - $other->y);
    }

    /**
     * Multiply vector by a scalar
     */
    public function multiply(float $scalar): Vector2D
    {
        return new Vector2D($this->x * $scalar, $this->y * $scalar);
    }

    /**
     * Divide vector by a scalar
     */
    public function divide(float $scalar): Vector2D
    {
        if ($scalar == 0) {
            throw new \InvalidArgumentException('Cannot divide by zero');
        }
        return new Vector2D($this->x / $scalar, $this->y / $scalar);
    }

    /**
     * Calculate the magnitude (length) of the vector
     */
    public function magnitude(): float
    {
        return sqrt($this->x * $this->x + $this->y * $this->y);
    }

    /**
     * Calculate the squared magnitude (faster than magnitude)
     */
    public function magnitudeSquared(): float
    {
        return $this->x * $this->x + $this->y * $this->y;
    }

    /**
     * Normalize the vector (make it unit length)
     */
    public function normalize(): Vector2D
    {
        $mag = $this->magnitude();
        if ($mag == 0) {
            return new Vector2D(0, 0);
        }
        return $this->divide($mag);
    }

    /**
     * Calculate dot product with another vector
     */
    public function dot(Vector2D $other): float
    {
        return $this->x * $other->x + $this->y * $other->y;
    }

    /**
     * Calculate the angle of this vector in radians
     */
    public function angle(): float
    {
        return atan2($this->y, $this->x);
    }

    /**
     * Calculate the angle between this vector and another in radians
     */
    public function angleBetween(Vector2D $other): float
    {
        $dot = $this->dot($other);
        $mag1 = $this->magnitude();
        $mag2 = $other->magnitude();
        
        if ($mag1 == 0 || $mag2 == 0) {
            return 0;
        }
        
        return acos($dot / ($mag1 * $mag2));
    }

    /**
     * Rotate the vector by an angle in radians
     */
    public function rotate(float $angle): Vector2D
    {
        $cos = cos($angle);
        $sin = sin($angle);
        
        return new Vector2D(
            $this->x * $cos - $this->y * $sin,
            $this->x * $sin + $this->y * $cos
        );
    }

    /**
     * Calculate perpendicular vector (rotated 90 degrees counter-clockwise)
     */
    public function perpendicular(): Vector2D
    {
        return new Vector2D(-$this->y, $this->x);
    }

    /**
     * Limit the magnitude of the vector to a maximum value
     */
    public function limit(float $max): Vector2D
    {
        $mag = $this->magnitude();
        if ($mag > $max) {
            return $this->normalize()->multiply($max);
        }
        return new Vector2D($this->x, $this->y);
    }

    /**
     * Create a vector from an angle and magnitude
     */
    public static function fromAngle(float $angle, float $magnitude = 1.0): Vector2D
    {
        return new Vector2D(
            cos($angle) * $magnitude,
            sin($angle) * $magnitude
        );
    }

    /**
     * Calculate distance between two vectors
     */
    public static function distance(Vector2D $v1, Vector2D $v2): float
    {
        return $v1->subtract($v2)->magnitude();
    }

    /**
     * Calculate squared distance between two vectors (faster than distance)
     */
    public static function distanceSquared(Vector2D $v1, Vector2D $v2): float
    {
        return $v1->subtract($v2)->magnitudeSquared();
    }

    /**
     * Linear interpolation between two vectors
     */
    public static function lerp(Vector2D $v1, Vector2D $v2, float $t): Vector2D
    {
        $t = max(0, min(1, $t)); // Clamp t between 0 and 1
        return new Vector2D(
            $v1->x + ($v2->x - $v1->x) * $t,
            $v1->y + ($v2->y - $v1->y) * $t
        );
    }

    /**
     * Convert to array [x, y]
     */
    public function toArray(): array
    {
        return [$this->x, $this->y];
    }

    /**
     * Convert to string representation
     */
    public function __toString(): string
    {
        return sprintf('Vector2D(%.2f, %.2f)', $this->x, $this->y);
    }

    /**
     * Check if two vectors are equal (within epsilon)
     */
    public function equals(Vector2D $other, float $epsilon = 0.0001): bool
    {
        return abs($this->x - $other->x) < $epsilon && 
               abs($this->y - $other->y) < $epsilon;
    }
}

