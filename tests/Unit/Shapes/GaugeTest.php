<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Shapes\Gauge;

describe('Gauge Construction', function () {
    it('creates with default parameters', function () {
        $gauge = new Gauge(64, 16, 12, 50);
        
        expect($gauge->cx)->toBe(64)
            ->and($gauge->cy)->toBe(16)
            ->and($gauge->radius)->toBe(12)
            ->and($gauge->value)->toBe(50.0)
            ->and($gauge->min)->toBe(0.0)
            ->and($gauge->max)->toBe(100.0);
    });

    it('creates with custom range', function () {
        $gauge = new Gauge(64, 16, 12, 50, -50, 150);
        
        expect($gauge->min)->toBe(-50.0)
            ->and($gauge->max)->toBe(150.0);
    });

    it('clamps value to range', function () {
        $gauge1 = new Gauge(64, 16, 12, -10, 0, 100);
        $gauge2 = new Gauge(64, 16, 12, 150, 0, 100);
        
        expect($gauge1->value)->toBe(0.0)
            ->and($gauge2->value)->toBe(100.0);
    });
});

describe('Gauge Angle Calculations', function () {
    it('calculates value angle for half circle gauge', function () {
        $gauge = new Gauge(64, 16, 12, 50, 0, 100, Gauge::STYLE_HALF_CIRCLE, 180, 360);
        $angle = $gauge->getValueAngle();
        
        expect($angle)->toBe(270.0); // 50% of 180-360 range
    });

    it('calculates value angle at min', function () {
        $gauge = new Gauge(64, 16, 12, 0, 0, 100, Gauge::STYLE_HALF_CIRCLE, 180, 360);
        
        expect($gauge->getValueAngle())->toBe(180.0);
    });

    it('calculates value angle at max', function () {
        $gauge = new Gauge(64, 16, 12, 100, 0, 100, Gauge::STYLE_HALF_CIRCLE, 180, 360);
        
        expect($gauge->getValueAngle())->toBe(360.0);
    });
});

describe('Gauge Percentage', function () {
    it('calculates correct percentage', function () {
        $gauge = new Gauge(64, 16, 12, 50);
        
        expect($gauge->getPercent())->toBe(50.0);
    });

    it('calculates percentage with custom range', function () {
        $gauge = new Gauge(64, 16, 12, 50, 0, 200);
        
        expect($gauge->getPercent())->toBe(25.0);
    });
});

describe('Gauge Needle Point', function () {
    it('calculates needle point coordinates', function () {
        $gauge = new Gauge(64, 16, 12, 50, 0, 100, Gauge::STYLE_HALF_CIRCLE, 180, 360);
        $point = $gauge->getNeedlePoint();
        
        expect($point)->toHaveKey('x')
            ->and($point)->toHaveKey('y')
            ->and($point['x'])->toBeInt()
            ->and($point['y'])->toBeInt();
    });

    it('needle point changes with value', function () {
        $gauge1 = new Gauge(64, 16, 12, 25);
        $gauge2 = new Gauge(64, 16, 12, 75);
        
        $point1 = $gauge1->getNeedlePoint();
        $point2 = $gauge2->getNeedlePoint();
        
        expect($point1['x'])->not->toBe($point2['x']);
    });
});

describe('Gauge Bounds', function () {
    it('returns correct bounds', function () {
        $gauge = new Gauge(64, 16, 12, 50);
        $bounds = $gauge->getBounds();
        
        expect($bounds['x'])->toBe(52) // 64 - 12
            ->and($bounds['y'])->toBe(4)  // 16 - 12
            ->and($bounds['width'])->toBe(24) // 12 * 2
            ->and($bounds['height'])->toBe(24);
    });
});

describe('Gauge Styles', function () {
    it('supports full circle style', function () {
        $gauge = new Gauge(64, 16, 12, 50, style: Gauge::STYLE_FULL_CIRCLE);
        
        expect($gauge->style)->toBe(Gauge::STYLE_FULL_CIRCLE);
    });

    it('supports half circle style', function () {
        $gauge = new Gauge(64, 16, 12, 50, style: Gauge::STYLE_HALF_CIRCLE);
        
        expect($gauge->style)->toBe(Gauge::STYLE_HALF_CIRCLE);
    });

    it('supports arc style', function () {
        $gauge = new Gauge(64, 16, 12, 50, style: Gauge::STYLE_ARC);
        
        expect($gauge->style)->toBe(Gauge::STYLE_ARC);
    });
});

describe('Gauge Configuration', function () {
    it('supports tick marks configuration', function () {
        $gauge = new Gauge(64, 16, 12, 50, showTicks: true, tickCount: 7);
        
        expect($gauge->showTicks)->toBeTrue()
            ->and($gauge->tickCount)->toBe(7);
    });

    it('supports value display configuration', function () {
        $gauge = new Gauge(64, 16, 12, 50, showValue: true);
        
        expect($gauge->showValue)->toBeTrue();
    });
});

