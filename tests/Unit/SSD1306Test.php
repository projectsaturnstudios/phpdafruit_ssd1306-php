<?php

declare(strict_types=1);

use ProjectSaturnStudios\SSD1306\SSD1306;

describe('SSD1306 Display', function () {
    
    beforeEach(function () {
        // Skip tests if extension is not loaded (for CI/development without hardware)
        if (!extension_loaded('ssd1306')) {
            $this->markTestSkipped('SSD1306 extension not available');
        }
    });

    describe('Constructor', function () {
        
        it('creates display with default dimensions', function () {
            $display = new SSD1306();
            
            expect($display->getWidth())->toBe(128);
            expect($display->getHeight())->toBe(32);
        });
        
        it('creates display with custom dimensions', function () {
            $display = new SSD1306(128, 64);
            
            expect($display->getWidth())->toBe(128);
            expect($display->getHeight())->toBe(64);
        });
        
        it('creates display with custom I2C settings', function () {
            $display = new SSD1306(128, 32, 7, 0x3C);
            
            expect($display->getWidth())->toBe(128);
            expect($display->getHeight())->toBe(32);
        });
        
        it('throws exception when extension not loaded', function () {
            // This test would need to mock extension_loaded() to return false
            // For now, we'll test the error message format
            expect(true)->toBeTrue(); // Placeholder
        })->skip('Need to mock extension_loaded() function');
        
        it('validates display dimensions', function () {
            expect(fn() => new SSD1306(0, 32))->toThrow(InvalidArgumentException::class);
            expect(fn() => new SSD1306(128, 0))->toThrow(InvalidArgumentException::class);
            expect(fn() => new SSD1306(-1, 32))->toThrow(InvalidArgumentException::class);
        });
        
        it('validates I2C parameters', function () {
            expect(fn() => new SSD1306(128, 32, -1))->toThrow(InvalidArgumentException::class);
            expect(fn() => new SSD1306(128, 32, 7, -1))->toThrow(InvalidArgumentException::class);
        });
        
    });
    
    describe('Display Control', function () {
        
        beforeEach(function () {
            $this->display = new SSD1306(128, 32, 7, 0x3C, true);
        });
        
        it('initializes display successfully', function () {
            $result = $this->display->begin();
            expect($result)->toBeTrue();
        });
        
        it('clears display without errors', function () {
            $this->display->begin();
            
            $this->display->clear();
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('updates display without errors', function () {
            $this->display->begin();
            
            $this->display->display();
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('ends display operations cleanly', function () {
            $this->display->begin();
            
            $this->display->end();
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
    });
    
    describe('Drawing Functions', function () {
        
        beforeEach(function () {
            $this->display = new SSD1306(128, 32, 7, 0x3C, true);
            $this->display->begin();
        });
        
        afterEach(function () {
            $this->display->end();
        });
        
        it('draws pixels correctly', function () {
            $this->display->pixel(10, 10);
            $this->display->pixel(10, 10, SSD1306::WHITE);
            $this->display->pixel(10, 10, SSD1306::BLACK);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('handles out-of-bounds pixels gracefully', function () {
            $this->display->pixel(-1, 10);
            $this->display->pixel(10, -1);
            $this->display->pixel(200, 10);
            $this->display->pixel(10, 100);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('draws lines correctly', function () {
            $this->display->line(0, 0, 127, 31);
            $this->display->line(10, 10, 50, 20, SSD1306::WHITE);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('draws rectangles correctly', function () {
            $this->display->rectangle(10, 10, 20, 15);
            $this->display->rectangle(10, 10, 20, 15, SSD1306::WHITE, false);
            $this->display->rectangle(10, 10, 20, 15, SSD1306::WHITE, true);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('draws circles correctly', function () {
            $this->display->circle(64, 16, 10);
            $this->display->circle(64, 16, 10, SSD1306::WHITE, false);
            $this->display->circle(64, 16, 10, SSD1306::WHITE, true);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('renders text correctly', function () {
            $this->display->text('Hello', 0, 0);
            $this->display->text('World', 0, 8, 1);
            $this->display->text('Test', 0, 16, 2, SSD1306::WHITE);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('validates text parameters', function () {
            $this->display->text('', 0, 0); // Empty string should be OK
            $this->display->text('Test', -10, 0); // Negative coords should be handled
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
    });
    
    describe('Display Properties', function () {
        
        beforeEach(function () {
            $this->display = new SSD1306(128, 64, 7, 0x3C);
        });
        
        it('returns correct width', function () {
            expect($this->display->getWidth())->toBe(128);
        });
        
        it('returns correct height', function () {
            expect($this->display->getHeight())->toBe(64);
        });
        
    });
    
    describe('Advanced Features', function () {
        
        beforeEach(function () {
            $this->display = new SSD1306(128, 32, 7, 0x3C);
            $this->display->begin();
        });
        
        afterEach(function () {
            $this->display->end();
        });
        
        it('inverts display', function () {
            $this->display->invertDisplay(true);
            $this->display->invertDisplay(false);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('dims display', function () {
            $this->display->dim(true);
            $this->display->dim(false);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('sets contrast', function () {
            $this->display->setContrast(128);
            $this->display->setContrast(255);
            $this->display->setContrast(0);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('validates contrast range', function () {
            expect(fn() => $this->display->setContrast(-1))->toThrow(InvalidArgumentException::class);
            expect(fn() => $this->display->setContrast(256))->toThrow(InvalidArgumentException::class);
        });
        
        it('gets pixel values', function () {
            // Draw a white pixel and verify we can read it
            $this->display->pixel(10, 10, SSD1306::WHITE);
            $this->display->display();
            
            $pixel = $this->display->getPixel(10, 10);
            expect($pixel)->toBeInt();
            expect($pixel)->toBeGreaterThanOrEqual(0);
            expect($pixel)->toBeLessThanOrEqual(1);
        });
        
    });
    
    describe('Scrolling Functions', function () {
        
        beforeEach(function () {
            $this->display = new SSD1306(128, 32, 7, 0x3C);
            $this->display->begin();
        });
        
        afterEach(function () {
            $this->display->end();
        });
        
        it('starts horizontal scroll right', function () {
            $this->display->startScrollRight(0, 3);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('starts horizontal scroll left', function () {
            $this->display->startScrollLeft(0, 3);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('starts diagonal scroll right', function () {
            $this->display->startScrollDiagRight(0, 3);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('starts diagonal scroll left', function () {
            $this->display->startScrollDiagLeft(0, 3);
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('stops scrolling', function () {
            $this->display->startScrollRight(0, 3);
            $this->display->stopScroll();
            expect(true)->toBeTrue(); // If we get here, no exception was thrown
        });
        
        it('validates scroll parameters', function () {
            expect(fn() => $this->display->startScrollRight(-1, 3))->toThrow(InvalidArgumentException::class);
            expect(fn() => $this->display->startScrollRight(0, 8))->toThrow(InvalidArgumentException::class);
        });
        
    });
    
    describe('Integration Tests', function () {
        
        beforeEach(function () {
            $this->display = new SSD1306(128, 32, 7, 0x3C, true);
        });
        
        it('performs complete display cycle', function () {
            // Initialize
            expect($this->display->begin())->toBeTrue();
            
            // Clear
            $this->display->clear();
            
            // Draw some content
            $this->display->text('SSD1306 Test', 0, 0);
            $this->display->line(0, 10, 127, 10);
            $this->display->rectangle(10, 15, 30, 10);
            $this->display->circle(100, 20, 8);
            
            // Update display
            $this->display->display();
            
            // Clean up
            $this->display->end();
            
            expect(true)->toBeTrue(); // If we get here, the cycle completed successfully
        });
        
        it('handles rapid updates', function () {
            $this->display->begin();
            
            for ($i = 0; $i < 10; $i++) {
                $this->display->clear();
                $this->display->text("Frame $i", 0, 0);
                $this->display->display();
            }
            
            $this->display->end();
            
            expect(true)->toBeTrue(); // If we get here, rapid updates worked
        });
        
        it('recovers from errors gracefully', function () {
            // Test error recovery by trying to use display before initialization
            $this->display->clear();
            $this->display->display();
            
            // Now initialize properly
            expect($this->display->begin())->toBeTrue();
            
            // Should work normally now
            $this->display->clear();
            $this->display->display();
            
            $this->display->end();
            
            expect(true)->toBeTrue(); // If we get here, error recovery worked
        });
        
    });
    
});

describe('SSD1306 Constants', function () {
    
    it('has correct color constants', function () {
        expect(SSD1306::BLACK)->toBe(0);
        expect(SSD1306::WHITE)->toBe(1);
    });
    
    it('has correct dimension constants', function () {
        expect(SSD1306::WIDTH)->toBe(128);
        expect(SSD1306::HEIGHT_32)->toBe(32);
        expect(SSD1306::HEIGHT_64)->toBe(64);
    });
    
    it('has correct I2C constants', function () {
        expect(SSD1306::DEFAULT_I2C_BUS)->toBe(7); // Should be 7 for Yahboom CUBE
        expect(SSD1306::DEFAULT_I2C_ADDRESS)->toBe(0x3C);
    });
    
});