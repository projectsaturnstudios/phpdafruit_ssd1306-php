<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\SSD1306Display;

describe('SSD1306Display Constructor', function () {
    it('verifies default constructor parameters from shared display', function () {
        $display = getSharedDisplay();
        
        expect($display->getDisplayWidth())->toBe(128)
            ->and($display->getDisplayHeight())->toBe(32)
            ->and($display->getDevicePath())->toBe('/dev/i2c-7')
            ->and($display->getResetPin())->toBe(-1)
            ->and($display->getWireClk())->toBe(400000)
            ->and($display->getRestoreClk())->toBe(100000);
    });

    it('verifies I2C address auto-detection (32px = 0x3C)', function () {
        $display = getSharedDisplay();
        
        // 32px height defaults to 0x3C
        expect($display->getI2CAddress())->toBe(0x3C);
    });

    it('has negative file descriptor before begin', function () {
        // Create a temporary display without calling begin() - using non-existent device
        $display = new SSD1306Display(128, 32, '/dev/i2c-99');
        
        expect($display->getI2CFileDescriptor())->toBeLessThan(0);
    });
});

describe('Display Dimensions', function () {
    beforeEach(function () {
        $this->display = getSharedDisplay();
    });
    
    afterEach(function () {
        if (isDisplayAvailable()) {
            // Ensure rotation is reset
            $this->display->setRotation(0);
            clearTestDisplay();
        }
    });

    it('returns correct display size as array', function () {
        $size = $this->display->getDisplaySize();
        
        expect($size)->toBeArray()
            ->and($size[0])->toBe(128)
            ->and($size[1])->toBe(32);
    });

    it('returns rotation-adjusted dimensions', function () {
        // Reset rotation first
        $this->display->setRotation(0);
        
        // No rotation
        expect($this->display->width())->toBe(128)
            ->and($this->display->height())->toBe(32);
        
        // Rotate 90 degrees
        $this->display->setRotation(1);
        expect($this->display->width())->toBe(32)
            ->and($this->display->height())->toBe(128);
        
        // Rotate 180 degrees
        $this->display->setRotation(2);
        expect($this->display->width())->toBe(128)
            ->and($this->display->height())->toBe(32);
        
        // Reset back to normal
        $this->display->setRotation(0);
    });
});

describe('Display State', function () {
    it('has no buffer before begin', function () {
        // Create temp display without begin - using non-existent device
        $display = new SSD1306Display(128, 32, '/dev/i2c-99');
        
        expect($display->hasBuffer())->toBeFalse()
            ->and($display->getVCCState())->toBe(0);
    });

    it('has buffer after begin (from shared display)', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $display = getSharedDisplay();
        
        expect($display->hasBuffer())->toBeTrue()
            ->and($display->getVCCState())->toBe(2) // SWITCHCAPVCC
            ->and($display->getI2CFileDescriptor())->toBeGreaterThan(0)
            ->and($display->getContrast())->toBeGreaterThan(0);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

