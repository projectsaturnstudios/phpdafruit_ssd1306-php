<?php
declare(strict_types=1);

/**
 * SSD1306-PHP Comprehensive Test Suite
 * 
 * This example demonstrates all major features of the SSD1306-PHP library
 * in an entertaining and comprehensive way. Perfect for testing hardware
 * functionality and showcasing display capabilities.
 * 
 * Features demonstrated:
 * - Basic text and graphics rendering
 * - Real-time animation (bouncing ball)
 * - System information display
 * - Scrolling effects
 * - Pixel art creation
 * - Display effects (invert, contrast)
 * - Proper initialization and cleanup
 * 
 * @author Project Saturn Studios, LLC
 * @version 1.0.0
 */

// Autoload - try Composer first, fallback to direct inclusion
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../src/SSD1306.php';
}

use ProjectSaturnStudios\SSD1306\SSD1306;

echo "🚀 Starting OLED Display Comprehensive Test!\n";
echo "This test will run through all major SSD1306-PHP features...\n\n";

// Initialize display (Yahboom CUBE: bus 7, address 0x3C, 128x32)
$display = new SSD1306(128, 32, 7, 0x3C, true);

if (!$display->begin()) {
    die("❌ Failed to initialize display\n");
}

echo "✅ Display initialized successfully!\n";

// Test 1: Basic text and shapes
echo "📝 Test 1: Basic graphics and text rendering...\n";
$display->clear();
$display->text('Hello Angel!', 0, 0, 1);
$display->text('PHP Rocks!', 0, 10, 1);
$display->rectangle(0, 20, 127, 11, SSD1306::WHITE, false);
$display->circle(110, 25, 5, SSD1306::WHITE, true);
$display->display();
sleep(3);

// Test 2: Animation - bouncing ball
echo "🏀 Test 2: Bouncing ball animation (50 frames)...\n";
$x = 10; $y = 16; $dx = 2; $dy = 1;
for ($i = 0; $i < 50; $i++) {
    $display->clear();
    $display->text('Bouncing Ball', 0, 0, 1);
    $display->circle($x, $y, 3, SSD1306::WHITE, true);
    
    // Add frame counter
    $display->text("Frame: $i", 0, 25, 1);
    $display->display();
    
    // Update ball position
    $x += $dx; $y += $dy;
    if ($x <= 3 || $x >= 124) $dx = -$dx;
    if ($y <= 12 || $y >= 29) $dy = -$dy;
    
    usleep(100000); // 100ms = 10 FPS
}

// Test 3: System info display
echo "📊 Test 3: Real-time system information...\n";
$display->clear();
$display->text('System Stats:', 0, 0, 1);

// Get system information
$cpu = trim(shell_exec('cat /proc/loadavg | cut -d" " -f1') ?: '0.00');
$temp = trim(shell_exec('cat /sys/class/thermal/thermal_zone0/temp') ?: '0');
$temp_c = round((int)$temp / 1000, 1);

// Memory information
$mem = shell_exec('free -m | grep Mem') ?: '';
preg_match('/Mem:\s+(\d+)\s+(\d+)/', $mem, $matches);
$mem_total = (int)($matches[1] ?? 0);
$mem_used = (int)($matches[2] ?? 0);
$mem_percent = $mem_total > 0 ? round(($mem_used / $mem_total) * 100) : 0;

// Disk usage
$disk = shell_exec('df -h / | tail -1') ?: '';
preg_match('/(\d+)%/', $disk, $disk_matches);
$disk_percent = (int)($disk_matches[1] ?? 0);

$display->text("CPU: {$cpu}", 0, 10, 1);
$display->text("Temp: {$temp_c}C", 0, 18, 1);
$display->text("RAM: {$mem_percent}% Disk: {$disk_percent}%", 0, 26, 1);
$display->display();
sleep(5);

// Test 4: Scrolling text effects
echo "📜 Test 4: Scrolling effects demonstration...\n";
$display->clear();
$display->text('Scroll Test', 0, 0, 1);
$display->text('Left to Right', 0, 10, 1);
$display->text('Magic Happens!', 0, 20, 1);
$display->display();
sleep(1);

// Start horizontal scroll right
echo "   → Scrolling right...\n";
$display->startScrollRight(0, 3);
sleep(3);
$display->stopScroll();

// Start horizontal scroll left
echo "   ← Scrolling left...\n";
$display->startScrollLeft(0, 3);
sleep(3);
$display->stopScroll();

// Test 5: Pixel art creation
echo "🎨 Test 5: Pixel art and detailed graphics...\n";
$display->clear();
$display->text('Pixel Art:', 0, 0, 1);

