<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Builder;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Factory for creating pre-configured displays
 * 
 * Provides convenient static methods for common display configurations,
 * making it easy to create displays with sensible defaults.
 */
class DisplayFactory
{
    /**
     * Create a standard 128x32 display with default settings
     * 
     * @param string $devicePath I2C device path (default: /dev/i2c-7)
     * @throws \RuntimeException if initialization fails
     */
    public static function standard(string $devicePath = '/dev/i2c-7'): SSD1306Display
    {
        return DisplayBuilder::create()
            ->size(128, 32)
            ->on($devicePath)
            ->brightness(255)
            ->textSize(1)
            ->build();
    }

    /**
     * Create a large 128x64 display
     * 
     * @param string $devicePath I2C device path (default: /dev/i2c-7)
     * @throws \RuntimeException if initialization fails
     */
    public static function large(string $devicePath = '/dev/i2c-7'): SSD1306Display
    {
        return DisplayBuilder::create()
            ->size(128, 64)
            ->on($devicePath)
            ->brightness(255)
            ->textSize(1)
            ->build();
    }

    /**
     * Create a high contrast display optimized for readability
     * 
     * @param string $devicePath I2C device path (default: /dev/i2c-7)
     * @throws \RuntimeException if initialization fails
     */
    public static function highContrast(string $devicePath = '/dev/i2c-7'): SSD1306Display
    {
        return DisplayBuilder::create()
            ->size(128, 32)
            ->on($devicePath)
            ->brightness(255)
            ->textSize(2)
            ->textColor(1, 0)
            ->build();
    }

    /**
     * Create a dashboard display with multiple text sizes
     * 
     * @param string $devicePath I2C device path (default: /dev/i2c-7)
     * @throws \RuntimeException if initialization fails
     */
    public static function dashboard(string $devicePath = '/dev/i2c-7'): SSD1306Display
    {
        return DisplayBuilder::create()
            ->size(128, 32)
            ->on($devicePath)
            ->brightness(200)
            ->textSize(1)
            ->textWrap(false)
            ->build();
    }

    /**
     * Create a dim display for low power/night mode
     * 
     * @param string $devicePath I2C device path (default: /dev/i2c-7)
     * @throws \RuntimeException if initialization fails
     */
    public static function dimmed(string $devicePath = '/dev/i2c-7'): SSD1306Display
    {
        return DisplayBuilder::create()
            ->size(128, 32)
            ->on($devicePath)
            ->brightness(50)
            ->textSize(1)
            ->build();
    }

    /**
     * Create an inverted display (white background, black text)
     * 
     * @param string $devicePath I2C device path (default: /dev/i2c-7)
     * @throws \RuntimeException if initialization fails
     */
    public static function inverted(string $devicePath = '/dev/i2c-7'): SSD1306Display
    {
        return DisplayBuilder::create()
            ->size(128, 32)
            ->on($devicePath)
            ->brightness(255)
            ->inverted(true)
            ->textSize(1)
            ->build();
    }

    /**
     * Create a display rotated 180 degrees
     * 
     * @param string $devicePath I2C device path (default: /dev/i2c-7)
     * @throws \RuntimeException if initialization fails
     */
    public static function rotated(string $devicePath = '/dev/i2c-7'): SSD1306Display
    {
        return DisplayBuilder::create()
            ->size(128, 32)
            ->on($devicePath)
            ->rotation(2) // 180 degrees
            ->textSize(1)
            ->build();
    }

    /**
     * Create a custom display with specific size
     * 
     * @param int $width Display width in pixels
     * @param int $height Display height in pixels
     * @param string $devicePath I2C device path
     * @throws \RuntimeException if initialization fails
     */
    public static function custom(int $width, int $height, string $devicePath = '/dev/i2c-7'): SSD1306Display
    {
        return DisplayBuilder::create()
            ->size($width, $height)
            ->on($devicePath)
            ->build();
    }

    /**
     * Create a display for testing (without initializing hardware)
     * 
     * @param int $width Display width
     * @param int $height Display height
     * @param string $devicePath I2C device path
     */
    public static function forTesting(int $width = 128, int $height = 32, string $devicePath = '/dev/i2c-99'): SSD1306Display
    {
        return DisplayBuilder::create()
            ->size($width, $height)
            ->on($devicePath)
            ->buildWithoutInit();
    }
}

