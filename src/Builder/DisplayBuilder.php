<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Builder;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Fluent Builder for SSD1306Display configuration
 * 
 * Provides a chainable API for creating and configuring displays
 * with all options specified before initialization.
 */
class DisplayBuilder
{
    private int $width = 128;
    private int $height = 32;
    private string $devicePath = '/dev/i2c-7';
    private int $rstPin = -1;
    private int $wireClk = 400000;
    private int $restoreClk = 100000;
    
    // Display settings to apply after begin()
    private ?string $font = null;
    private int $textSize = 1;
    private int $textSizeY = 1;
    private int $textColor = 1;
    private ?int $textBackground = null;
    private int $brightness = 255;
    private bool $inverted = false;
    private int $rotation = 0;
    private bool $textWrap = true;
    
    // Begin parameters
    private int $vccState = 0x02; // SWITCHCAPVCC
    private int $i2cAddr = 0; // Auto-detect
    private bool $reset = true;

    /**
     * Create a new builder instance
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Set display size
     */
    public function size(int $width, int $height): self
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * Set I2C device path
     */
    public function on(string $devicePath): self
    {
        $this->devicePath = $devicePath;
        return $this;
    }

    /**
     * Set reset pin
     */
    public function withResetPin(int $pin): self
    {
        $this->rstPin = $pin;
        return $this;
    }

    /**
     * Set I2C clock speeds
     */
    public function clockSpeed(int $wireClk, ?int $restoreClk = null): self
    {
        $this->wireClk = $wireClk;
        if ($restoreClk !== null) {
            $this->restoreClk = $restoreClk;
        }
        return $this;
    }

    /**
     * Set font
     */
    public function font(string $font): self
    {
        $this->font = $font;
        return $this;
    }

    /**
     * Set text size
     */
    public function textSize(int $size, ?int $sizeY = null): self
    {
        $this->textSize = $size;
        $this->textSizeY = $sizeY ?? $size;
        return $this;
    }

    /**
     * Set text color
     */
    public function textColor(int $color, ?int $background = null): self
    {
        $this->textColor = $color;
        $this->textBackground = $background;
        return $this;
    }

    /**
     * Set brightness/contrast
     */
    public function brightness(int $level): self
    {
        $this->brightness = max(0, min(255, $level));
        return $this;
    }

    /**
     * Enable/disable inverted display
     */
    public function inverted(bool $enabled = true): self
    {
        $this->inverted = $enabled;
        return $this;
    }

    /**
     * Set display rotation (0-3)
     */
    public function rotation(int $rotation): self
    {
        $this->rotation = $rotation % 4;
        return $this;
    }

    /**
     * Enable/disable text wrapping
     */
    public function textWrap(bool $enabled = true): self
    {
        $this->textWrap = $enabled;
        return $this;
    }

    /**
     * Set VCC state for begin()
     */
    public function vccState(int $state): self
    {
        $this->vccState = $state;
        return $this;
    }

    /**
     * Set I2C address for begin()
     */
    public function i2cAddress(int $address): self
    {
        $this->i2cAddr = $address;
        return $this;
    }

    /**
     * Enable/disable hardware reset
     */
    public function hardwareReset(bool $enabled = true): self
    {
        $this->reset = $enabled;
        return $this;
    }

    /**
     * Build and initialize the display
     * 
     * Creates the display, calls begin(), and applies all configured settings
     * 
     * @throws \RuntimeException if begin() fails
     */
    public function build(): SSD1306Display
    {
        // Create display
        $display = new SSD1306Display(
            $this->width,
            $this->height,
            $this->devicePath,
            $this->rstPin,
            $this->wireClk,
            $this->restoreClk
        );

        // Initialize display
        if (!$display->begin($this->vccState, $this->i2cAddr, $this->reset)) {
            throw new \RuntimeException("Failed to initialize display on {$this->devicePath}");
        }

        // Apply settings
        if ($this->font !== null) {
            $display->setFont($this->font);
        }
        
        $display->setTextSize($this->textSize, $this->textSizeY);
        
        if ($this->textBackground !== null) {
            $display->setTextColor($this->textColor, $this->textBackground);
        } else {
            $display->setTextColor($this->textColor);
        }
        
        $display->setTextWrap($this->textWrap);
        $display->setRotation($this->rotation);
        
        if ($this->inverted) {
            $display->invertDisplay(true);
        }
        
        if ($this->brightness !== 255) {
            $display->setContrast($this->brightness);
        }
        
        $display->clearDisplay();

        return $display;
    }

    /**
     * Build without calling begin() - for testing
     */
    public function buildWithoutInit(): SSD1306Display
    {
        return new SSD1306Display(
            $this->width,
            $this->height,
            $this->devicePath,
            $this->rstPin,
            $this->wireClk,
            $this->restoreClk
        );
    }
}

