<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Shapes\ProgressBar;

describe('ProgressBar Construction', function () {
    it('creates with default parameters', function () {
        $bar = new ProgressBar(0, 0, 100, 10);
        
        expect($bar->x)->toBe(0)
            ->and($bar->y)->toBe(0)
            ->and($bar->width)->toBe(100)
            ->and($bar->height)->toBe(10)
            ->and($bar->percent)->toBe(0.0)
            ->and($bar->style)->toBe(ProgressBar::STYLE_SOLID);
    });

    it('creates with custom parameters', function () {
        $bar = new ProgressBar(
            10, 20, 80, 8, 75.5, 
            ProgressBar::STYLE_SEGMENTED, 
            ProgressBar::ORIENTATION_VERTICAL
        );
        
        expect($bar->x)->toBe(10)
            ->and($bar->y)->toBe(20)
            ->and($bar->percent)->toBe(75.5)
            ->and($bar->style)->toBe(ProgressBar::STYLE_SEGMENTED)
            ->and($bar->orientation)->toBe(ProgressBar::ORIENTATION_VERTICAL);
    });

    it('clamps percent to 0-100 range', function () {
        $bar1 = new ProgressBar(0, 0, 100, 10, -10);
        $bar2 = new ProgressBar(0, 0, 100, 10, 150);
        
        expect($bar1->percent)->toBe(0.0)
            ->and($bar2->percent)->toBe(100.0);
    });
});

describe('ProgressBar Factory Methods', function () {
    it('creates horizontal progress bar', function () {
        $bar = ProgressBar::horizontal(10, 10, 100, 8, 50);
        
        expect($bar->orientation)->toBe(ProgressBar::ORIENTATION_HORIZONTAL)
            ->and($bar->percent)->toBe(50.0);
    });

    it('creates vertical progress bar', function () {
        $bar = ProgressBar::vertical(10, 10, 8, 100, 75);
        
        expect($bar->orientation)->toBe(ProgressBar::ORIENTATION_VERTICAL)
            ->and($bar->percent)->toBe(75.0);
    });
});

describe('ProgressBar Calculations', function () {
    it('calculates filled size for horizontal bar', function () {
        $bar = ProgressBar::horizontal(0, 0, 102, 10, 50); // 102 width - 2 border = 100 usable
        
        expect($bar->getFilledSize())->toBe(50);
    });

    it('calculates filled size for vertical bar', function () {
        $bar = ProgressBar::vertical(0, 0, 10, 102, 50);
        
        expect($bar->getFilledSize())->toBe(50);
    });

    it('returns zero filled size at 0%', function () {
        $bar = ProgressBar::horizontal(0, 0, 100, 10, 0);
        
        expect($bar->getFilledSize())->toBe(0);
    });

    it('returns full filled size at 100%', function () {
        $bar = ProgressBar::horizontal(0, 0, 102, 10, 100);
        
        expect($bar->getFilledSize())->toBe(100);
    });
});

describe('ProgressBar Bounds', function () {
    it('returns correct bounds', function () {
        $bar = new ProgressBar(10, 20, 100, 8);
        $bounds = $bar->getBounds();
        
        expect($bounds)->toHaveKey('x')
            ->and($bounds['x'])->toBe(10)
            ->and($bounds['y'])->toBe(20)
            ->and($bounds['width'])->toBe(100)
            ->and($bounds['height'])->toBe(8);
    });
});

describe('ProgressBar Styles', function () {
    it('supports solid style', function () {
        $bar = new ProgressBar(0, 0, 100, 10, 50, ProgressBar::STYLE_SOLID);
        
        expect($bar->style)->toBe(ProgressBar::STYLE_SOLID);
    });

    it('supports segmented style', function () {
        $bar = new ProgressBar(0, 0, 100, 10, 50, ProgressBar::STYLE_SEGMENTED);
        
        expect($bar->style)->toBe(ProgressBar::STYLE_SEGMENTED);
    });

    it('supports rounded style', function () {
        $bar = new ProgressBar(0, 0, 100, 10, 50, ProgressBar::STYLE_ROUNDED);
        
        expect($bar->style)->toBe(ProgressBar::STYLE_ROUNDED);
    });
});

