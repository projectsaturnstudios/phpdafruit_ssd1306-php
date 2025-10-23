<?php

declare(strict_types=1);

describe('Low-Level Write Operations', function () {
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

    it('performs start and end write transactions', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        // Test start/end write - should not crash
        $this->display->startWrite();
        $this->display->endWrite();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('writes individual pixels', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        // Low-level write pixel
        $this->display->startWrite();
        $this->display->writePixel(10, 10, 1);
        $this->display->writePixel(20, 10, 1);
        $this->display->endWrite();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('writes fast vertical lines (low-level)', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->startWrite();
        $this->display->writeFastVLine(30, 5, 20, 1);
        $this->display->endWrite();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('writes fast horizontal lines (low-level)', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->startWrite();
        $this->display->writeFastHLine(10, 15, 50, 1);
        $this->display->endWrite();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('writes filled rectangles (low-level)', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->startWrite();
        $this->display->writeFillRect(10, 10, 30, 15, 1);
        $this->display->endWrite();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('writes lines (low-level)', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->startWrite();
        $this->display->writeLine(0, 0, 127, 31, 1);
        $this->display->endWrite();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('Additional Shape Drawing', function () {
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

    it('draws ellipse outline', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->drawEllipse(64, 16, 30, 10, 1);
        $this->display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('fills ellipse', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->fillEllipse(64, 16, 25, 8, 1);
        $this->display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws rounded rectangle outline', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->drawRoundRect(20, 5, 60, 20, 5, 1);
        $this->display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('fills rounded rectangle', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->fillRoundRect(20, 5, 60, 20, 5, 1);
        $this->display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws circle helper (quarters)', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        // Draw four quarters separately
        $this->display->drawCircleHelper(64, 16, 10, 0x01, 1); // Top right
        $this->display->drawCircleHelper(64, 16, 10, 0x02, 1); // Top left
        $this->display->drawCircleHelper(64, 16, 10, 0x04, 1); // Bottom left
        $this->display->drawCircleHelper(64, 16, 10, 0x08, 1); // Bottom right
        $this->display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('fills circle helper (quarters)', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->fillCircleHelper(64, 16, 8, 0x03, 0, 1);
        $this->display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

