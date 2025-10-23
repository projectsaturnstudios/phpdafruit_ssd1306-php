<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Shapes\RoundedBox;

describe('RoundedBox Construction', function () {
    it('creates with default parameters', function () {
        $box = new RoundedBox(0, 0, 100, 30);
        
        expect($box->x)->toBe(0)
            ->and($box->y)->toBe(0)
            ->and($box->width)->toBe(100)
            ->and($box->height)->toBe(30)
            ->and($box->radius)->toBe(4)
            ->and($box->filled)->toBeFalse()
            ->and($box->border)->toBeTrue();
    });

    it('creates with custom parameters', function () {
        $box = new RoundedBox(10, 20, 80, 40, 6, true, false, 4);
        
        expect($box->x)->toBe(10)
            ->and($box->radius)->toBe(6)
            ->and($box->filled)->toBeTrue()
            ->and($box->border)->toBeFalse()
            ->and($box->padding)->toBe(4);
    });

    it('limits radius to half smaller dimension', function () {
        $box = new RoundedBox(0, 0, 100, 20, 20); // Radius too large
        
        expect($box->radius)->toBe(10); // Half of 20 (smaller dimension)
    });
});

describe('RoundedBox Bounds', function () {
    it('returns correct bounds', function () {
        $box = new RoundedBox(10, 20, 100, 30);
        $bounds = $box->getBounds();
        
        expect($bounds['x'])->toBe(10)
            ->and($bounds['y'])->toBe(20)
            ->and($bounds['width'])->toBe(100)
            ->and($bounds['height'])->toBe(30);
    });

    it('returns correct inner bounds', function () {
        $box = new RoundedBox(10, 20, 100, 30, padding: 4);
        $inner = $box->getInnerBounds();
        
        expect($inner['x'])->toBe(14) // 10 + 4
            ->and($inner['y'])->toBe(24) // 20 + 4
            ->and($inner['width'])->toBe(92) // 100 - 8
            ->and($inner['height'])->toBe(22); // 30 - 8
    });
});

describe('RoundedBox Corner Centers', function () {
    it('calculates corner centers correctly', function () {
        $box = new RoundedBox(10, 10, 100, 40, 5);
        $corners = $box->getCornerCenters();
        
        expect($corners)->toHaveKey('top_left')
            ->and($corners)->toHaveKey('top_right')
            ->and($corners)->toHaveKey('bottom_left')
            ->and($corners)->toHaveKey('bottom_right');
    });

    it('top left corner is at correct position', function () {
        $box = new RoundedBox(10, 20, 100, 40, 5);
        $corners = $box->getCornerCenters();
        
        expect($corners['top_left']['x'])->toBe(15) // 10 + 5
            ->and($corners['top_left']['y'])->toBe(25); // 20 + 5
    });

    it('top right corner is at correct position', function () {
        $box = new RoundedBox(10, 20, 100, 40, 5);
        $corners = $box->getCornerCenters();
        
        expect($corners['top_right']['x'])->toBe(104) // 10 + 100 - 5 - 1
            ->and($corners['top_right']['y'])->toBe(25);
    });

    it('bottom left corner is at correct position', function () {
        $box = new RoundedBox(10, 20, 100, 40, 5);
        $corners = $box->getCornerCenters();
        
        expect($corners['bottom_left']['x'])->toBe(15)
            ->and($corners['bottom_left']['y'])->toBe(54); // 20 + 40 - 5 - 1
    });

    it('bottom right corner is at correct position', function () {
        $box = new RoundedBox(10, 20, 100, 40, 5);
        $corners = $box->getCornerCenters();
        
        expect($corners['bottom_right']['x'])->toBe(104)
            ->and($corners['bottom_right']['y'])->toBe(54);
    });
});

describe('RoundedBox Presets', function () {
    it('creates panel preset', function () {
        $panel = RoundedBox::panel(10, 10, 100, 40);
        
        expect($panel->radius)->toBe(4)
            ->and($panel->filled)->toBeFalse()
            ->and($panel->border)->toBeTrue()
            ->and($panel->padding)->toBe(4);
    });

    it('creates button preset', function () {
        $button = RoundedBox::button(10, 10, 60, 20);
        
        expect($button->radius)->toBe(3)
            ->and($button->filled)->toBeTrue()
            ->and($button->border)->toBeTrue()
            ->and($button->padding)->toBe(2);
    });
});

