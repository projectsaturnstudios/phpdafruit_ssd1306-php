<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306;

use SSD1306;

/**
 * PHP wrapper for the SSD1306 C extension
 * 
 * Provides a clean, type-safe interface to control SSD1306 OLED displays
 * over I2C. This class wraps all public methods from the SSD1306 extension
 * which extends AdafruitGFX for complete graphics capabilities.
 */
class SSD1306Display
{
    private SSD1306 $display;

    /**
     * Create a new SSD1306 display instance
     *
     * @param int $width Display width in pixels (typically 128)
     * @param int $height Display height in pixels (32 or 64)
     * @param string $i2cDevicePath Path to I2C device (e.g., '/dev/i2c-7')
     * @param int $rstPin Reset pin (-1 for no reset)
     * @param int $wireClk I2C clock speed in Hz (default: 400000)
     * @param int $restoreClk Clock speed to restore after operations (default: 100000)
     */
    public function __construct(
        int $width,
        int $height,
        string $i2cDevicePath,
        int $rstPin = -1,
        int $wireClk = 400000,
        int $restoreClk = 100000
    ) {
        $this->display = new SSD1306(
            $width,
            $height,
            $i2cDevicePath,
            $rstPin,
            $wireClk,
            $restoreClk
        );
    }

    // ========================================================================
    // I2C Configuration Getters
    // ========================================================================

    /**
     * Get the I2C device path
     *
     * @return string Device path (e.g., '/dev/i2c-7')
     */
    public function getDevicePath(): string
    {
        return $this->display->getDevicePath();
    }

    /**
     * Get the I2C address
     *
     * @return int I2C address (e.g., 0x3C or 0x3D)
     */
    public function getI2CAddress(): int
    {
        return $this->display->getI2CAddress();
    }

    /**
     * Get the I2C file descriptor
     *
     * @return int File descriptor for the I2C device
     */
    public function getI2CFileDescriptor(): int
    {
        return $this->display->getI2CFileDescriptor();
    }

    /**
     * Get the reset pin number
     *
     * @return int Reset pin (-1 if not used)
     */
    public function getResetPin(): int
    {
        return $this->display->getResetPin();
    }

    /**
     * Get the I2C wire clock speed
     *
     * @return int Clock speed in Hz
     */
    public function getWireClk(): int
    {
        return $this->display->getWireClk();
    }

    /**
     * Get the restore clock speed
     *
     * @return int Restore clock speed in Hz
     */
    public function getRestoreClk(): int
    {
        return $this->display->getRestoreClk();
    }

    // ========================================================================
    // Display State Getters
    // ========================================================================

    /**
     * Get the VCC state
     *
     * @return int VCC state (typically 0x02 for external VCC)
     */
    public function getVCCState(): int
    {
        return $this->display->getVCCState();
    }

    /**
     * Get current contrast/brightness level
     *
     * @return int Contrast value (0-255)
     */
    public function getContrast(): int
    {
        return $this->display->getContrast();
    }

    /**
     * Check if display buffer is allocated
     *
     * @return bool True if buffer is allocated, false otherwise
     */
    public function hasBuffer(): bool
    {
        return $this->display->hasBuffer();
    }

    // ========================================================================
    // Low-level Command Functions
    // ========================================================================

    /**
     * Send a single command byte to the display
     *
     * @param int $command Command byte to send
     * @return void
     */
    public function ssd1306_command(int $command): void
    {
        $this->display->ssd1306_command($command);
    }

    /**
     * Send multiple command bytes to the display
     *
     * @param array<int> $commands Array of command bytes
     * @return void
     */
    public function ssd1306_commandList(array $commands): void
    {
        $this->display->ssd1306_commandList($commands);
    }

    // ========================================================================
    // Display Initialization and Buffer Management
    // ========================================================================

