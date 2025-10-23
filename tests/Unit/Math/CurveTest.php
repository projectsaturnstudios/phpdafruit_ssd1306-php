<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Math\Curve;
use PhpdaFruit\SSD1306\Math\Vector2D;

describe('Curve Basic Functions', function () {
    it('performs linear interpolation', function () {
        $result = Curve::lerp(0, 10, 0.5);
        
        expect($result)->toBe(5.0);
    });

    it('clamps values', function () {
        expect(Curve::clamp(5, 0, 10))->toBe(5.0)
            ->and(Curve::clamp(-5, 0, 10))->toBe(0.0)
            ->and(Curve::clamp(15, 0, 10))->toBe(10.0);
    });

    it('maps values between ranges', function () {
        $result = Curve::map(5, 0, 10, 0, 100);
        
        expect($result)->toBe(50.0);
    });

    it('maps values with different ranges', function () {
        $result = Curve::map(50, 0, 100, -1, 1);
        
        expect($result)->toBe(0.0);
    });
});

describe('Curve Bezier Functions', function () {
    it('calculates quadratic bezier at start', function () {
        $p0 = new Vector2D(0, 0);
        $p1 = new Vector2D(5, 10);
        $p2 = new Vector2D(10, 0);
        
        $result = Curve::quadraticBezier($p0, $p1, $p2, 0);
        
        expect($result->x)->toBe(0.0)
            ->and($result->y)->toBe(0.0);
    });

    it('calculates quadratic bezier at end', function () {
        $p0 = new Vector2D(0, 0);
        $p1 = new Vector2D(5, 10);
        $p2 = new Vector2D(10, 0);
        
        $result = Curve::quadraticBezier($p0, $p1, $p2, 1);
        
        expect($result->x)->toBe(10.0)
            ->and($result->y)->toBe(0.0);
    });

    it('calculates quadratic bezier at midpoint', function () {
        $p0 = new Vector2D(0, 0);
        $p1 = new Vector2D(5, 10);
        $p2 = new Vector2D(10, 0);
        
        $result = Curve::quadraticBezier($p0, $p1, $p2, 0.5);
        
        expect($result->x)->toBe(5.0)
            ->and($result->y)->toBe(5.0);
    });

    it('clamps quadratic bezier t parameter', function () {
        $p0 = new Vector2D(0, 0);
        $p1 = new Vector2D(5, 10);
        $p2 = new Vector2D(10, 0);
        
        $result = Curve::quadraticBezier($p0, $p1, $p2, 1.5);
        
        expect($result->x)->toBe(10.0); // Clamped to t=1
    });

    it('calculates cubic bezier at start', function () {
        $p0 = new Vector2D(0, 0);
        $p1 = new Vector2D(3, 10);
        $p2 = new Vector2D(7, 10);
        $p3 = new Vector2D(10, 0);
        
        $result = Curve::cubicBezier($p0, $p1, $p2, $p3, 0);
        
        expect($result->x)->toBe(0.0)
            ->and($result->y)->toBe(0.0);
    });

    it('calculates cubic bezier at end', function () {
        $p0 = new Vector2D(0, 0);
        $p1 = new Vector2D(3, 10);
        $p2 = new Vector2D(7, 10);
        $p3 = new Vector2D(10, 0);
        
        $result = Curve::cubicBezier($p0, $p1, $p2, $p3, 1);
        
        expect($result->x)->toBe(10.0)
            ->and($result->y)->toBe(0.0);
    });

    it('calculates catmull-rom spline', function () {
        $p0 = new Vector2D(0, 0);
        $p1 = new Vector2D(0, 10);
        $p2 = new Vector2D(10, 10);
        $p3 = new Vector2D(10, 0);
        
        $result = Curve::catmullRom($p0, $p1, $p2, $p3, 0);
        
        // At t=0, should be at p1
        expect($result->x)->toBe(0.0)
            ->and($result->y)->toBe(10.0);
    });
});

describe('Curve Easing Functions', function () {
    it('performs linear easing', function () {
        expect(Curve::easeLinear(0))->toBe(0.0)
            ->and(Curve::easeLinear(0.5))->toBe(0.5)
            ->and(Curve::easeLinear(1))->toBe(1.0);
    });

    it('performs ease in', function () {
        $result = Curve::easeIn(0.5);
        
        expect($result)->toBe(0.25); // t² at t=0.5
    });

    it('performs ease out', function () {
        $result = Curve::easeOut(0.5);
        
        expect($result)->toBe(0.75); // t(2-t) at t=0.5
    });

    it('performs ease in-out', function () {
        $result = Curve::easeInOut(0.5);
        
        expect($result)->toBe(0.5);
    });

    it('easeIn starts slow', function () {
        $early = Curve::easeIn(0.1);
        
        expect($early)->toBeLessThan(0.1); // Slower than linear
    });

    it('easeOut ends slow', function () {
        $late = Curve::easeOut(0.9);
        
        expect($late)->toBeGreaterThan(0.9); // Faster than linear
    });
});

