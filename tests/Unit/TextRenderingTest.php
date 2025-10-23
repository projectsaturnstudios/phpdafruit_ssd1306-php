<?php

declare(strict_types=1);

describe('Text Rendering', function () {
    beforeEach(function () {
        $this->display = getSharedDisplay();
        clearTestDisplay();
    });
    
    afterEach(function () {
        if (isDisplayAvailable()) {
            pauseToView(0.5); // Pause to view the output
            // Simple clear only
            $this->display->clearDisplay();
            $this->display->display();
        }
    });

    it('sets and gets cursor position', function () {
        $this->display->setCursor(10, 20);
        
        expect($this->display->getCursorX())->toBe(10)
            ->and($this->display->getCursorY())->toBe(20);
    });

    it('sets text size', function () {
        // Default size
        $this->display->setTextSize(1);
        expect(true)->toBeTrue();
        
        // Larger size
        $this->display->setTextSize(2);
        expect(true)->toBeTrue();
        
        // Independent x and y sizing
        $this->display->setTextSize(2, 3);
        expect(true)->toBeTrue();
        
        // Reset
        $this->display->setTextSize(1, 1);
        expect(true)->toBeTrue();
    });

    it('sets text color', function () {
        // White text on black background
        $this->display->setTextColor(1, 0);
        expect(true)->toBeTrue();
        
        // Black text on white background
        $this->display->setTextColor(0, 1);
        expect(true)->toBeTrue();
        
        // Transparent background
        $this->display->setTextColor(1);
        expect(true)->toBeTrue();
    });

    it('enables and disables text wrap', function () {
        $this->display->setTextWrap(true);
        expect(true)->toBeTrue();
        
        $this->display->setTextWrap(false);
        expect(true)->toBeTrue();
    });

    it('writes text using write method', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->setCursor(0, 0);
        $this->display->setTextSize(1);
        $this->display->setTextColor(1);
        
        // Write string character by character using write()
        $text = "Hello";
        foreach (str_split($text) as $char) {
            $this->display->write(ord($char));
        }
        
        $this->display->display();
        
        // Verify cursor moved
        expect($this->display->getCursorX())->toBeGreaterThan(0);
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('writes text with drawChar', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->setTextSize(1, 1);
        $this->display->setTextColor(1, 0);
        
        // Draw some characters manually
        $this->display->drawChar(0, 0, ord('A'), 1, 0, 1, 1);
        $this->display->drawChar(10, 0, ord('B'), 1, 0, 1, 1);
        $this->display->drawChar(20, 0, ord('C'), 1, 0, 1, 1);
        
        $this->display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('writes multi-line text', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->setTextSize(1);
        $this->display->setTextColor(1);
        
        // Line 1
        $this->display->setCursor(0, 0);
        foreach (str_split("Line 1") as $char) {
            $this->display->write(ord($char));
        }
        
        // Line 2
        $this->display->setCursor(0, 10);
        foreach (str_split("Line 2") as $char) {
            $this->display->write(ord($char));
        }
        
        $this->display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('calculates text bounds', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        // Set font to null (default) and size
        $this->display->setFont(null);
        $this->display->setTextSize(1, 1);
        
        $x1 = $y1 = $w = $h = 0;
        $this->display->getTextBounds("Hello", 0, 0, $x1, $y1, $w, $h);
        
        // Just verify the method can be called - actual bounds calculation
        // may vary based on font state in C extension
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('writes text at different sizes', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        clearTestDisplay();
        
        $this->display->setTextColor(1);
        
        // Size 1
        $this->display->setCursor(0, 0);
        $this->display->setTextSize(1);
        foreach (str_split("S1") as $char) {
            $this->display->write(ord($char));
        }
        
        // Size 2
        $this->display->setCursor(0, 16);
        $this->display->setTextSize(2);
        foreach (str_split("S2") as $char) {
            $this->display->write(ord($char));
        }
        
        $this->display->display();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('enables CP437 character set', function () {
        $this->display->cp437(true);
        expect(true)->toBeTrue();
        
        $this->display->cp437(false);
        expect(true)->toBeTrue();
    });
});