    /**
     * Initialize the display and allocate frame buffer
     * Must be called before any drawing operations
     *
     * @param int $vccstate VCC generation state (0x02 for external VCC)
     * @param int $i2caddr I2C address (0 for auto-detect)
     * @param bool $reset Whether to perform hardware reset
     * @return bool True on success, false on I2C failure
     */
    public function begin(int $vccstate = 0x02, int $i2caddr = 0, bool $reset = true): bool
    {
        return $this->display->begin($vccstate, $i2caddr, $reset);
    }

    /**
     * Clear the display buffer (set all pixels to 0)
     * Does not update the physical display until display() is called
     *
     * @return void
     */
    public function clearDisplay(): void
    {
        $this->display->clearDisplay();
    }

    /**
     * Push the frame buffer to the physical display
     * Call this after drawing operations to make them visible
     *
     * @return void
     */
    public function display(): void
    {
        $this->display->display();
    }

    // ========================================================================
    // Drawing Functions (SSD1306-specific)
    // ========================================================================

    /**
     * Draw a single pixel
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $color Color (1 = white/on, 0 = black/off)
     * @return void
     */
    public function drawPixel(int $x, int $y, int $color): void
    {
        $this->display->drawPixel($x, $y, $color);
    }

    /**
     * Draw a horizontal line (hardware-optimized)
     *
     * @param int $x Starting X coordinate
     * @param int $y Y coordinate
     * @param int $w Width in pixels
     * @param int $color Color (1 = white/on, 0 = black/off)
     * @return void
     */
    public function drawFastHLine(int $x, int $y, int $w, int $color): void
    {
        $this->display->drawFastHLine($x, $y, $w, $color);
    }

    /**
     * Draw a vertical line (hardware-optimized)
     *
     * @param int $x X coordinate
     * @param int $y Starting Y coordinate
     * @param int $h Height in pixels
     * @param int $color Color (1 = white/on, 0 = black/off)
     * @return void
     */
    public function drawFastVLine(int $x, int $y, int $h, int $color): void
    {
        $this->display->drawFastVLine($x, $y, $h, $color);
    }

    // ========================================================================
    // Utility Functions
    // ========================================================================

    /**
     * Read a pixel from the buffer
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @return bool True if pixel is set, false if clear
     */
    public function getPixel(int $x, int $y): bool
    {
        return $this->display->getPixel($x, $y);
    }

    /**
     * Adjust display brightness/contrast
     *
     * @param bool $dim True to dim display (0x00 contrast), false for normal brightness (0xCF)
     * @return void
     */
    public function dim(bool $dim): void
    {
        $this->display->dim($dim);
    }

    /**
     * Set display contrast/brightness level
     *
     * @param int $level Contrast level (0-255)
     * @return void
     */
    public function setContrast(int $level): void
    {
        $level = max(0, min(255, $level));
        $this->display->ssd1306_commandList([0x81, $level]); // SSD1306_SETCONTRAST
    }

    // ========================================================================
    // Display Control Functions
    // ========================================================================

    /**
     * Toggle hardware display inversion
     *
     * @param bool $invert True to invert (0=white, 1=black), false for normal
     * @return void
     */
    public function invertDisplay(bool $invert): void
    {
        $this->display->invertDisplay($invert);
    }

    // ========================================================================
    // Hardware Scrolling Functions
    // ========================================================================

    /**
     * Scroll display content to the right
     *
     * @param int $start Start page (0-7)
     * @param int $stop Stop page (0-7)
     * @return void
     */
    public function startscrollright(int $start, int $stop): void
    {
        $this->display->startscrollright($start, $stop);
    }

    /**
     * Scroll display content to the left
     *
     * @param int $start Start page (0-7)
     * @param int $stop Stop page (0-7)
     * @return void
     */
    public function startscrollleft(int $start, int $stop): void
    {
        $this->display->startscrollleft($start, $stop);
    }

