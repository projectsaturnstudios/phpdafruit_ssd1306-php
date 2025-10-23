<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Shapes\Icon;

beforeAll(function () {
    // Initialize built-in icons once for all tests
    Icon::initializeBuiltIns();
});

describe('Icon Registration', function () {
    it('registers new icon', function () {
        Icon::register('test_icon', 8, 8, array_fill(0, 8, 0b11111111));
        
        expect(Icon::has('test_icon'))->toBeTrue();
    });

    it('retrieves registered icon', function () {
        Icon::register('test_icon2', 8, 8, array_fill(0, 8, 0b10101010));
        $icon = Icon::get('test_icon2');
        
        expect($icon)->toBeInstanceOf(Icon::class)
            ->and($icon->name)->toBe('test_icon2')
            ->and($icon->width)->toBe(8)
            ->and($icon->height)->toBe(8);
    });

    it('returns null for non-existent icon', function () {
        $icon = Icon::get('non_existent_icon_xyz');
        
        expect($icon)->toBeNull();
    });

    it('checks if icon exists', function () {
        Icon::register('check_exists', 8, 8, []);
        
        expect(Icon::has('check_exists'))->toBeTrue()
            ->and(Icon::has('does_not_exist'))->toBeFalse();
    });
});

describe('Icon Built-ins', function () {
    it('initializes built-in icons', function () {
        $names = Icon::getNames();
        
        expect($names)->toBeArray()
            ->and($names)->toContain('checkmark')
            ->and($names)->toContain('cross')
            ->and($names)->toContain('warning')
            ->and($names)->toContain('info');
    });

    it('checkmark icon exists', function () {
        expect(Icon::has('checkmark'))->toBeTrue();
    });

    it('cross icon exists', function () {
        expect(Icon::has('cross'))->toBeTrue();
    });

    it('warning icon exists', function () {
        expect(Icon::has('warning'))->toBeTrue();
    });

    it('info icon exists', function () {
        expect(Icon::has('info'))->toBeTrue();
    });

    it('arrow icons exist', function () {
        expect(Icon::has('arrow_up'))->toBeTrue()
            ->and(Icon::has('arrow_down'))->toBeTrue();
    });

    it('wifi icon exists', function () {
        expect(Icon::has('wifi'))->toBeTrue();
    });

    it('battery icon exists', function () {
        expect(Icon::has('battery'))->toBeTrue();
    });
});

describe('Icon Properties', function () {
    it('gets bitmap data', function () {
        $icon = Icon::get('checkmark');
        $bitmap = $icon->getBitmap();
        
        expect($bitmap)->toBeArray()
            ->and($bitmap)->toHaveCount(8);
    });

    it('gets size', function () {
        $icon = Icon::get('checkmark');
        $size = $icon->getSize();
        
        expect($size)->toHaveKey('width')
            ->and($size)->toHaveKey('height')
            ->and($size['width'])->toBe(8)
            ->and($size['height'])->toBe(8);
    });

    it('stores correct dimensions', function () {
        Icon::register('custom_size', 16, 12, array_fill(0, 12, 0));
        $icon = Icon::get('custom_size');
        
        expect($icon->width)->toBe(16)
            ->and($icon->height)->toBe(12);
    });
});

describe('Icon Management', function () {
    it('lists all registered icon names', function () {
        Icon::register('list_test_1', 8, 8, []);
        Icon::register('list_test_2', 8, 8, []);
        
        $names = Icon::getNames();
        
        expect($names)->toContain('list_test_1')
            ->and($names)->toContain('list_test_2');
    });

    it('overwrites icon with same name', function () {
        Icon::register('overwrite_test', 8, 8, [0b11111111]);
        Icon::register('overwrite_test', 8, 8, [0b00000000]);
        
        $icon = Icon::get('overwrite_test');
        expect($icon->getBitmap()[0])->toBe(0);
    });
});

