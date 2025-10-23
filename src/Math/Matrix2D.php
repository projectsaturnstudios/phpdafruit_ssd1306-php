<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Math;

/**
 * 2D Transformation Matrix
 * 
 * 3x3 matrix for 2D affine transformations using homogeneous coordinates.
 * Supports translation, rotation, scaling, and arbitrary transformations.
 */
class Matrix2D
{
    private array $m;

    /**
     * Create a new matrix (default is identity)
     */
    public function __construct(?array $values = null)
    {
        if ($values === null) {
            // Identity matrix
            $this->m = [
                [1, 0, 0],
                [0, 1, 0],
                [0, 0, 1]
            ];
        } else {
            if (count($values) !== 3 || count($values[0]) !== 3) {
                throw new \InvalidArgumentException('Matrix must be 3x3');
            }
            $this->m = $values;
        }
    }

    /**
     * Create an identity matrix
     */
    public static function identity(): Matrix2D
    {
        return new Matrix2D();
    }

    /**
     * Create a translation matrix
     */
    public static function translation(float $x, float $y): Matrix2D
    {
        return new Matrix2D([
            [1, 0, $x],
            [0, 1, $y],
            [0, 0, 1]
        ]);
    }

    /**
     * Create a rotation matrix (angle in radians)
     */
    public static function rotation(float $angle): Matrix2D
    {
        $cos = cos($angle);
        $sin = sin($angle);
        
        return new Matrix2D([
            [$cos, -$sin, 0],
            [$sin, $cos, 0],
            [0, 0, 1]
        ]);
    }

    /**
     * Create a scaling matrix
     */
    public static function scaling(float $sx, float $sy): Matrix2D
    {
        return new Matrix2D([
            [$sx, 0, 0],
            [0, $sy, 0],
            [0, 0, 1]
        ]);
    }

    /**
     * Translate this matrix
     */
    public function translate(float $x, float $y): Matrix2D
    {
        return $this->multiply(Matrix2D::translation($x, $y));
    }

    /**
     * Rotate this matrix
     */
    public function rotate(float $angle): Matrix2D
    {
        return $this->multiply(Matrix2D::rotation($angle));
    }

    /**
     * Scale this matrix
     */
    public function scale(float $sx, float $sy): Matrix2D
    {
        return $this->multiply(Matrix2D::scaling($sx, $sy));
    }

    /**
     * Multiply this matrix by another matrix
     */
    public function multiply(Matrix2D $other): Matrix2D
    {
        $result = [];
        
        for ($i = 0; $i < 3; $i++) {
            $result[$i] = [];
            for ($j = 0; $j < 3; $j++) {
                $sum = 0;
                for ($k = 0; $k < 3; $k++) {
                    $sum += $this->m[$i][$k] * $other->m[$k][$j];
                }
                $result[$i][$j] = $sum;
            }
        }
        
        return new Matrix2D($result);
    }

    /**
     * Transform a vector by this matrix
     */
    public function transform(Vector2D $v): Vector2D
    {
        $x = $this->m[0][0] * $v->x + $this->m[0][1] * $v->y + $this->m[0][2];
        $y = $this->m[1][0] * $v->x + $this->m[1][1] * $v->y + $this->m[1][2];
        
        return new Vector2D($x, $y);
    }

    /**
     * Transform multiple vectors
     */
    public function transformMany(array $vectors): array
    {
        return array_map(fn($v) => $this->transform($v), $vectors);
    }

    /**
     * Get the determinant of the matrix
     */
    public function determinant(): float
    {
        return $this->m[0][0] * ($this->m[1][1] * $this->m[2][2] - $this->m[1][2] * $this->m[2][1])
             - $this->m[0][1] * ($this->m[1][0] * $this->m[2][2] - $this->m[1][2] * $this->m[2][0])
             + $this->m[0][2] * ($this->m[1][0] * $this->m[2][1] - $this->m[1][1] * $this->m[2][0]);
    }

    /**
     * Get the inverse of this matrix
     */
    public function inverse(): ?Matrix2D
    {
        $det = $this->determinant();
        
        if (abs($det) < 0.0001) {
            return null; // Matrix is singular
        }
        
        $invDet = 1.0 / $det;
        
        $result = [
            [
                ($this->m[1][1] * $this->m[2][2] - $this->m[1][2] * $this->m[2][1]) * $invDet,
                ($this->m[0][2] * $this->m[2][1] - $this->m[0][1] * $this->m[2][2]) * $invDet,
                ($this->m[0][1] * $this->m[1][2] - $this->m[0][2] * $this->m[1][1]) * $invDet
            ],
            [
                ($this->m[1][2] * $this->m[2][0] - $this->m[1][0] * $this->m[2][2]) * $invDet,
                ($this->m[0][0] * $this->m[2][2] - $this->m[0][2] * $this->m[2][0]) * $invDet,
                ($this->m[0][2] * $this->m[1][0] - $this->m[0][0] * $this->m[1][2]) * $invDet
            ],
            [
                ($this->m[1][0] * $this->m[2][1] - $this->m[1][1] * $this->m[2][0]) * $invDet,
                ($this->m[0][1] * $this->m[2][0] - $this->m[0][0] * $this->m[2][1]) * $invDet,
                ($this->m[0][0] * $this->m[1][1] - $this->m[0][1] * $this->m[1][0]) * $invDet
            ]
        ];
        
        return new Matrix2D($result);
    }

    /**
     * Get matrix values as array
     */
    public function toArray(): array
    {
        return $this->m;
    }

    /**
     * Get a specific value from the matrix
     */
    public function get(int $row, int $col): float
    {
        return $this->m[$row][$col];
    }

    /**
     * Convert to string representation
     */
    public function __toString(): string
    {
        $lines = [];
        foreach ($this->m as $row) {
            $lines[] = sprintf('[%.2f, %.2f, %.2f]', $row[0], $row[1], $row[2]);
        }
        return implode("\n", $lines);
    }
}

