<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Builder\DisplayBuilder;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;
use PhpdaFruit\SSD1306\SSD1306Display;

describe('DisplayBuilder Integration', function () {
    afterEach(function () {
        // Give time to see the output
        if (file_exists('/dev/i2c-7')) {
            usleep(500000); // 0.5s pause
        }
    });

    it('builds and initializes a real display', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayBuilder::create()
            ->size(128, 32)
            ->on('/dev/i2c-7')
            ->build();

        expect($display)->toBeInstanceOf(SSD1306Display::class)
            ->and($display->hasBuffer())->toBeTrue()
            ->and($display->getI2CFileDescriptor())->toBeGreaterThan(0);

        // Test we can write to it
        $display->clearDisplay();
        $display->drawPixel(64, 16, 1);
        $display->display();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('applies text settings after initialization', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayBuilder::create()
            ->size(128, 32)
            ->on('/dev/i2c-7')
            ->textSize(2)
            ->brightness(200)
            ->build();

        // Verify display is configured
        expect($display->hasBuffer())->toBeTrue();

        // Draw text to verify text size was applied
        $display->clearDisplay();
        $display->setCursor(0, 0);
        $display->setTextColor(1);
        foreach (str_split("Hi") as $char) {
            $display->write(ord($char));
        }
        $display->display();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('chains multiple configurations', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayBuilder::create()
            ->size(128, 32)
            ->on('/dev/i2c-7')
            ->brightness(150)
            ->textSize(1)
            ->textWrap(true)
            ->build();

        expect($display->hasBuffer())->toBeTrue();

        // Test display works
        $display->clearDisplay();
        $display->fillRect(0, 0, 10, 10, 1);
        $display->display();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('DisplayFactory Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(500000);
        }
    });

    it('creates standard display', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');

        expect($display)->toBeInstanceOf(SSD1306Display::class)
            ->and($display->getDisplayWidth())->toBe(128)
            ->and($display->getDisplayHeight())->toBe(32)
            ->and($display->hasBuffer())->toBeTrue();

        // Test it works
        $display->clearDisplay();
        $display->drawRect(10, 10, 20, 10, 1);
        $display->display();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('creates dashboard display', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::dashboard('/dev/i2c-7');

        expect($display)->toBeInstanceOf(SSD1306Display::class)
            ->and($display->hasBuffer())->toBeTrue();

        // Draw a simple dashboard
        $display->clearDisplay();
        
        // Title
        $display->setCursor(0, 0);
        $display->setTextColor(1);
        foreach (str_split("Status") as $char) {
            $display->write(ord($char));
        }
        
        // Value
        $display->setCursor(0, 12);
        foreach (str_split("OK") as $char) {
            $display->write(ord($char));
        }
        
        $display->display();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('creates dimmed display', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::dimmed('/dev/i2c-7');

        expect($display)->toBeInstanceOf(SSD1306Display::class);

        $display->clearDisplay();
        $display->fillCircle(64, 16, 10, 1);
        $display->display();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('creates rotated display', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::rotated('/dev/i2c-7');

        expect($display)->toBeInstanceOf(SSD1306Display::class)
            ->and($display->getRotation())->toBe(2);

        $display->clearDisplay();
        $display->drawLine(0, 0, 127, 31, 1);
        $display->display();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('creates custom sized display', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::custom(128, 32, '/dev/i2c-7');

        expect($display)->toBeInstanceOf(SSD1306Display::class)
            ->and($display->getDisplayWidth())->toBe(128)
            ->and($display->getDisplayHeight())->toBe(32);

        $display->clearDisplay();
        $display->drawTriangle(64, 5, 44, 25, 84, 25, 1);
        $display->display();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('DisplayBuilder Complex Scenarios', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(500000);
        }
    });

    it('builds display with all options configured', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayBuilder::create()
            ->size(128, 32)
            ->on('/dev/i2c-7')
            ->clockSpeed(400000)
            ->brightness(180)
            ->textSize(1)
            ->textColor(1, 0)
            ->textWrap(true)
            ->rotation(0)
            ->build();

        expect($display->hasBuffer())->toBeTrue();

        // Complex drawing
        $display->clearDisplay();
        $display->drawRect(0, 0, 128, 32, 1);
        $display->fillRect(2, 2, 124, 28, 0);
        $display->setCursor(10, 12);
        $display->setTextColor(1);
        foreach (str_split("Builder") as $char) {
            $display->write(ord($char));
        }
        $display->display();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

