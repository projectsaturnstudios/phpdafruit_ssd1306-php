<?php

declare(strict_types=1);

/**
 * SSD1306-PHP Graphics Demo
 * 
 * Demonstrates all the graphics capabilities of the SSD1306-PHP library:
 * - Pixels, lines, rectangles, circles
 * - Text rendering with different sizes
 * - Display effects (invert, contrast, dim)
 * - Scrolling animations
 * 
 * This example can be run with or without Composer:
 * - With Composer: php examples/graphics_demo.php
 * - Without Composer: Direct class inclusion (as shown below)
 */

// Autoload - try Composer first, fallback to direct inclusion
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../src/SSD1306.php';
}

use ProjectSaturnStudios\SSD1306\SSD1306;

echo "SSD1306-PHP Graphics Demo\n";
echo "========================\n\n";

// Initialize display (Yahboom CUBE defaults: 128x32, bus 7, address 0x3C)
$display = new SSD1306(debug: true);

if (!$display->begin()) {
    die("❌ Failed to initialize SSD1306 display\n");
}

echo "✅ Display initialized: {$display->getWidth()}x{$display->getHeight()}\n\n";

// Demo 1: Basic shapes
echo "🎨 Demo 1: Basic Shapes\n";
$display->clear();

// Draw some pixels
for ($i = 0; $i < 10; $i++) {
    $x = random_int(0, $display->getWidth() - 1);
    $y = random_int(0, $display->getHeight() - 1);
    $display->pixel($x, $y, SSD1306::WHITE);
}

// Draw lines
$display->line(0, 0, 127, 31, SSD1306::WHITE);
$display->line(0, 31, 127, 0, SSD1306::WHITE);

// Draw rectangles
$display->rectangle(10, 5, 30, 15, SSD1306::WHITE);
$display->rectangle(50, 8, 25, 10, SSD1306::WHITE, true); // filled

// Draw circles
$display->circle(100, 15, 8, SSD1306::WHITE);
$display->circle(110, 20, 5, SSD1306::WHITE, true); // filled

$display->display();
sleep(3);

// Demo 2: Text rendering
echo "📝 Demo 2: Text Rendering\n";
$display->clear();

$display->text("Size 1", 0, 0, 1);
$display->text("Size 2", 0, 10, 2);
$display->text("Hello!", 0, 24, 1);

$display->display();
sleep(3);

// Demo 3: Display effects
echo "✨ Demo 3: Display Effects\n";

// Contrast test
echo "   - Testing contrast...\n";
$display->setContrast(50);
sleep(1);
$display->setContrast(255);
sleep(1);

// Invert test
echo "   - Testing invert...\n";
$display->invertDisplay(true);
sleep(1);
$display->invertDisplay(false);
sleep(1);

// Dim test
echo "   - Testing dim...\n";
$display->dim(true);
sleep(1);
$display->dim(false);
sleep(1);

// Demo 4: Animation
echo "🎬 Demo 4: Simple Animation\n";
for ($frame = 0; $frame < 30; $frame++) {
    $display->clear();
    
    // Bouncing ball
    $x = (int)(64 + 50 * sin($frame * 0.2));
    $y = (int)(16 + 10 * cos($frame * 0.3));
    $display->circle($x, $y, 3, SSD1306::WHITE, true);
    
    // Frame counter
    $display->text("Frame: $frame", 0, 0, 1);
    
    $display->display();
    usleep(100000); // 100ms = 10 FPS
}

// Demo 5: Pixel manipulation
echo "🔍 Demo 5: Pixel Manipulation\n";
$display->clear();

// Draw a pattern and read it back
for ($x = 0; $x < 64; $x += 4) {
    for ($y = 0; $y < 32; $y += 4) {
        $display->pixel($x, $y, SSD1306::WHITE);
    }
}

$display->display();
sleep(1);

// Read some pixels back
echo "   - Reading pixels:\n";
for ($x = 0; $x < 64; $x += 8) {
    for ($y = 0; $y < 32; $y += 8) {
        $pixel = $display->getPixel($x, $y);
        echo "     Pixel at ($x, $y): $pixel\n";
    }
}

sleep(2);

// Demo 6: Scrolling effects
echo "📜 Demo 6: Scrolling Effects\n";
$display->clear();
$display->text("SCROLLING DEMO", 10, 8, 2);
$display->text("Watch this text scroll!", 0, 24, 1);
$display->display();
sleep(1);

echo "   - Horizontal scroll right...\n";
$display->startScrollRight(0, 3);
sleep(3);
$display->stopScroll();

echo "   - Horizontal scroll left...\n";
$display->startScrollLeft(0, 3);
sleep(3);
$display->stopScroll();

echo "   - Diagonal scroll right...\n";
$display->startScrollDiagRight(0, 3);
sleep(3);
$display->stopScroll();

echo "   - Diagonal scroll left...\n";
$display->startScrollDiagLeft(0, 3);
sleep(3);
$display->stopScroll();

// Final demo: Complex graphics
echo "🎯 Final Demo: Complex Graphics\n";
$display->clear();

// Draw a house
$display->rectangle(30, 15, 40, 15, SSD1306::WHITE); // house base
$display->line(30, 15, 50, 5, SSD1306::WHITE);       // roof left
$display->line(50, 5, 70, 15, SSD1306::WHITE);       // roof right
$display->rectangle(35, 20, 8, 10, SSD1306::WHITE);  // door
$display->rectangle(55, 18, 6, 6, SSD1306::WHITE);   // window

// Draw a sun
$display->circle(100, 10, 6, SSD1306::WHITE, true);
for ($angle = 0; $angle < 360; $angle += 45) {
    $rad = deg2rad($angle);
    $x1 = 100 + (int)(8 * cos($rad));
    $y1 = 10 + (int)(8 * sin($rad));
    $x2 = 100 + (int)(12 * cos($rad));
    $y2 = 10 + (int)(12 * sin($rad));
    $display->line($x1, $y1, $x2, $y2, SSD1306::WHITE);
}

// Add some clouds
$display->circle(15, 8, 4, SSD1306::WHITE);
$display->circle(20, 8, 5, SSD1306::WHITE);
$display->circle(25, 8, 4, SSD1306::WHITE);

$display->text("Home Sweet Home", 0, 0, 1);
$display->display();

echo "🏠 House scene complete!\n";
sleep(5);

// Cleanup
echo "\n🧹 Cleaning up...\n";
$display->clear();
$display->text("Demo Complete!", 20, 8, 1);
$display->text("Thanks for watching!", 0, 20, 1);
$display->display();
sleep(3);

$display->clear();
$display->display();
$display->end();

echo "✅ Graphics demo completed successfully!\n";
echo "\n💡 This demo showcased:\n";
echo "   - Basic shapes (pixels, lines, rectangles, circles)\n";
echo "   - Text rendering with different sizes\n";
echo "   - Display effects (contrast, invert, dim)\n";
echo "   - Simple animation\n";
echo "   - Pixel manipulation and reading\n";
echo "   - Scrolling effects\n";
echo "   - Complex graphics composition\n";
echo "\n🚀 Ready for your own SSD1306 projects!\n";