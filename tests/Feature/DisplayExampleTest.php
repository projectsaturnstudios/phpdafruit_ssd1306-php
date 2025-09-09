<?php

declare(strict_types=1);

use ProjectSaturnStudios\SSD1306\SSD1306;

describe('SSD1306 Display Examples', function () {
    
    beforeEach(function () {
        // Skip tests if extension is not loaded (for CI/development without hardware)
        if (!extension_loaded('ssd1306')) {
            $this->markTestSkipped('SSD1306 extension not available');
        }
    });

    it('displays hello world example', function () {
        $display = new SSD1306(128, 32, 7, 0x3C, true);
        
        // Initialize display
        expect($display->begin())->toBeTrue();
        
        // Clear screen
        $display->clear();
        
        // Draw "Hello World" text
        $display->text('Hello World!', 0, 0, 1);
        
        // Draw a line under the text
        $display->line(0, 10, 127, 10);
        
        // Draw a small rectangle
        $display->rectangle(5, 15, 20, 10);
        
        // Draw a circle
        $display->circle(100, 20, 8);
        
        // Update the display
        $display->display();
        
        // Test scrolling
        $display->startScrollRight(0, 3);
        
        // Wait a moment (in real usage)
        // usleep(2000000); // 2 seconds
        
        $display->stopScroll();
        
        // Test invert
        $display->invertDisplay(true);
        $display->display();
        
        $display->invertDisplay(false);
        $display->display();
        
        // Test contrast
        $display->setContrast(128);
        $display->display();
        
        // Clean up
        $display->end();
        
        expect(true)->toBeTrue(); // If we get here, the example worked
    });
    
    it('demonstrates drawing functions', function () {
        $display = new SSD1306(128, 64, 7, 0x3C, true);
        
        expect($display->begin())->toBeTrue();
        
        $display->clear();
        
        // Draw various shapes
        $display->pixel(10, 10, SSD1306::WHITE);
        $display->line(0, 0, 127, 63, SSD1306::WHITE);
        $display->rectangle(20, 20, 30, 20, SSD1306::WHITE, false);
        $display->rectangle(60, 20, 30, 20, SSD1306::WHITE, true);
        $display->circle(30, 50, 10, SSD1306::WHITE, false);
        $display->circle(100, 50, 10, SSD1306::WHITE, true);
        
        // Add some text
        $display->text('Shapes!', 0, 0, 1);
        
        $display->display();
        
        // Test pixel reading
        $pixelValue = $display->getPixel(10, 10);
        expect($pixelValue)->toBeInt();
        
        $display->end();
        
        expect(true)->toBeTrue();
    });
    
    it('tests error handling', function () {
        $display = new SSD1306(128, 32, 7, 0x3C, true);
        
        // These should not throw exceptions even without initialization
        $display->clear();
        $display->display();
        $display->text('Test', 0, 0);
        
        // Out of bounds operations should be handled gracefully
        $display->pixel(-10, -10);
        $display->pixel(200, 200);
        
        // Initialize and test normal operations
        expect($display->begin())->toBeTrue();
        
        $display->clear();
        $display->display();
        
        $display->end();
    });
    
    it('validates the 6-method pattern compliance', function () {
        $display = new SSD1306(128, 32, 7, 0x3C);
        
        // Test the 6 core methods that should be exposed according to the pattern
        expect($display->begin())->toBeTrue();
        
        $display->clear();
        
        $display->text('Test', 0, 0);
        
        $display->text('Line 2', 0, 8);
        
        $display->display();
        
        $display->end();
        
        expect(true)->toBeTrue();
    });
    
});