describe('Curve Cubic Easing', function () {
    it('performs cubic ease in', function () {
        $result = Curve::easeInCubic(0.5);
        
        expect($result)->toBe(0.125); // t³ at t=0.5
    });

    it('performs cubic ease out', function () {
        $result = Curve::easeOutCubic(0.5);
        
        expect($result)->toBe(0.875);
    });

    it('performs cubic ease in-out', function () {
        expect(Curve::easeInOutCubic(0))->toBe(0.0)
            ->and(Curve::easeInOutCubic(1))->toBe(1.0)
            ->and(Curve::easeInOutCubic(0.5))->toBe(0.5);
    });
});

describe('Curve Exponential Easing', function () {
    it('performs exponential ease in', function () {
        expect(Curve::easeInExpo(0))->toBe(0.0)
            ->and(Curve::easeInExpo(1))->toBe(1.0);
    });

    it('performs exponential ease out', function () {
        expect(Curve::easeOutExpo(0))->toBe(0.0)
            ->and(Curve::easeOutExpo(1))->toBe(1.0);
    });

    it('performs exponential ease in-out', function () {
        expect(Curve::easeInOutExpo(0))->toBe(0.0)
            ->and(Curve::easeInOutExpo(1))->toBe(1.0)
            ->and(Curve::easeInOutExpo(0.5))->toBe(0.5);
    });
});

describe('Curve Elastic Easing', function () {
    it('performs elastic ease in', function () {
        expect(Curve::easeInElastic(0))->toBe(0.0)
            ->and(Curve::easeInElastic(1))->toBe(1.0);
    });

    it('performs elastic ease out', function () {
        expect(Curve::easeOutElastic(0))->toBe(0.0)
            ->and(Curve::easeOutElastic(1))->toBe(1.0);
    });

    it('elastic easing overshoots', function () {
        $mid = Curve::easeOutElastic(0.7);
        
        // Elastic should overshoot 1.0 at some point
        expect($mid)->toBeFloat();
    });
});

describe('Curve Back Easing', function () {
    it('performs back ease in', function () {
        expect(Curve::easeInBack(0))->toBe(0.0)
            ->and(Curve::easeInBack(1))->toBeCloseTo(1.0, 0.0001);
    });

    it('performs back ease out', function () {
        expect(Curve::easeOutBack(0))->toBeCloseTo(0.0, 0.0001)
            ->and(Curve::easeOutBack(1))->toBeCloseTo(1.0, 0.0001);
    });

    it('back easing goes negative (anticipation)', function () {
        $early = Curve::easeInBack(0.1);
        
        expect($early)->toBeLessThan(0); // Goes backward
    });

    it('back easing overshoots (overshoot)', function () {
        $late = Curve::easeOutBack(0.9);
        
        expect($late)->toBeGreaterThan(1); // Goes beyond target
    });
});

describe('Curve Bounce Easing', function () {
    it('performs bounce ease out', function () {
        expect(Curve::easeOutBounce(0))->toBe(0.0)
            ->and(Curve::easeOutBounce(1))->toBeCloseTo(1.0, 0.0001);
    });

    it('performs bounce ease in', function () {
        expect(Curve::easeInBounce(0))->toBeCloseTo(0.0, 0.0001)
            ->and(Curve::easeInBounce(1))->toBe(1.0);
    });

    it('bounce has multiple peaks', function () {
        $samples = [];
        for ($t = 0; $t <= 1; $t += 0.1) {
            $samples[] = Curve::easeOutBounce($t);
        }
        
        // Should have varying values indicating bounces
        expect($samples)->toHaveCount(11);
    });
});

describe('Curve Edge Cases', function () {
    it('handles zero magnitude vectors in bezier', function () {
        $p0 = new Vector2D(0, 0);
        $p1 = new Vector2D(0, 0);
        $p2 = new Vector2D(0, 0);
        
        $result = Curve::quadraticBezier($p0, $p1, $p2, 0.5);
        
        expect($result->x)->toBe(0.0)
            ->and($result->y)->toBe(0.0);
    });

    it('clamps easing functions to 0-1', function () {
        expect(Curve::easeIn(-0.5))->toBe(0.0)
            ->and(Curve::easeOut(1.5))->toBe(1.0);
    });

    it('handles map with zero range', function () {
        $result = Curve::map(5, 0, 0, 0, 100);
        
        expect($result)->toBe(0.0); // Returns minimum output when input range is zero
    });
});

