<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\SSD1306Display;

/*
|--------------------------------------------------------------------------
| Shared Display Instance
|--------------------------------------------------------------------------
*/

// Global shared display instance - created once and reused across all tests
$GLOBALS['shared_display'] = null;
$GLOBALS['display_initialized'] = false;

// Get or create the shared display instance
function getSharedDisplay(): SSD1306Display
{
    if ($GLOBALS['shared_display'] === null) {
        $GLOBALS['shared_display'] = new SSD1306Display(128, 32, '/dev/i2c-7');
        
        // Try to initialize once
        if (file_exists('/dev/i2c-7')) {
            $GLOBALS['display_initialized'] = $GLOBALS['shared_display']->begin();
        }
    }
    
    return $GLOBALS['shared_display'];
}

// Check if display is available and initialized
function isDisplayAvailable(): bool
{
    return file_exists('/dev/i2c-7') && $GLOBALS['display_initialized'];
}

// Helper to clear display before test - always clear and display to ensure clean state
function clearTestDisplay(bool $pauseBefore = false): void
{
    if (isDisplayAvailable()) {
        if ($pauseBefore) {
            usleep(500000); // Pause 0.5s before clearing so user can see the output
        }
        $display = getSharedDisplay();
        $display->clearDisplay();
        $display->display(); // Push the clear to the physical display
    }
}

// Helper to pause after displaying so user can see the output
function pauseToView(float $seconds = 0.5): void
{
    if (isDisplayAvailable()) {
        usleep((int)($seconds * 1000000));
    }
}

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

expect()->extend('toBeI2CAddress', function () {
    $value = $this->value;
    expect($value)->toBeInt()
        ->toBeGreaterThanOrEqual(0x00)
        ->toBeLessThanOrEqual(0x7F);
    
    return $this;
});

expect()->extend('toBeValidDimension', function () {
    expect($this->value)->toBeInt()->toBeGreaterThan(0);
    
    return $this;
});

expect()->extend('toBeValidColor', function () {
    expect($this->value)->toBeInt()->toBeGreaterThanOrEqual(0)->toBeLessThanOrEqual(2);

    return $this;
});

expect()->extend('toBeCloseTo', function (float $expected, float $epsilon = 0.0001) {
    $actual = $this->value;
    $diff = abs($actual - $expected);
    
    expect($diff)->toBeLessThan($epsilon, 
        "Expected {$actual} to be close to {$expected} (within {$epsilon})");

    return $this;
});

