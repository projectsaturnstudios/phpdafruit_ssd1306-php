<?php

declare(strict_types=1);

describe('Dashboard Demo Integration', function () {
    beforeEach(function () {
        $this->display = getSharedDisplay();
        clearTestDisplay();
    });
    
    afterEach(function () {
        if (isDisplayAvailable()) {
            pauseToView(1.0); // Pause longer for dashboard demos
            clearTestDisplay();
            // Reset text settings
            $this->display->setTextSize(1, 1);
            $this->display->setFont(null);
        }
    });
    
    it('renders complete dashboard with icons and stats', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $display = $this->display;
        
        $display->clearDisplay();
        
        // Status icons
        $wifi = [0x00, 0x18, 0x24, 0x42, 0x99, 0x24, 0x00, 0x18];
        $battery = [0x7E, 0x81, 0xBD, 0xBD, 0xBD, 0xBD, 0x81, 0x7E];
        $ok = [0x00, 0x01, 0x02, 0x84, 0x48, 0x30, 0x00, 0x00];
        
        // Icon row
        $display->drawBitmap(0, 0, $wifi, 8, 8, 1);
        $display->drawBitmap(12, 0, $battery, 8, 8, 1);
        $display->drawBitmap(24, 0, $ok, 8, 8, 1);
        $display->drawFastHLine(0, 8, 128, 1);
        
        // Content area - stats
        $display->setTextSize(1, 1);
        $display->setTextColor(1, 0);
        
        // Line 1: CPU
        $x = 2;
        foreach (str_split("CPU: 45%") as $char) {
            $display->drawChar($x, 12, ord($char), 1, 0, 1, 1);
            $x += 6;
        }
        
        // Line 2: Memory
        $x = 2;
        foreach (str_split("MEM: 2.1GB") as $char) {
            $display->drawChar($x, 22, ord($char), 1, 0, 1, 1);
            $x += 6;
        }
        
        $display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders bar graph visualization', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $display = $this->display;
        
        $display->clearDisplay();
        
        // Bar graph at y=22
        $display->fillRect(2, 22, 25, 8, 1);   // 100%
        $display->fillRect(30, 24, 20, 6, 1);  // 75%
        $display->fillRect(53, 26, 15, 4, 1);  // 50%
        $display->fillRect(71, 28, 10, 2, 1);  // 25%
        
        $display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders alert message with large text', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $display = $this->display;
        
        $display->clearDisplay();
        
        // Big alert text
        $display->setTextSize(2, 2);
        $x = 5;
        foreach (str_split("ALERT") as $char) {
            $display->drawChar($x, 14, ord($char), 1, 0, 2, 2);
            $x += 12;
        }
        
        $display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('Complex Graphics Composition', function () {
    beforeEach(function () {
        $this->display = getSharedDisplay();
        clearTestDisplay();
    });
    
    afterEach(function () {
        if (isDisplayAvailable()) {
            pauseToView(1.0); // Pause longer for complex graphics
            clearTestDisplay();
            // Reset text settings
            $this->display->setTextSize(1, 1);
            $this->display->setFont(null);
        }
    });
    
    it('combines shapes, text, and bitmaps', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $display = $this->display;
        
        $display->clearDisplay();
        
        // Border
        $display->drawRect(0, 0, 128, 32, 1);
        
        // Shapes
        $display->fillCircle(20, 16, 8, 1);
        $display->fillTriangle(40, 8, 50, 24, 30, 24, 1);
        $display->drawRect(60, 8, 20, 16, 1);
        
        // Text
        $display->setTextSize(1, 1);
        $display->setCursor(90, 12);
        $display->setTextColor(1, 0);
        foreach (str_split("Hi!") as $c) {
            $display->write(ord($c));
        }
        
        $display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders bouncing ball animation frame', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $display = $this->display;
        
        $x = 64;
        $y = 16;
        
        $display->clearDisplay();
        $display->fillCircle($x, $y, 3, 1);
        $display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