    /**
     * Scroll diagonally (right and up)
     *
     * @param int $start Start page (0-7)
     * @param int $stop Stop page (0-7)
     * @return void
     */
    public function startscrolldiagright(int $start, int $stop): void
    {
        $this->display->startscrolldiagright($start, $stop);
    }

    /**
     * Scroll diagonally (left and up)
     *
     * @param int $start Start page (0-7)
     * @param int $stop Stop page (0-7)
     * @return void
     */
    public function startscrolldiagleft(int $start, int $stop): void
    {
        $this->display->startscrolldiagleft($start, $stop);
    }

    /**
     * Stop any active scrolling
     *
     * @return void
     */
    public function stopscroll(): void
    {
        $this->display->stopscroll();
    }

    // ========================================================================
    // Inherited AdafruitGFX Methods - Basic Getters
    // ========================================================================

    /**
     * Get display width (unrotated)
     *
     * @return int Width in pixels
     */
    public function getDisplayWidth(): int
    {
        return $this->display->getDisplayWidth();
    }

    /**
     * Get display height (unrotated)
     *
     * @return int Height in pixels
     */
    public function getDisplayHeight(): int
    {
        return $this->display->getDisplayHeight();
    }

    /**
     * Get display dimensions as array
     *
     * @return array{0: int, 1: int} [width, height]
     */
    public function getDisplaySize(): array
    {
        return $this->display->getDisplaySize();
    }

    /**
     * Get current width (rotation-adjusted)
     *
     * @return int Width in pixels
     */
    public function width(): int
    {
        return $this->display->width();
    }

    /**
     * Get current height (rotation-adjusted)
     *
     * @return int Height in pixels
     */
    public function height(): int
    {
        return $this->display->height();
    }

    // ========================================================================
    // Inherited AdafruitGFX Methods - Rotation
    // ========================================================================

    /**
     * Set display rotation
     *
     * @param int $rotation Rotation value (0-3)
     * @return void
     */
    public function setRotation(int $rotation): void
    {
        $this->display->setRotation($rotation);
    }

    /**
     * Get current rotation
     *
     * @return int Rotation value (0-3)
     */
    public function getRotation(): int
    {
        return $this->display->getRotation();
    }

    // ========================================================================
    // Inherited AdafruitGFX Methods - Low-level Write Functions
    // ========================================================================

    /**
     * Start a write transaction
     *
     * @return void
     */
    public function startWrite(): void
    {
        $this->display->startWrite();
    }

    /**
     * Write a single pixel (low-level)
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $color Color value
     * @return void
     */
    public function writePixel(int $x, int $y, int $color): void
    {
        $this->display->writePixel($x, $y, $color);
    }

    /**
     * Write a fast vertical line (low-level)
     *
     * @param int $x X coordinate
     * @param int $y Starting Y coordinate
     * @param int $h Height in pixels
     * @param int $color Color value
     * @return void
     */
    public function writeFastVLine(int $x, int $y, int $h, int $color): void
    {
        $this->display->writeFastVLine($x, $y, $h, $color);
    }

    /**
     * Write a fast horizontal line (low-level)
     *
     * @param int $x Starting X coordinate
     * @param int $y Y coordinate
     * @param int $w Width in pixels
     * @param int $color Color value
     * @return void
     */
    public function writeFastHLine(int $x, int $y, int $w, int $color): void
    {
        $this->display->writeFastHLine($x, $y, $w, $color);
    }

    /**
     * Write a filled rectangle (low-level)
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $w Width in pixels
     * @param int $h Height in pixels
     * @param int $color Color value
     * @return void
     */
    public function writeFillRect(int $x, int $y, int $w, int $h, int $color): void
    {
        $this->display->writeFillRect($x, $y, $w, $h, $color);
    }

    /**
     * End a write transaction
     *
     * @return void
     */
    public function endWrite(): void
    {
        $this->display->endWrite();
    }

