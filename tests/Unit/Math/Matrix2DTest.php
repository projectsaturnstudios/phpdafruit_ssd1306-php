<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Math\Matrix2D;
use PhpdaFruit\SSD1306\Math\Vector2D;

describe('Matrix2D Construction', function () {
    it('creates identity matrix by default', function () {
        $m = new Matrix2D();
        
        expect($m->get(0, 0))->toBe(1.0)
            ->and($m->get(1, 1))->toBe(1.0)
            ->and($m->get(2, 2))->toBe(1.0)
            ->and($m->get(0, 1))->toBe(0.0);
    });

    it('creates matrix from array', function () {
        $values = [
            [2, 0, 0],
            [0, 3, 0],
            [0, 0, 1]
        ];
        $m = new Matrix2D($values);
        
        expect($m->get(0, 0))->toBe(2.0)
            ->and($m->get(1, 1))->toBe(3.0);
    });

    it('throws exception for invalid matrix size', function () {
        new Matrix2D([[1, 2]]);
    })->throws(InvalidArgumentException::class);
});

describe('Matrix2D Factory Methods', function () {
    it('creates identity matrix', function () {
        $m = Matrix2D::identity();
        
        expect($m->get(0, 0))->toBe(1.0)
            ->and($m->get(1, 1))->toBe(1.0)
            ->and($m->get(0, 1))->toBe(0.0);
    });

    it('creates translation matrix', function () {
        $m = Matrix2D::translation(5, 10);
        
        expect($m->get(0, 2))->toBe(5.0)
            ->and($m->get(1, 2))->toBe(10.0);
    });

    it('creates rotation matrix', function () {
        $m = Matrix2D::rotation(M_PI / 2); // 90 degrees
        
        expect($m->get(0, 0))->toBeCloseTo(0.0, 0.0001)
            ->and($m->get(1, 1))->toBeCloseTo(0.0, 0.0001);
    });

    it('creates scaling matrix', function () {
        $m = Matrix2D::scaling(2, 3);
        
        expect($m->get(0, 0))->toBe(2.0)
            ->and($m->get(1, 1))->toBe(3.0);
    });
});

describe('Matrix2D Transformations', function () {
    it('translates matrix', function () {
        $m = Matrix2D::identity()->translate(10, 20);
        $v = new Vector2D(5, 5);
        $result = $m->transform($v);
        
        expect($result->x)->toBe(15.0)
            ->and($result->y)->toBe(25.0);
    });

    it('rotates matrix', function () {
        $m = Matrix2D::identity()->rotate(M_PI / 2); // 90 degrees
        $v = new Vector2D(1, 0);
        $result = $m->transform($v);
        
        expect($result->x)->toBeCloseTo(0.0, 0.0001)
            ->and($result->y)->toBeCloseTo(1.0, 0.0001);
    });

    it('scales matrix', function () {
        $m = Matrix2D::identity()->scale(2, 3);
        $v = new Vector2D(4, 5);
        $result = $m->transform($v);
        
        expect($result->x)->toBe(8.0)
            ->and($result->y)->toBe(15.0);
    });

    it('combines transformations', function () {
        $m = Matrix2D::identity()
            ->translate(10, 10)
            ->scale(2, 2)
            ->rotate(M_PI / 4); // 45 degrees
        
        expect($m)->toBeInstanceOf(Matrix2D::class);
    });
});

describe('Matrix2D Multiplication', function () {
    it('multiplies two matrices', function () {
        $m1 = Matrix2D::scaling(2, 2);
        $m2 = Matrix2D::translation(5, 5);
        $result = $m1->multiply($m2);
        
        expect($result)->toBeInstanceOf(Matrix2D::class);
    });

    it('matrix multiplication is not commutative', function () {
        $m1 = Matrix2D::scaling(2, 2);
        $m2 = Matrix2D::translation(5, 5);
        
        $v = new Vector2D(1, 1);
        
        $result1 = $m1->multiply($m2)->transform($v);
        $result2 = $m2->multiply($m1)->transform($v);
        
        expect($result1->x)->not->toBe($result2->x);
    });
});

