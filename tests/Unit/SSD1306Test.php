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
            expect(fn() => new SSD1306(128, 32, -1, 0x3C))->toThrow(InvalidArgumentException::class);
            expect(fn() => new SSD1306(128, 32, 7, -1))->toThrow(InvalidArgumentException::class);
        });
        
    });
    
    describe('Display Control', function () {
        
        it('initializes display successfully', function () {
            $display = new SSD1306(128, 32, 7, 0x3C, true);
            
            expect($display->begin())->toBeTrue();
            
            $display->end();
        });
        
        it('clears display without errors', function () {
            $display = new SSD1306();
            $display->begin();
            
            expect(fn() => $display->clear())->not->toThrow();
            
            $display->end();
        });
        
        it('updates display without errors', function () {
            $display = new SSD1306();
            $display->begin();
            
            expect(fn() => $display->display())->not->toThrow();
            
            $display->end();
        });
        
        it('ends display session without errors', function () {
            $display = new SSD1306();
            $display->begin();
            
            expect(fn() => $display->end())->not->toThrow();
        });
        
    });
    
    describe('Drawing Functions', function () {
        
        beforeEach(function () {
            $this->display = new SSD1306(128, 32, 7, 0x3C);
            $this->display->begin();
        });
        
        afterEach(function () {
            $this->display->end();
        });
        
        it('draws pixels without errors', function () {
            expect(fn() => $this->display->pixel(10, 10, SSD1306::WHITE))->not->toThrow();
            expect(fn() => $this->display->pixel(0, 0, SSD1306::BLACK))->not->toThrow();
        });
        
        it('handles out-of-bounds pixels gracefully', function () {
            expect(fn() => $this->display->pixel(-1, 10))->not->toThrow();
            expect(fn() => $this->display->pixel(10, -1))->not->toThrow();
            expect(fn() => $this->display->pixel(200, 10))->not->toThrow();
            expect(fn() => $this->display->pixel(10, 200))->not->toThrow();
        });
        
        it('draws lines without errors', function () {
            expect(fn() => $this->display->line(0, 0, 127, 31))->not->toThrow();
            expect(fn() => $this->display->line(10, 10, 50, 20, SSD1306::WHITE))->not->toThrow();
        });
        
        it('draws rectangles without errors', function () {
            expect(fn() => $this->display->rectangle(10, 10, 20, 15))->not->toThrow();
            expect(fn() => $this->display->rectangle(50, 5, 30, 20, SSD1306::WHITE, true))->not->toThrow();
        });
        
        it('draws circles without errors', function () {
            expect(fn() => $this->display->circle(64, 16, 10))->not->toThrow();
            expect(fn() => $this->display->circle(100, 20, 8, SSD1306::WHITE, true))->not->toThrow();
        });
        
        it('renders text without errors', function () {
            expect(fn() => $this->display->text('Hello', 0, 0))->not->toThrow();
            expect(fn() => $this->display->text('World', 0, 10, 2, SSD1306::WHITE))->not->toThrow();
        });
        
        it('handles empty text gracefully', function () {
            expect(fn() => $this->display->text('', 0, 0))->not->toThrow();
        });
        
    });
    
    describe('Display Properties', function () {
        
        it('returns correct width and height', function () {
            $display = new SSD1306(128, 64);
            
            expect($display->getWidth())->toBe(128);
            expect($display->getHeight())->toBe(64);
        });
        
        it('gets pixel values', function () {
            $display = new SSD1306();
            $display->begin();
            
            // Draw a pixel and read it back
            $display->pixel(10, 10, SSD1306::WHITE);
            $display->display();
            
            $pixelValue = $display->getPixel(10, 10);
            expect($pixelValue)->toBeInt();
            
            $display->end();
        });
        
        it('handles out-of-bounds pixel reads gracefully', function () {
            $display = new SSD1306();
            
            expect($display->getPixel(-1, 10))->toBe(0);
            expect($display->getPixel(10, -1))->toBe(0);
            expect($display->getPixel(200, 10))->toBe(0);
            expect($display->getPixel(10, 200))->toBe(0);
        });
        
    });
    
    describe('Advanced Features', function () {
        
        beforeEach(function () {
            $this->display = new SSD1306();
            $this->display->begin();
        });
        
        afterEach(function () {
            $this->display->end();
        });
        
        it('inverts display without errors', function () {
            expect(fn() => $this->display->invertDisplay(true))->not->toThrow();
            expect(fn() => $this->display->invertDisplay(false))->not->toThrow();
        });
        
        it('dims display without errors', function () {
            expect(fn() => $this->display->dim(true))->not->toThrow();
            expect(fn() => $this->display->dim(false))->not->toThrow();
        });
        
        it('sets contrast within valid range', function () {
            expect(fn() => $this->display->setContrast(0))->not->toThrow();
            expect(fn() => $this->display->setContrast(128))->not->toThrow();
            expect(fn() => $this->display->setContrast(255))->not->toThrow();
        });
        
        it('validates contrast range', function () {
            expect(fn() => $this->display->setContrast(-1))->toThrow(InvalidArgumentException::class);
            expect(fn() => $this->display->setContrast(256))->toThrow(InvalidArgumentException::class);
        });
        
    });
    
    describe('Scrolling Functions', function () {
        
        beforeEach(function () {
            $this->display = new SSD1306();
            $this->display->begin();
        });
        
        afterEach(function () {
            $this->display->stopScroll();
            $this->display->end();
        });
        
        it('starts horizontal scrolling without errors', function () {
            expect(fn() => $this->display->startScrollRight(0, 3))->not->toThrow();
            expect(fn() => $this->display->startScrollLeft(0, 3))->not->toThrow();
        });
        
        it('starts diagonal scrolling without errors', function () {
            expect(fn() => $this->display->startScrollDiagRight(0, 3))->not->toThrow();
            expect(fn() => $this->display->startScrollDiagLeft(0, 3))->not->toThrow();
        });
        
        it('stops scrolling without errors', function () {
            $this->display->startScrollRight(0, 3);
            expect(fn() => $this->display->stopScroll())->not->toThrow();
        });
        
        it('validates scroll page numbers', function () {
            expect(fn() => $this->display->startScrollRight(-1, 3))->toThrow(InvalidArgumentException::class);
            expect(fn() => $this->display->startScrollRight(0, 8))->toThrow(InvalidArgumentException::class);
            expect(fn() => $this->display->startScrollLeft(8, 3))->toThrow(InvalidArgumentException::class);
        });
        
    });
    
    describe('Integration Tests', function () {
        
        it('completes full display cycle', function () {
            $display = new SSD1306(128, 32, 7, 0x3C, true);
            
            // Initialize
            expect($display->begin())->toBeTrue();
            
            // Clear and draw
            $display->clear();
            $display->text('Test', 0, 0);
            $display->pixel(10, 10, SSD1306::WHITE);
            $display->line(0, 15, 50, 15);
            $display->rectangle(60, 10, 20, 10);
            $display->circle(100, 15, 8);
            
            // Update display
            $display->display();
            
            // Test effects
            $display->setContrast(128);
            $display->invertDisplay(true);
            $display->invertDisplay(false);
            
            // Cleanup
            $display->end();
            
            expect(true)->toBeTrue(); // If we get here, the cycle completed
        });
        
        it('handles rapid updates', function () {
            $display = new SSD1306();
            $display->begin();
            
            for ($i = 0; $i < 10; $i++) {
                $display->clear();
                $display->text("Frame $i", 0, 0);
                $display->display();
            }
            
            $display->end();
            
            expect(true)->toBeTrue();
        });
        
        it('recovers from errors gracefully', function () {
            $display = new SSD1306();
            
            // These should not crash even without initialization
            $display->clear();
            $display->display();
            $display->text('Test', 0, 0);
            
            // Now initialize properly
            expect($display->begin())->toBeTrue();
            
            $display->clear();
            $display->display();
            $display->end();
        });
        
    });
    
});