    /**
     * Write a line (low-level)
     *
     * @param int $x0 Starting X coordinate
     * @param int $y0 Starting Y coordinate
     * @param int $x1 Ending X coordinate
     * @param int $y1 Ending Y coordinate
     * @param int $color Color value
     * @return void
     */
    public function writeLine(int $x0, int $y0, int $x1, int $y1, int $color): void
    {
        $this->display->writeLine($x0, $y0, $x1, $y1, $color);
    }

    // ========================================================================
    // Inherited AdafruitGFX Methods - High-level Draw Functions
    // ========================================================================

    /**
     * Fill a rectangle
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $w Width in pixels
     * @param int $h Height in pixels
     * @param int $color Color value
     * @return void
     */
    public function fillRect(int $x, int $y, int $w, int $h, int $color): void
    {
        $this->display->fillRect($x, $y, $w, $h, $color);
    }

    /**
     * Fill entire screen with a color
     *
     * @param int $color Color value
     * @return void
     */
    public function fillScreen(int $color): void
    {
        $this->display->fillScreen($color);
    }

    /**
     * Draw a line
     *
     * @param int $x0 Starting X coordinate
     * @param int $y0 Starting Y coordinate
     * @param int $x1 Ending X coordinate
     * @param int $y1 Ending Y coordinate
     * @param int $color Color value
     * @return void
     */
    public function drawLine(int $x0, int $y0, int $x1, int $y1, int $color): void
    {
        $this->display->drawLine($x0, $y0, $x1, $y1, $color);
    }

    /**
     * Draw a rectangle outline
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $w Width in pixels
     * @param int $h Height in pixels
     * @param int $color Color value
     * @return void
     */
    public function drawRect(int $x, int $y, int $w, int $h, int $color): void
    {
        $this->display->drawRect($x, $y, $w, $h, $color);
    }

    /**
     * Draw a circle outline
     *
     * @param int $x0 Center X coordinate
     * @param int $y0 Center Y coordinate
     * @param int $r Radius in pixels
     * @param int $color Color value
     * @return void
     */
    public function drawCircle(int $x0, int $y0, int $r, int $color): void
    {
        $this->display->drawCircle($x0, $y0, $r, $color);
    }

    /**
     * Draw circle helper (quarters)
     *
     * @param int $x0 Center X coordinate
     * @param int $y0 Center Y coordinate
     * @param int $r Radius in pixels
     * @param int $cornername Corner name bitfield
     * @param int $color Color value
     * @return void
     */
    public function drawCircleHelper(int $x0, int $y0, int $r, int $cornername, int $color): void
    {
        $this->display->drawCircleHelper($x0, $y0, $r, $cornername, $color);
    }

    /**
     * Draw a filled circle
     *
     * @param int $x0 Center X coordinate
     * @param int $y0 Center Y coordinate
     * @param int $r Radius in pixels
     * @param int $color Color value
     * @return void
     */
    public function fillCircle(int $x0, int $y0, int $r, int $color): void
    {
        $this->display->fillCircle($x0, $y0, $r, $color);
    }

    /**
     * Fill circle helper (quarters)
     *
     * @param int $x0 Center X coordinate
     * @param int $y0 Center Y coordinate
     * @param int $r Radius in pixels
     * @param int $corners Corner bitfield
     * @param int $delta Offset from center
     * @param int $color Color value
     * @return void
     */
    public function fillCircleHelper(int $x0, int $y0, int $r, int $corners, int $delta, int $color): void
    {
        $this->display->fillCircleHelper($x0, $y0, $r, $corners, $delta, $color);
    }

    /**
     * Draw an ellipse outline
     *
     * @param int $x0 Center X coordinate
     * @param int $y0 Center Y coordinate
     * @param int $rx Radius X in pixels
     * @param int $ry Radius Y in pixels
     * @param int $color Color value
     * @return void
     */
    public function drawEllipse(int $x0, int $y0, int $rx, int $ry, int $color): void
    {
        $this->display->drawEllipse($x0, $y0, $rx, $ry, $color);
    }

