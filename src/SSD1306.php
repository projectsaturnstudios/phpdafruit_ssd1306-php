<?php
declare(strict_types=1);

namespace ProjectSaturnStudios\SSD1306;

use RuntimeException;
use InvalidArgumentException;

/**
 * PHP port of Adafruit Python SSD1306 library
 * 
 * This class provides an intermediate layer between PHP and the ssd1306 extension,
 * implementing the same pattern as the Python Adafruit_SSD1306 library:
 * - Creates an image buffer using GD
 * - Draws to the buffer using GD functions
 * - Pushes the complete image to the display
 */
class SSD1306
{
    // Display dimensions
    public const WIDTH = 128;
    public const HEIGHT_32 = 32;
    public const HEIGHT_64 = 64;
    
    // Colors
    public const BLACK = 0;
    public const WHITE = 1;
    
    // Default I2C settings (Yahboom CUBE uses bus 7)
    public const DEFAULT_I2C_BUS = 7;
    public const DEFAULT_I2C_ADDRESS = 0x3C;
    
    private int $width;
    private int $height;
    private int $i2cBus;
    private int $i2cAddress;
    private bool $debug;
    
    // Extension handles the internal buffer
    
    public function __construct(
        int $width = self::WIDTH,
        int $height = self::HEIGHT_32,
        int $i2cBus = self::DEFAULT_I2C_BUS,
        int $i2cAddress = self::DEFAULT_I2C_ADDRESS,
        bool $debug = false
    ) {
        if (!extension_loaded('ssd1306')) {
            throw new RuntimeException('SSD1306 extension not found. Please install the ssd1306 PHP extension.');
        }
        
        // Validate parameters
        if ($width <= 0) {
            throw new InvalidArgumentException('Width must be greater than 0');
        }
        if ($height <= 0) {
            throw new InvalidArgumentException('Height must be greater than 0');
        }
        if ($i2cBus < 0) {
            throw new InvalidArgumentException('I2C bus must be non-negative');
        }
        if ($i2cAddress < 0) {
            throw new InvalidArgumentException('I2C address must be non-negative');
        }
        
        $this->width = $width;
        $this->height = $height;
        $this->i2cBus = $i2cBus;
        $this->i2cAddress = $i2cAddress;
        $this->debug = $debug;
        
        if ($this->debug) {
            echo "SSD1306 constructor: {$width}x{$height} display\n";
        }
        
        // Extension handles the buffer internally
    }
    
    public function __destruct()
    {
        // Extension handles cleanup
    }
    
    /**
     * Initialize the display - equivalent to Python's begin()
     */
    public function begin(): bool
    {
        try {
            // Use SWITCHCAPVCC (2) like Adafruit default so the charge pump is enabled
            \ssd1306_begin($this->i2cBus, $this->i2cAddress, $this->width, $this->height, 2);
            
            if ($this->debug) {
                echo "SSD1306 initialized on I2C bus {$this->i2cBus}, address 0x" . dechex($this->i2cAddress) . "\n";
            }
            
            $this->clear();
            $this->display();
            
            return true;
        } catch (\Throwable $e) {
            if ($this->debug) {
                echo "Failed to initialize SSD1306: " . $e->getMessage() . "\n";
            }
            return false;
        }
    }
    
    /**
     * Clear the display - equivalent to Python's clear()
     */
    public function clear(): void
    {
        \ssd1306_clear_display();
    }
    
    /**
     * Push the image buffer to the display - equivalent to Python's image()
     */
    public function display(): void
    {
        // Just update the hardware display - the extension handles the buffer
        \ssd1306_display();
    }
    
    /**
     * Draw text directly to the display - like Python's text()
     */
    public function text(string $text, int $x, int $y, int $size = 1, int $color = self::WHITE): void
    {
        \ssd1306_set_cursor($x, $y);
        \ssd1306_set_text_size($size);
        \ssd1306_set_text_color($color);
        \ssd1306_print($text);
    }
    
