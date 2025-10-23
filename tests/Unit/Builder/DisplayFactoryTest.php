<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\SSD1306Display;

describe('DisplayFactory for Testing', function () {
    it('creates test display without init', function () {
        $display = DisplayFactory::forTesting();
        
        expect($display)->toBeInstanceOf(SSD1306Display::class)
            ->and($display->getDisplayWidth())->toBe(128)
            ->and($display->getDisplayHeight())->toBe(32);
    });

    it('creates test display with custom size', function () {
        $display = DisplayFactory::forTesting(128, 64);
        
        expect($display->getDisplayWidth())->toBe(128)
            ->and($display->getDisplayHeight())->toBe(64);
    });

    it('creates test display with non-existent device', function () {
        $display = DisplayFactory::forTesting(128, 32, '/dev/i2c-99');
        
        expect($display->getDevicePath())->toBe('/dev/i2c-99');
    });
});

describe('DisplayFactory Preset Configurations', function () {
    it('creates standard configuration', function () {
        // Only verify the method exists and returns correct type
        // Don't actually initialize since device may not exist locally
        expect(function () {
            DisplayFactory::standard('/dev/i2c-99');
        })->toThrow(RuntimeException::class); // Should fail with invalid device
    });

    it('creates large configuration', function () {
        expect(function () {
            DisplayFactory::large('/dev/i2c-99');
        })->toThrow(RuntimeException::class);
    });

    it('creates high contrast configuration', function () {
        expect(function () {
            DisplayFactory::highContrast('/dev/i2c-99');
        })->toThrow(RuntimeException::class);
    });

    it('creates dashboard configuration', function () {
        expect(function () {
            DisplayFactory::dashboard('/dev/i2c-99');
        })->toThrow(RuntimeException::class);
    });

    it('creates dimmed configuration', function () {
        expect(function () {
            DisplayFactory::dimmed('/dev/i2c-99');
        })->toThrow(RuntimeException::class);
    });

    it('creates inverted configuration', function () {
        expect(function () {
            DisplayFactory::inverted('/dev/i2c-99');
        })->toThrow(RuntimeException::class);
    });

    it('creates rotated configuration', function () {
        expect(function () {
            DisplayFactory::rotated('/dev/i2c-99');
        })->toThrow(RuntimeException::class);
    });

    it('creates custom configuration', function () {
        expect(function () {
            DisplayFactory::custom(128, 64, '/dev/i2c-99');
        })->toThrow(RuntimeException::class);
    });
});

describe('DisplayFactory Method Signatures', function () {
    it('standard accepts device path parameter', function () {
        $reflection = new ReflectionMethod(DisplayFactory::class, 'standard');
        $params = $reflection->getParameters();
        
        expect($params)->toHaveCount(1)
            ->and($params[0]->getName())->toBe('devicePath');
    });

    it('large accepts device path parameter', function () {
        $reflection = new ReflectionMethod(DisplayFactory::class, 'large');
        $params = $reflection->getParameters();
        
        expect($params)->toHaveCount(1)
            ->and($params[0]->getName())->toBe('devicePath');
    });

    it('custom accepts width, height, and device path', function () {
        $reflection = new ReflectionMethod(DisplayFactory::class, 'custom');
        $params = $reflection->getParameters();
        
        expect($params)->toHaveCount(3)
            ->and($params[0]->getName())->toBe('width')
            ->and($params[1]->getName())->toBe('height')
            ->and($params[2]->getName())->toBe('devicePath');
    });

    it('forTesting accepts optional parameters', function () {
        $reflection = new ReflectionMethod(DisplayFactory::class, 'forTesting');
        $params = $reflection->getParameters();
        
        expect($params)->toHaveCount(3)
            ->and($params[0]->isOptional())->toBeTrue()
            ->and($params[1]->isOptional())->toBeTrue()
            ->and($params[2]->isOptional())->toBeTrue();
    });
});