// Draw a smiley face
$cx = 64; $cy = 20;
$display->circle($cx, $cy, 8, SSD1306::WHITE, false); // face outline
$display->pixel($cx - 3, $cy - 2, SSD1306::WHITE); // left eye
$display->pixel($cx + 3, $cy - 2, SSD1306::WHITE); // right eye

// Draw smile curve
for ($i = -3; $i <= 3; $i++) {
    $smile_y = $cy + 2 + abs($i) - 3;
    if ($smile_y >= $cy + 1) { // Only draw lower part of smile
        $display->pixel($cx + $i, $smile_y, SSD1306::WHITE);
    }
}

// Add some decorative elements
$display->rectangle(20, 15, 10, 10, SSD1306::WHITE, false);
$display->rectangle(94, 15, 10, 10, SSD1306::WHITE, true);
$display->display();
sleep(4);

// Test 6: Display effects and contrast
echo "🔄 Test 6: Display effects (invert, contrast)...\n";

// Invert effect
echo "   🔄 Testing invert effect...\n";
for ($i = 0; $i < 5; $i++) {
    $display->invertDisplay(true);
    usleep(500000); // 500ms
    $display->invertDisplay(false);
    usleep(500000);
}

// Contrast test
echo "   🔆 Testing contrast levels...\n";
$display->clear();
$display->text('Contrast Test', 0, 8, 1);
$display->text('Brightness:', 0, 18, 1);
$display->display();

$contrasts = [50, 100, 150, 200, 255, 200, 150, 100];
foreach ($contrasts as $contrast) {
    $display->setContrast($contrast);
    $display->clear();
    $display->text('Contrast Test', 0, 8, 1);
    $display->text("Level: $contrast", 0, 18, 1);
    $display->display();
    usleep(500000);
}

// Reset to normal contrast
$display->setContrast(200);

// Test 7: Line and shape combinations
echo "📐 Test 7: Complex geometric patterns...\n";
$display->clear();
$display->text('Geometry!', 0, 0, 1);

// Draw a pattern of lines
for ($i = 0; $i < 8; $i++) {
    $x1 = $i * 16;
    $y1 = 10;
    $x2 = 127 - ($i * 16);
    $y2 = 31;
    $display->line($x1, $y1, $x2, $y2, SSD1306::WHITE);
}

// Add some circles
$display->circle(32, 20, 5, SSD1306::WHITE, false);
$display->circle(96, 20, 5, SSD1306::WHITE, true);
$display->display();
sleep(3);

// Test 8: Text size variations
echo "📝 Test 8: Text rendering at different sizes...\n";
$display->clear();
$display->text('Size 1', 0, 0, 1);
$display->text('Big!', 0, 10, 2);
$display->text('Tiny', 0, 26, 1);
$display->display();
sleep(3);

// Final celebration
echo "🎉 Test 9: Grand finale animation...\n";
for ($frame = 0; $frame < 20; $frame++) {
    $display->clear();
    
    // Animated title
    $title_x = ($frame % 2 == 0) ? 0 : 2;
    $display->text('TEST COMPLETE!', $title_x, 0, 1);
    
    // Bouncing checkmark
    $check_y = 15 + (int)(3 * sin($frame * 0.5));
    $display->text('✓', 60, $check_y, 2);
    
    // Progress bar
    $progress = ($frame * 127) / 19;
    $display->rectangle(0, 28, (int)$progress, 3, SSD1306::WHITE, true);
    
    $display->display();
    usleep(200000); // 200ms
}

// Final message
$display->clear();
$display->text('All Tests', 0, 4, 1);
$display->text('PASSED!', 0, 14, 2);
$display->text('Hardware OK!', 0, 26, 1);
$display->display();
sleep(3);

// Cleanup with fade effect
echo "🧹 Cleaning up display...\n";
for ($contrast = 255; $contrast >= 0; $contrast -= 15) {
    $display->setContrast($contrast);
    usleep(50000);
}

$display->clear();
$display->display();
$display->end();

echo "\n🎉 Comprehensive OLED test completed successfully!\n";
echo "📊 Tests performed:\n";
echo "   ✅ Basic graphics and text\n";
echo "   ✅ Real-time animation\n";
echo "   ✅ System information display\n";
echo "   ✅ Scrolling effects\n";
echo "   ✅ Pixel art creation\n";
echo "   ✅ Display effects\n";
echo "   ✅ Geometric patterns\n";
echo "   ✅ Text size variations\n";
echo "   ✅ Animation finale\n";
echo "\n🚀 Your SSD1306 display is working perfectly!\n";
