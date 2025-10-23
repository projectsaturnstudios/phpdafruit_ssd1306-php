<?php

declare(strict_types=1);

describe('Bitmap Drawing', function () {
    beforeEach(function () {
        $this->display = getSharedDisplay();
        clearTestDisplay();
        
        // 8x8 smiley face bitmap
        $this->smiley = [
            0b00111100, // Row 0:   ****
            0b01000010, // Row 1:  *    *
            0b10100101, // Row 2: * *  * *
            0b10000001, // Row 3: *      *
            0b10100101, // Row 4: * *  * *
            0b10011001, // Row 5: *  **  *
            0b01000010, // Row 6:  *    *
            0b00111100, // Row 7:   ****
        ];
        
        // 8x8 WiFi icon
        $this->wifi = [
            0b00000000,
            0b00011000,
            0b00100100,
            0b01000010,
            0b10011001,
            0b00100100,
            0b00000000,
            0b00011000,
        ];
    });
    
    afterEach(function () {
        if (isDisplayAvailable()) {
            pauseToView(0.5); // Pause to view the output
            clearTestDisplay();
        }
    });

    it('draws bitmap with transparent background', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->drawBitmap(0, 0, $this->smiley, 8, 8, 1);
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws bitmap with opaque background', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->drawBitmap(16, 0, $this->smiley, 8, 8, 1, 0);
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws inverted bitmap', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $this->display->drawBitmap(32, 0, $this->smiley, 8, 8, 0, 1);
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws multiple bitmaps in icon row', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        // Icon row like in dashboard demo
        $this->display->drawBitmap(0, 0, $this->wifi, 8, 8, 1);
        $this->display->drawBitmap(12, 0, $this->smiley, 8, 8, 1);
        $this->display->drawBitmap(24, 0, $this->wifi, 8, 8, 1);
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws XBitmap (LSB first format)', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        // XBitmap uses LSB first, different from standard bitmap
        $xbm = [0x3C, 0x42, 0xA5, 0x81, 0xA5, 0x99, 0x42, 0x3C];
        
        $this->display->drawXBitmap(50, 10, $xbm, 8, 8, 1);
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('draws grayscale bitmap', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        // Grayscale bitmap with intensity values
        $grayscale = array_fill(0, 64, 128); // 8x8 gray square
        
        $this->display->drawGrayscaleBitmap(70, 10, $grayscale, 8, 8);
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('Complex Bitmap Scenarios', function () {
    it('creates dashboard icon row', function () {
        $display = getSharedDisplay();
        
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $wifi = [0x00, 0x18, 0x24, 0x42, 0x99, 0x24, 0x00, 0x18];
        $battery = [0x7E, 0x81, 0xBD, 0xBD, 0xBD, 0xBD, 0x81, 0x7E];
        $ok = [0x00, 0x01, 0x02, 0x84, 0x48, 0x30, 0x00, 0x00];
        
        $display->drawBitmap(0, 0, $wifi, 8, 8, 1);
        $display->drawBitmap(12, 0, $battery, 8, 8, 1);
        $display->drawBitmap(24, 0, $ok, 8, 8, 1);
        $display->drawFastHLine(0, 8, 128, 1);
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});