    /**
     * Draw a pixel
     */
    public function pixel(int $x, int $y, int $color = self::WHITE): void
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            return;
        }
        
        \ssd1306_draw_pixel($x, $y, $color);
    }
    
    /**
     * Draw a line
     */
    public function line(int $x0, int $y0, int $x1, int $y1, int $color = self::WHITE): void
    {
        \ssd1306_draw_line($x0, $y0, $x1, $y1, $color);
    }
    
    /**
     * Draw a rectangle
     */
    public function rectangle(int $x, int $y, int $width, int $height, int $color = self::WHITE, bool $filled = false): void
    {
        if ($filled) {
            \ssd1306_fill_rect($x, $y, $width, $height, $color);
        } else {
            \ssd1306_draw_rect($x, $y, $width, $height, $color);
        }
    }
    
    /**
     * Draw a circle
     */
    public function circle(int $x, int $y, int $radius, int $color = self::WHITE, bool $filled = false): void
    {
        if ($filled) {
            \ssd1306_fill_circle($x, $y, $radius, $color);
        } else {
            \ssd1306_draw_circle($x, $y, $radius, $color);
        }
    }
    
    /**
     * Get display dimensions
     */
    public function getWidth(): int
    {
        return $this->width;
    }
    
    public function getHeight(): int
    {
        return $this->height;
    }
    
    /**
     * Invert display colors - equivalent to Python's invert()
     */
    public function invertDisplay(bool $invert): void
    {
        \ssd1306_invert_display($invert);
    }
    
    /**
     * Dim the display - equivalent to Python's dim()
     */
    public function dim(bool $dim): void
    {
        \ssd1306_dim($dim);
    }
    
    /**
     * Set display contrast - equivalent to Python's set_contrast()
     */
    public function setContrast(int $contrast): void
    {
        if ($contrast < 0 || $contrast > 255) {
            throw new InvalidArgumentException('Contrast must be between 0 and 255');
        }
        \ssd1306_set_contrast($contrast);
    }
    
    /**
     * Get pixel value at coordinates - equivalent to Python's get_pixel()
     */
    public function getPixel(int $x, int $y): int
    {
        if ($x < 0 || $x >= $this->width || $y < 0 || $y >= $this->height) {
            return 0;
        }
        return \ssd1306_get_pixel($x, $y);
    }
    
    /**
     * Start horizontal scroll right - equivalent to Python's start_scroll_right()
     */
    public function startScrollRight(int $startPage, int $endPage): void
    {
        if ($startPage < 0 || $startPage > 7 || $endPage < 0 || $endPage > 7) {
            throw new InvalidArgumentException('Page numbers must be between 0 and 7');
        }
        \ssd1306_start_scroll_right($startPage, $endPage);
    }
    
    /**
     * Start horizontal scroll left - equivalent to Python's start_scroll_left()
     */
    public function startScrollLeft(int $startPage, int $endPage): void
    {
        if ($startPage < 0 || $startPage > 7 || $endPage < 0 || $endPage > 7) {
            throw new InvalidArgumentException('Page numbers must be between 0 and 7');
        }
        \ssd1306_start_scroll_left($startPage, $endPage);
    }
    
    /**
     * Start diagonal scroll right - equivalent to Python's start_scroll_diag_right()
     */
    public function startScrollDiagRight(int $startPage, int $endPage): void
    {
        if ($startPage < 0 || $startPage > 7 || $endPage < 0 || $endPage > 7) {
            throw new InvalidArgumentException('Page numbers must be between 0 and 7');
        }
        \ssd1306_start_scroll_diag_right($startPage, $endPage);
    }
    
    /**
     * Start diagonal scroll left - equivalent to Python's start_scroll_diag_left()
     */
    public function startScrollDiagLeft(int $startPage, int $endPage): void
    {
        if ($startPage < 0 || $startPage > 7 || $endPage < 0 || $endPage > 7) {
            throw new InvalidArgumentException('Page numbers must be between 0 and 7');
        }
        \ssd1306_start_scroll_diag_left($startPage, $endPage);
    }
    
    /**
     * Stop scrolling - equivalent to Python's stop_scroll()
     */
    public function stopScroll(): void
    {
        \ssd1306_stop_scroll();
    }
    
    /**
     * End display operations
     */
    public function end(): void
    {
        \ssd1306_end();
    }
    
}