<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Math;

/**
 * Curve and interpolation utilities
 * 
 * Provides Bezier curves, easing functions, and interpolation
 * for smooth animations and paths.
 */
class Curve
{
    /**
     * Linear interpolation between two values
     */
    public static function lerp(float $a, float $b, float $t): float
    {
        return $a + ($b - $a) * $t;
    }

    /**
     * Clamp a value between min and max
     */
    public static function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }

    /**
     * Map a value from one range to another
     */
    public static function map(float $value, float $inMin, float $inMax, float $outMin, float $outMax): float
    {
        $inRange = $inMax - $inMin;
        if ($inRange == 0) {
            return $outMin; // If input range is zero, return minimum output
        }
        return ($value - $inMin) * ($outMax - $outMin) / $inRange + $outMin;
    }

    /**
     * Quadratic Bezier curve
     * 
     * @param Vector2D $p0 Start point
     * @param Vector2D $p1 Control point
     * @param Vector2D $p2 End point
     * @param float $t Parameter (0 to 1)
     */
    public static function quadraticBezier(Vector2D $p0, Vector2D $p1, Vector2D $p2, float $t): Vector2D
    {
        $t = self::clamp($t, 0, 1);
        $mt = 1 - $t;
        
        $x = $mt * $mt * $p0->x + 2 * $mt * $t * $p1->x + $t * $t * $p2->x;
        $y = $mt * $mt * $p0->y + 2 * $mt * $t * $p1->y + $t * $t * $p2->y;
        
        return new Vector2D($x, $y);
    }

    /**
     * Cubic Bezier curve
     * 
     * @param Vector2D $p0 Start point
     * @param Vector2D $p1 First control point
     * @param Vector2D $p2 Second control point
     * @param Vector2D $p3 End point
     * @param float $t Parameter (0 to 1)
     */
    public static function cubicBezier(Vector2D $p0, Vector2D $p1, Vector2D $p2, Vector2D $p3, float $t): Vector2D
    {
        $t = self::clamp($t, 0, 1);
        $mt = 1 - $t;
        $mt2 = $mt * $mt;
        $mt3 = $mt2 * $mt;
        $t2 = $t * $t;
        $t3 = $t2 * $t;
        
        $x = $mt3 * $p0->x + 3 * $mt2 * $t * $p1->x + 3 * $mt * $t2 * $p2->x + $t3 * $p3->x;
        $y = $mt3 * $p0->y + 3 * $mt2 * $t * $p1->y + 3 * $mt * $t2 * $p2->y + $t3 * $p3->y;
        
        return new Vector2D($x, $y);
    }

    /**
     * Catmull-Rom spline (smooth curve through points)
     */
    public static function catmullRom(Vector2D $p0, Vector2D $p1, Vector2D $p2, Vector2D $p3, float $t): Vector2D
    {
        $t = self::clamp($t, 0, 1);
        $t2 = $t * $t;
        $t3 = $t2 * $t;
        
        $x = 0.5 * (
            (2 * $p1->x) +
            (-$p0->x + $p2->x) * $t +
            (2 * $p0->x - 5 * $p1->x + 4 * $p2->x - $p3->x) * $t2 +
            (-$p0->x + 3 * $p1->x - 3 * $p2->x + $p3->x) * $t3
        );
        
        $y = 0.5 * (
            (2 * $p1->y) +
            (-$p0->y + $p2->y) * $t +
            (2 * $p0->y - 5 * $p1->y + 4 * $p2->y - $p3->y) * $t2 +
            (-$p0->y + 3 * $p1->y - 3 * $p2->y + $p3->y) * $t3
        );
        
        return new Vector2D($x, $y);
    }

    // ========================================================================
    // Easing Functions
    // ========================================================================

    /**
     * Linear easing (no acceleration)
     */
    public static function easeLinear(float $t): float
    {
        return self::clamp($t, 0, 1);
    }

    /**
     * Ease in (accelerating from zero velocity)
     */
    public static function easeIn(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        return $t * $t;
    }

    /**
     * Ease out (decelerating to zero velocity)
     */
    public static function easeOut(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        return $t * (2 - $t);
    }

    /**
     * Ease in-out (accelerating and decelerating)
     */
    public static function easeInOut(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        return $t < 0.5 ? 2 * $t * $t : -1 + (4 - 2 * $t) * $t;
    }

    /**
     * Cubic ease in
     */
    public static function easeInCubic(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        return $t * $t * $t;
    }

    /**
     * Cubic ease out
     */
    public static function easeOutCubic(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        $t = $t - 1;
        return $t * $t * $t + 1;
    }

    /**
     * Cubic ease in-out
     */
    public static function easeInOutCubic(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        return $t < 0.5 ? 4 * $t * $t * $t : 1 + (--$t) * (2 * $t) * (2 * $t);
    }

    /**
     * Exponential ease in
     */
    public static function easeInExpo(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        return $t == 0 ? 0 : pow(2, 10 * ($t - 1));
    }

    /**
     * Exponential ease out
     */
    public static function easeOutExpo(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        return $t == 1 ? 1 : 1 - pow(2, -10 * $t);
    }

    /**
     * Exponential ease in-out
     */
    public static function easeInOutExpo(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        if ($t == 0 || $t == 1) {
            return $t;
        }
        
        $t = $t * 2;
        if ($t < 1) {
            return 0.5 * pow(2, 10 * ($t - 1));
        }
        
        return 0.5 * (2 - pow(2, -10 * ($t - 1)));
    }

    /**
     * Elastic ease in (bounce effect)
     */
    public static function easeInElastic(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        if ($t == 0 || $t == 1) {
            return $t;
        }
        
        $p = 0.3;
        $s = $p / 4;
        $t = $t - 1;
        
        return -(pow(2, 10 * $t) * sin(($t - $s) * (2 * M_PI) / $p));
    }

    /**
     * Elastic ease out (bounce effect)
     */
    public static function easeOutElastic(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        if ($t == 0 || $t == 1) {
            return $t;
        }
        
        $p = 0.3;
        $s = $p / 4;
        
        return pow(2, -10 * $t) * sin(($t - $s) * (2 * M_PI) / $p) + 1;
    }

    /**
     * Back ease in (anticipation)
     */
    public static function easeInBack(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        $s = 1.70158;
        return $t * $t * (($s + 1) * $t - $s);
    }

    /**
     * Back ease out (overshoot)
     */
    public static function easeOutBack(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        $s = 1.70158;
        $t = $t - 1;
        return $t * $t * (($s + 1) * $t + $s) + 1;
    }

    /**
     * Bounce ease out
     */
    public static function easeOutBounce(float $t): float
    {
        $t = self::clamp($t, 0, 1);
        
        if ($t < 1 / 2.75) {
            return 7.5625 * $t * $t;
        } elseif ($t < 2 / 2.75) {
            $t = $t - (1.5 / 2.75);
            return 7.5625 * $t * $t + 0.75;
        } elseif ($t < 2.5 / 2.75) {
            $t = $t - (2.25 / 2.75);
            return 7.5625 * $t * $t + 0.9375;
        } else {
            $t = $t - (2.625 / 2.75);
            return 7.5625 * $t * $t + 0.984375;
        }
    }

    /**
     * Bounce ease in
     */
    public static function easeInBounce(float $t): float
    {
        return 1 - self::easeOutBounce(1 - $t);
    }
}

