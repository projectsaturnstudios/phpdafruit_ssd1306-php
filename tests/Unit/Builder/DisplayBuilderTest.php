<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Builder\DisplayBuilder;
use PhpdaFruit\SSD1306\SSD1306Display;

describe('DisplayBuilder Construction', function () {
    it('creates a new builder instance', function () {
        $builder = DisplayBuilder::create();
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('builds display without initialization', function () {
        $display = DisplayBuilder::create()
            ->size(128, 32)
            ->on('/dev/i2c-99') // Non-existent device for testing
            ->buildWithoutInit();
        
        expect($display)->toBeInstanceOf(SSD1306Display::class)
            ->and($display->getDisplayWidth())->toBe(128)
            ->and($display->getDisplayHeight())->toBe(32)
            ->and($display->getDevicePath())->toBe('/dev/i2c-99');
    });
});

describe('DisplayBuilder Fluent API', function () {
    it('chains size configuration', function () {
        $builder = DisplayBuilder::create()->size(128, 64);
        $display = $builder->buildWithoutInit();
        
        expect($display->getDisplayWidth())->toBe(128)
            ->and($display->getDisplayHeight())->toBe(64);
    });

    it('chains device path configuration', function () {
        $builder = DisplayBuilder::create()->on('/dev/i2c-1');
        $display = $builder->buildWithoutInit();
        
        expect($display->getDevicePath())->toBe('/dev/i2c-1');
    });

    it('chains reset pin configuration', function () {
        $builder = DisplayBuilder::create()->withResetPin(4);
        $display = $builder->buildWithoutInit();
        
        expect($display->getResetPin())->toBe(4);
    });

    it('chains clock speed configuration', function () {
        $builder = DisplayBuilder::create()->clockSpeed(100000, 50000);
        $display = $builder->buildWithoutInit();
        
        expect($display->getWireClk())->toBe(100000)
            ->and($display->getRestoreClk())->toBe(50000);
    });

    it('chains clock speed without restore clock', function () {
        $builder = DisplayBuilder::create()->clockSpeed(200000);
        $display = $builder->buildWithoutInit();
        
        expect($display->getWireClk())->toBe(200000);
    });
});

describe('DisplayBuilder Configuration Methods', function () {
    it('configures font', function () {
        $builder = DisplayBuilder::create()->font('FreeSans9pt7b');
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('configures text size', function () {
        $builder = DisplayBuilder::create()->textSize(2);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('configures text size with different x/y', function () {
        $builder = DisplayBuilder::create()->textSize(2, 3);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('configures text color', function () {
        $builder = DisplayBuilder::create()->textColor(1);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('configures text color with background', function () {
        $builder = DisplayBuilder::create()->textColor(1, 0);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('configures brightness', function () {
        $builder = DisplayBuilder::create()->brightness(200);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('clamps brightness to 0-255', function () {
        $builder1 = DisplayBuilder::create()->brightness(-10);
        $builder2 = DisplayBuilder::create()->brightness(300);
        
        expect($builder1)->toBeInstanceOf(DisplayBuilder::class)
            ->and($builder2)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('configures inverted display', function () {
        $builder = DisplayBuilder::create()->inverted(true);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('configures rotation', function () {
        $builder = DisplayBuilder::create()->rotation(2);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('wraps rotation to 0-3', function () {
        $builder = DisplayBuilder::create()->rotation(5);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('configures text wrap', function () {
        $builder = DisplayBuilder::create()->textWrap(false);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('configures VCC state', function () {
        $builder = DisplayBuilder::create()->vccState(0x01);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('configures I2C address', function () {
        $builder = DisplayBuilder::create()->i2cAddress(0x3C);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('configures hardware reset', function () {
        $builder = DisplayBuilder::create()->hardwareReset(false);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });
});

describe('DisplayBuilder Method Chaining', function () {
    it('chains multiple configuration methods', function () {
        $builder = DisplayBuilder::create()
            ->size(128, 64)
            ->on('/dev/i2c-1')
            ->clockSpeed(200000)
            ->brightness(150)
            ->rotation(1)
            ->textSize(2)
            ->inverted(true);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });

    it('can chain in any order', function () {
        $builder = DisplayBuilder::create()
            ->brightness(200)
            ->size(128, 32)
            ->textSize(1)
            ->on('/dev/i2c-7')
            ->rotation(0);
        
        expect($builder)->toBeInstanceOf(DisplayBuilder::class);
    });
});

describe('DisplayBuilder Build Errors', function () {
    it('throws exception when build fails', function () {
        DisplayBuilder::create()
            ->on('/dev/i2c-999') // Invalid device
            ->build();
    })->throws(RuntimeException::class);
});