describe('Matrix2D Vector Transformation', function () {
    it('transforms a vector', function () {
        $m = Matrix2D::translation(10, 20);
        $v = new Vector2D(5, 5);
        $result = $m->transform($v);
        
        expect($result->x)->toBe(15.0)
            ->and($result->y)->toBe(25.0);
    });

    it('transforms multiple vectors', function () {
        $m = Matrix2D::scaling(2, 2);
        $vectors = [
            new Vector2D(1, 1),
            new Vector2D(2, 2),
            new Vector2D(3, 3)
        ];
        
        $results = $m->transformMany($vectors);
        
        expect($results)->toHaveCount(3)
            ->and($results[0]->x)->toBe(2.0)
            ->and($results[1]->x)->toBe(4.0)
            ->and($results[2]->x)->toBe(6.0);
    });
});

describe('Matrix2D Determinant and Inverse', function () {
    it('calculates determinant', function () {
        $m = Matrix2D::scaling(2, 3);
        $det = $m->determinant();
        
        expect($det)->toBe(6.0);
    });

    it('calculates inverse matrix', function () {
        $m = Matrix2D::scaling(2, 2);
        $inv = $m->inverse();
        
        expect($inv)->toBeInstanceOf(Matrix2D::class)
            ->and($inv->get(0, 0))->toBe(0.5)
            ->and($inv->get(1, 1))->toBe(0.5);
    });

    it('returns null for singular matrix', function () {
        $m = new Matrix2D([
            [1, 2, 3],
            [2, 4, 6],
            [0, 0, 0]
        ]);
        
        expect($m->inverse())->toBeNull();
    });

    it('inverse of inverse equals original', function () {
        $m = Matrix2D::translation(5, 10);
        $inv = $m->inverse();
        $invInv = $inv->inverse();
        
        $v = new Vector2D(1, 1);
        $result1 = $m->transform($v);
        $result2 = $invInv->transform($v);
        
        expect($result1->x)->toBeCloseTo($result2->x, 0.0001)
            ->and($result1->y)->toBeCloseTo($result2->y, 0.0001);
    });
});

describe('Matrix2D Utility Methods', function () {
    it('converts to array', function () {
        $m = Matrix2D::identity();
        $arr = $m->toArray();
        
        expect($arr)->toBeArray()
            ->and($arr)->toHaveCount(3)
            ->and($arr[0])->toHaveCount(3);
    });

    it('gets specific value', function () {
        $m = Matrix2D::scaling(5, 7);
        
        expect($m->get(0, 0))->toBe(5.0)
            ->and($m->get(1, 1))->toBe(7.0);
    });

    it('converts to string', function () {
        $m = Matrix2D::identity();
        $str = (string)$m;
        
        expect($str)->toBeString()
            ->and($str)->toContain('[1.00');
    });
});

describe('Matrix2D Complex Transformations', function () {
    it('applies rotation around a point', function () {
        // Rotate around point (10, 10)
        $center = new Vector2D(10, 10);
        
        // Translate to origin, rotate, translate back
        $m1 = Matrix2D::translation(-$center->x, -$center->y);
        $m2 = Matrix2D::rotation(M_PI);  // 180 degrees
        $m3 = Matrix2D::translation($center->x, $center->y);
        
        // Apply transformations in correct order
        $m = $m3->multiply($m2)->multiply($m1);
        
        $v = new Vector2D(15, 10); // Point 5 units to the right of center
        $result = $m->transform($v);
        
        // Should be 5 units to the left of center after 180° rotation
        expect($result->x)->toBeCloseTo(5.0, 0.0001)
            ->and($result->y)->toBeCloseTo(10.0, 0.0001);
    });

    it('applies scale from a point', function () {
        // Scale 2x from point (10, 10)
        $center = new Vector2D(10, 10);
        
        // Translate to origin, scale, translate back
        $m1 = Matrix2D::translation(-$center->x, -$center->y);
        $m2 = Matrix2D::scaling(2, 2);
        $m3 = Matrix2D::translation($center->x, $center->y);
        
        // Apply transformations in correct order
        $m = $m3->multiply($m2)->multiply($m1);
        
        $v = new Vector2D(15, 15); // 5 units away from center
        $result = $m->transform($v);
        
        // Should be 10 units away from center after 2x scale
        expect($result->x)->toBe(20.0)
            ->and($result->y)->toBe(20.0);
    });
});

