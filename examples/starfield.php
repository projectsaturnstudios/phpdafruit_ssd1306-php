<?php

declare(strict_types=1);

// Standalone starfield demo using the SSD1306-PHP package (extension-backed)
require_once __DIR__ . '/../src/SSD1306.php';

use ProjectSaturnStudios\SSD1306\SSD1306;

// Config
$WIDTH = 128;
$HEIGHT = 32;
$NUM_STARS = 30;   // keep modest for 32px tall display
$MIN_SPEED = 1;
$MAX_SPEED = 3;
$FPS_DELAY_US = 40000; // ~25 FPS

$display = new SSD1306(width: $WIDTH, height: $HEIGHT, i2cBus: 7, i2cAddress: 0x3C, debug: false);
if (!$display->begin()) {
    fwrite(STDERR, "Failed to initialize OLED on bus 7 (0x3C)\n");
    exit(1);
}

// Visible prelude
$display->clear();
$display->text('STARFIELD...', 0, 0);
$display->text('Ctrl+C to exit', 0, 16);
$display->display();
sleep(2);

// Initialize stars
$stars = [];
for ($i = 0; $i < $NUM_STARS; $i++) {
    $stars[] = [
        'x' => random_int(0, $WIDTH - 1),
        'y' => random_int(0, $HEIGHT - 1),
        'z' => random_int($MIN_SPEED, $MAX_SPEED),
    ];
}

$running = true;
if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGINT, function () use (&$running) { $running = false; });
}

while ($running) {
    // Update star positions
    for ($i = 0, $n = count($stars); $i < $n; $i++) {
        $stars[$i]['x'] -= $stars[$i]['z'];
        if ($stars[$i]['x'] < 0) {
            // Respawn on the right
            $stars[$i]['x'] = $WIDTH - 1;
            $stars[$i]['y'] = random_int(0, $HEIGHT - 1);
            $stars[$i]['z'] = random_int($MIN_SPEED, $MAX_SPEED);
        }
    }

    // Clear and draw stars
    $display->clear();
    foreach ($stars as $star) {
        $display->pixel($star['x'], $star['y'], SSD1306::WHITE);
        
        // Add trail for fast stars
        if ($star['z'] >= 3 && $star['x'] - 1 >= 0) {
            $display->pixel($star['x'] - 1, $star['y'], SSD1306::WHITE);
        }
    }
    
    $display->display();
    
    // Check for signals
    if (function_exists('pcntl_signal_dispatch')) {
        pcntl_signal_dispatch();
    }
    
    usleep($FPS_DELAY_US);
}

// Clean exit
$display->clear();
$display->display();
$display->end();

echo "\nStarfield demo ended.\n";