    /**
     * Draw a filled ellipse
     *
     * @param int $x0 Center X coordinate
     * @param int $y0 Center Y coordinate
     * @param int $rx Radius X in pixels
     * @param int $ry Radius Y in pixels
     * @param int $color Color value
     * @return void
     */
    public function fillEllipse(int $x0, int $y0, int $rx, int $ry, int $color): void
    {
        $this->display->fillEllipse($x0, $y0, $rx, $ry, $color);
    }

    /**
     * Draw a rounded rectangle outline
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $w Width in pixels
     * @param int $h Height in pixels
     * @param int $r Corner radius in pixels
     * @param int $color Color value
     * @return void
     */
    public function drawRoundRect(int $x, int $y, int $w, int $h, int $r, int $color): void
    {
        $this->display->drawRoundRect($x, $y, $w, $h, $r, $color);
    }

    /**
     * Draw a filled rounded rectangle
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $w Width in pixels
     * @param int $h Height in pixels
     * @param int $r Corner radius in pixels
     * @param int $color Color value
     * @return void
     */
    public function fillRoundRect(int $x, int $y, int $w, int $h, int $r, int $color): void
    {
        $this->display->fillRoundRect($x, $y, $w, $h, $r, $color);
    }

    /**
     * Draw a triangle outline
     *
     * @param int $x0 Vertex 0 X coordinate
     * @param int $y0 Vertex 0 Y coordinate
     * @param int $x1 Vertex 1 X coordinate
     * @param int $y1 Vertex 1 Y coordinate
     * @param int $x2 Vertex 2 X coordinate
     * @param int $y2 Vertex 2 Y coordinate
     * @param int $color Color value
     * @return void
     */
    public function drawTriangle(int $x0, int $y0, int $x1, int $y1, int $x2, int $y2, int $color): void
    {
        $this->display->drawTriangle($x0, $y0, $x1, $y1, $x2, $y2, $color);
    }

    /**
     * Draw a filled triangle
     *
     * @param int $x0 Vertex 0 X coordinate
     * @param int $y0 Vertex 0 Y coordinate
     * @param int $x1 Vertex 1 X coordinate
     * @param int $y1 Vertex 1 Y coordinate
     * @param int $x2 Vertex 2 X coordinate
     * @param int $y2 Vertex 2 Y coordinate
     * @param int $color Color value
     * @return void
     */
    public function fillTriangle(int $x0, int $y0, int $x1, int $y1, int $x2, int $y2, int $color): void
    {
        $this->display->fillTriangle($x0, $y0, $x1, $y1, $x2, $y2, $color);
    }

    // ========================================================================
    // Inherited AdafruitGFX Methods - Bitmap Functions
    // ========================================================================

    /**
     * Draw a bitmap
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param array<int> $bitmap Bitmap data array
     * @param int $w Width in pixels
     * @param int $h Height in pixels
     * @param int $color Foreground color
     * @param int $bg Background color (-1 for transparent)
     * @return void
     */
    public function drawBitmap(int $x, int $y, array $bitmap, int $w, int $h, int $color, int $bg = -1): void
    {
        $this->display->drawBitmap($x, $y, $bitmap, $w, $h, $color, $bg);
    }

    /**
     * Draw an XBitmap (LSB first)
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param array<int> $bitmap Bitmap data array
     * @param int $w Width in pixels
     * @param int $h Height in pixels
     * @param int $color Color value
     * @return void
     */
    public function drawXBitmap(int $x, int $y, array $bitmap, int $w, int $h, int $color): void
    {
        $this->display->drawXBitmap($x, $y, $bitmap, $w, $h, $color);
    }

