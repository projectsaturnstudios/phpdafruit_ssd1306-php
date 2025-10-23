<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Math\Vector2D;

describe('Vector2D Construction', function () {
    it('creates vector with default values', function () {
        $v = new Vector2D();
        
        expect($v->x)->toBe(0.0)
            ->and($v->y)->toBe(0.0);
    });

    it('creates vector with specific values', function () {
        $v = new Vector2D(3.5, 4.2);
        
        expect($v->x)->toBe(3.5)
            ->and($v->y)->toBe(4.2);
    });
});

describe('Vector2D Basic Operations', function () {
    it('adds two vectors', function () {
        $v1 = new Vector2D(3, 4);
        $v2 = new Vector2D(1, 2);
        $result = $v1->add($v2);
        
        expect($result->x)->toBe(4.0)
            ->and($result->y)->toBe(6.0);
    });

    it('subtracts two vectors', function () {
        $v1 = new Vector2D(5, 7);
        $v2 = new Vector2D(2, 3);
        $result = $v1->subtract($v2);
        
        expect($result->x)->toBe(3.0)
            ->and($result->y)->toBe(4.0);
    });

    it('multiplies vector by scalar', function () {
        $v = new Vector2D(2, 3);
        $result = $v->multiply(3);
        
        expect($result->x)->toBe(6.0)
            ->and($result->y)->toBe(9.0);
    });

    it('divides vector by scalar', function () {
        $v = new Vector2D(6, 9);
        $result = $v->divide(3);
        
        expect($result->x)->toBe(2.0)
            ->and($result->y)->toBe(3.0);
    });

    it('throws exception when dividing by zero', function () {
        $v = new Vector2D(6, 9);
        $v->divide(0);
    })->throws(InvalidArgumentException::class);
});

describe('Vector2D Magnitude and Normalization', function () {
    it('calculates magnitude', function () {
        $v = new Vector2D(3, 4);
        
        expect($v->magnitude())->toBe(5.0);
    });

    it('calculates squared magnitude', function () {
        $v = new Vector2D(3, 4);
        
        expect($v->magnitudeSquared())->toBe(25.0);
    });

    it('normalizes vector to unit length', function () {
        $v = new Vector2D(3, 4);
        $normalized = $v->normalize();
        
        expect($normalized->magnitude())->toBeCloseTo(1.0, 0.0001);
    });

    it('handles normalization of zero vector', function () {
        $v = new Vector2D(0, 0);
        $normalized = $v->normalize();
        
        expect($normalized->x)->toBe(0.0)
            ->and($normalized->y)->toBe(0.0);
    });
});

describe('Vector2D Dot Product and Angles', function () {
    it('calculates dot product', function () {
        $v1 = new Vector2D(2, 3);
        $v2 = new Vector2D(4, 5);
        
        expect($v1->dot($v2))->toBe(23.0); // 2*4 + 3*5 = 23
    });

    it('calculates angle of vector', function () {
        $v = new Vector2D(1, 0);
        
        expect($v->angle())->toBe(0.0);
    });

    it('calculates angle between vectors', function () {
        $v1 = new Vector2D(1, 0);
        $v2 = new Vector2D(0, 1);
        
        expect($v1->angleBetween($v2))->toBeCloseTo(M_PI / 2, 0.0001); // 90 degrees
    });

    it('handles angle between zero vectors', function () {
        $v1 = new Vector2D(0, 0);
        $v2 = new Vector2D(1, 1);
        
        expect($v1->angleBetween($v2))->toBe(0.0);
    });
});

describe('Vector2D Rotation and Transformation', function () {
    it('rotates vector by angle', function () {
        $v = new Vector2D(1, 0);
        $rotated = $v->rotate(M_PI / 2); // 90 degrees
        
        expect($rotated->x)->toBeCloseTo(0.0, 0.0001)
            ->and($rotated->y)->toBeCloseTo(1.0, 0.0001);
    });

    it('calculates perpendicular vector', function () {
        $v = new Vector2D(3, 4);
        $perp = $v->perpendicular();
        
        expect($perp->x)->toBe(-4.0)
            ->and($perp->y)->toBe(3.0);
    });

    it('limits vector magnitude', function () {
        $v = new Vector2D(6, 8); // magnitude = 10
        $limited = $v->limit(5);
        
        expect($limited->magnitude())->toBeCloseTo(5.0, 0.0001);
    });

    it('does not limit vector below max', function () {
        $v = new Vector2D(3, 4); // magnitude = 5
        $limited = $v->limit(10);
        
        expect($limited->magnitude())->toBeCloseTo(5.0, 0.0001);
    });
});

describe('Vector2D Static Helpers', function () {
    it('creates vector from angle', function () {
        $v = Vector2D::fromAngle(M_PI / 2, 5); // 90 degrees, magnitude 5
        
        expect($v->x)->toBeCloseTo(0.0, 0.0001)
            ->and($v->y)->toBeCloseTo(5.0, 0.0001);
    });

    it('creates unit vector from angle', function () {
        $v = Vector2D::fromAngle(0);
        
        expect($v->x)->toBe(1.0)
            ->and($v->y)->toBeCloseTo(0.0, 0.0001)
            ->and($v->magnitude())->toBeCloseTo(1.0, 0.0001);
    });

    it('calculates distance between vectors', function () {
        $v1 = new Vector2D(0, 0);
        $v2 = new Vector2D(3, 4);
        
        expect(Vector2D::distance($v1, $v2))->toBe(5.0);
    });

    it('calculates squared distance between vectors', function () {
        $v1 = new Vector2D(0, 0);
        $v2 = new Vector2D(3, 4);
        
        expect(Vector2D::distanceSquared($v1, $v2))->toBe(25.0);
    });

    it('interpolates between vectors', function () {
        $v1 = new Vector2D(0, 0);
        $v2 = new Vector2D(10, 10);
        $mid = Vector2D::lerp($v1, $v2, 0.5);
        
        expect($mid->x)->toBe(5.0)
            ->and($mid->y)->toBe(5.0);
    });

    it('clamps lerp parameter', function () {
        $v1 = new Vector2D(0, 0);
        $v2 = new Vector2D(10, 10);
        $result = Vector2D::lerp($v1, $v2, 1.5);
        
        expect($result->x)->toBe(10.0)
            ->and($result->y)->toBe(10.0);
    });
});

describe('Vector2D Utility Methods', function () {
    it('converts to array', function () {
        $v = new Vector2D(3.5, 4.2);
        $arr = $v->toArray();
        
        expect($arr)->toBe([3.5, 4.2]);
    });

    it('converts to string', function () {
        $v = new Vector2D(3.5, 4.2);
        $str = (string)$v;
        
        expect($str)->toContain('3.50')
            ->and($str)->toContain('4.20');
    });

    it('checks equality', function () {
        $v1 = new Vector2D(3.0, 4.0);
        $v2 = new Vector2D(3.0, 4.0);
        
        expect($v1->equals($v2))->toBeTrue();
    });

    it('checks equality with epsilon', function () {
        $v1 = new Vector2D(3.0, 4.0);
        $v2 = new Vector2D(3.0001, 4.0001);
        
        expect($v1->equals($v2, 0.001))->toBeTrue();
    });

    it('detects inequality', function () {
        $v1 = new Vector2D(3.0, 4.0);
        $v2 = new Vector2D(5.0, 6.0);
        
        expect($v1->equals($v2))->toBeFalse();
    });
});

