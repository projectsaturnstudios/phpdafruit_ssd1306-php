<?php

declare(strict_types=1);

describe('Drawing Primitives', function () {
    beforeEach(function () {
        $this->display = getSharedDisplay();
        clearTestDisplay();
    });
    
    afterEach(function () {
        if (isDisplayAvailable()) {
            pauseToView(0.5); // Pause to view the output
            clearTestDisplay();
        }
    });

    it('draws pixels to buffer', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->clearDisplay();
        
        // Draw corner pixels
        $this->display->drawPixel(0, 0, 1);      // Top-left
        $this->display->drawPixel(127, 0, 1);    // Top-right
        $this->display->drawPixel(0, 31, 1);     // Bottom-left
        $this->display->drawPixel(127, 31, 1);   // Bottom-right
        $this->display->drawPixel(64, 16, 1);    // Center
        
        expect($this->display->getPixel(0, 0))->toBeTrue()
            ->and($this->display->getPixel(127, 0))->toBeTrue()
            ->and($this->display->getPixel(64, 16))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('handles pixel color modes', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->clearDisplay();
        
        // White pixel
        $this->display->drawPixel(10, 10, 1);
        expect($this->display->getPixel(10, 10))->toBeTrue();
        
        // Black pixel (clear)
        $this->display->drawPixel(10, 10, 0);
        expect($this->display->getPixel(10, 10))->toBeFalse();
        
        // Inverse (toggle)
        $this->display->drawPixel(10, 10, 2);
        expect($this->display->getPixel(10, 10))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws horizontal lines', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->clearDisplay();
        $this->display->drawFastHLine(10, 15, 50, 1);
        
        // Check some pixels in the line
        expect($this->display->getPixel(10, 15))->toBeTrue()
            ->and($this->display->getPixel(30, 15))->toBeTrue()
            ->and($this->display->getPixel(59, 15))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws vertical lines', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->clearDisplay();
        $this->display->drawFastVLine(20, 5, 20, 1);
        
        // Check some pixels in the line
        expect($this->display->getPixel(20, 5))->toBeTrue()
            ->and($this->display->getPixel(20, 15))->toBeTrue()
            ->and($this->display->getPixel(20, 24))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws rectangles', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->clearDisplay();
        $this->display->drawRect(10, 10, 30, 15, 1);
        
        // Check corners
        expect($this->display->getPixel(10, 10))->toBeTrue()
            ->and($this->display->getPixel(39, 10))->toBeTrue()
            ->and($this->display->getPixel(10, 24))->toBeTrue()
            ->and($this->display->getPixel(39, 24))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('fills rectangles', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->clearDisplay();
        $this->display->fillRect(10, 10, 20, 10, 1);
        
        // Check interior pixels
        expect($this->display->getPixel(15, 15))->toBeTrue()
            ->and($this->display->getPixel(20, 15))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws circles', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->clearDisplay();
        
        // Draw circle - test completes if no exception thrown
        $this->display->drawCircle(64, 16, 10, 1);
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('fills circles', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->clearDisplay();
        $this->display->fillCircle(64, 16, 8, 1);
        
        // Check center pixel
        expect($this->display->getPixel(64, 16))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws triangles', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->clearDisplay();
        $this->display->drawTriangle(64, 5, 50, 25, 78, 25, 1);
        
        // Check vertices
        expect($this->display->getPixel(64, 5))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('handles out of bounds drawing gracefully', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->clearDisplay();
        
        // Should not crash
        $this->display->drawPixel(-1, 0, 1);
        $this->display->drawPixel(128, 0, 1);
        $this->display->drawPixel(0, -1, 1);
        $this->display->drawPixel(0, 32, 1);
        
        expect(true)->toBeTrue(); // If we get here, it didn't crash
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('Fill Operations', function () {
    beforeEach(function () {
        $this->display = getSharedDisplay();
        clearTestDisplay();
    });
    
    afterEach(function () {
        if (isDisplayAvailable()) {
            pauseToView(0.5); // Pause to view the output
            clearTestDisplay();
        }
    });
    
    it('fills entire screen', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $display = $this->display;
        
        $display->fillScreen(1);
        
        // Check random pixels
        expect($display->getPixel(10, 10))->toBeTrue()
            ->and($display->getPixel(100, 20))->toBeTrue()
            ->and($display->getPixel(64, 16))->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('clears display buffer', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $display = $this->display;
        
        // Fill screen
        $display->fillScreen(1);
        expect($display->getPixel(64, 16))->toBeTrue();
        
        // Clear it
        $display->clearDisplay();
        expect($display->getPixel(64, 16))->toBeFalse();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