    /**
     * Draw a grayscale bitmap
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param array<int> $bitmap Bitmap data array
     * @param int $w Width in pixels
     * @param int $h Height in pixels
     * @return void
     */
    public function drawGrayscaleBitmap(int $x, int $y, array $bitmap, int $w, int $h): void
    {
        $this->display->drawGrayscaleBitmap($x, $y, $bitmap, $w, $h);
    }

    // ========================================================================
    // Inherited AdafruitGFX Methods - Text Rendering
    // ========================================================================

    /**
     * Set text cursor position
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @return void
     */
    public function setCursor(int $x, int $y): void
    {
        $this->display->setCursor($x, $y);
    }

    /**
     * Set text size scaling
     *
     * @param int $size_x Width scale factor
     * @param int $size_y Height scale factor (0 = same as width)
     * @return void
     */
    public function setTextSize(int $size_x, int $size_y = 0): void
    {
        $this->display->setTextSize($size_x, $size_y);
    }

    /**
     * Set text color
     *
     * @param int $color Foreground color
     * @param int $bg Background color (-1 for transparent)
     * @return void
     */
    public function setTextColor(int $color, int $bg = -1): void
    {
        $this->display->setTextColor($color, $bg);
    }

    /**
     * Enable/disable text wrapping
     *
     * @param bool $wrap True to enable wrapping
     * @return void
     */
    public function setTextWrap(bool $wrap): void
    {
        $this->display->setTextWrap($wrap);
    }

    /**
     * Enable/disable Code Page 437 character set
     *
     * @param bool $enable True to enable CP437
     * @return void
     */
    public function cp437(bool $enable = true): void
    {
        $this->display->cp437($enable);
    }

    /**
     * Set font
     *
     * @param mixed $font Font name string or null for default
     * @return void
     */
    public function setFont(mixed $font = null): void
    {
        $this->display->setFont($font);
    }

    /**
     * Draw a character at position
     *
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $c Character code
     * @param int $color Foreground color
     * @param int $bg Background color
     * @param int $size_x Width scale factor
     * @param int $size_y Height scale factor (0 = same as width)
     * @return void
     */
    public function drawChar(int $x, int $y, int $c, int $color, int $bg, int $size_x, int $size_y = 0): void
    {
        $this->display->drawChar($x, $y, $c, $color, $bg, $size_x, $size_y);
    }

    /**
     * Write a character at current cursor position
     *
     * @param int $c Character code
     * @return int Number of characters written (1)
     */
    public function write(int $c): int
    {
        return $this->display->write($c);
    }

    /**
     * Get bounding box for a character
     *
     * @param int $c Character code
     * @param int $x X position (modified in-place)
     * @param int $y Y position (modified in-place)
     * @param int $minx Minimum X (output)
     * @param int $miny Minimum Y (output)
     * @param int $maxx Maximum X (output)
     * @param int $maxy Maximum Y (output)
     * @return void
     */
    public function charBounds(int $c, int &$x, int &$y, int &$minx, int &$miny, int &$maxx, int &$maxy): void
    {
        $this->display->charBounds($c, $x, $y, $minx, $miny, $maxx, $maxy);
    }

    /**
     * Get bounding box for a string
     *
     * @param string $str String to measure
     * @param int $x Starting X position
     * @param int $y Starting Y position
     * @param int $x1 Output: bounding box X
     * @param int $y1 Output: bounding box Y
     * @param int $w Output: bounding box width
     * @param int $h Output: bounding box height
     * @return void
     */
    public function getTextBounds(string $str, int $x, int $y, int &$x1, int &$y1, int &$w, int &$h): void
    {
        $this->display->getTextBounds($str, $x, $y, $x1, $y1, $w, $h);
    }

    /**
     * Get current cursor X position
     *
     * @return int X coordinate
     */
    public function getCursorX(): int
    {
        return $this->display->getCursorX();
    }

    /**
     * Get current cursor Y position
     *
     * @return int Y coordinate
     */
    public function getCursorY(): int
    {
        return $this->display->getCursorY();
    }
}
