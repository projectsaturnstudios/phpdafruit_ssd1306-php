<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Services;

use PhpdaFruit\SSD1306\SSD1306Display;
use PhpdaFruit\SSD1306\Effects\TextEffect;

/**
 * Text rendering service with utilities and effects
 * 
 * Provides high-level text rendering capabilities including
 * centering, wrapping, measurement, and effect application.
 */
class TextRenderer
{
    public function __construct(
        private SSD1306Display $display
    ) {}

    /**
     * Render text at specific position
     *
     * @param string $text Text to render
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param array $options Options: size, color, background, wrap
     * @return void
     */
    public function text(string $text, int $x, int $y, array $options = []): void
    {
        $this->applyTextOptions($options);
        
        $this->display->setCursor($x, $y);
        foreach (str_split($text) as $char) {
            $this->display->write(ord($char));
        }
    }

    /**
     * Render text centered horizontally
     *
     * @param string $text Text to render
     * @param int $y Y coordinate
     * @param array $options Text rendering options
     * @return void
     */
    public function centeredText(string $text, int $y, array $options = []): void
    {
        $measurements = $this->measureText($text, $options);
        $x = (int)(($this->display->getDisplayWidth() - $measurements['width']) / 2);
        
        $this->text($text, max(0, $x), $y, $options);
    }

    /**
     * Render wrapped text within a width constraint
     *
     * @param string $text Text to render
     * @param int $x Starting X coordinate
     * @param int $y Starting Y coordinate
     * @param int $width Maximum width
     * @param array $options Text rendering options
     * @return int Number of lines rendered
     */
    public function wrappedText(string $text, int $x, int $y, int $width, array $options = []): int
    {
        $this->applyTextOptions($options);
        
        $words = explode(' ', $text);
        $line = '';
        $lineCount = 0;
        $lineHeight = ($options['size'] ?? 1) * 8;
        
        foreach ($words as $word) {
            $testLine = $line === '' ? $word : $line . ' ' . $word;
            $measurements = $this->measureText($testLine, $options);
            
            if ($measurements['width'] > $width && $line !== '') {
                // Render current line
                $this->display->setCursor($x, $y + ($lineCount * $lineHeight));
                foreach (str_split($line) as $char) {
                    $this->display->write(ord($char));
                }
                $lineCount++;
                $line = $word;
            } else {
                $line = $testLine;
            }
        }
        
        // Render last line
        if ($line !== '') {
            $this->display->setCursor($x, $y + ($lineCount * $lineHeight));
            foreach (str_split($line) as $char) {
                $this->display->write(ord($char));
            }
            $lineCount++;
        }
        
        return $lineCount;
    }

    /**
     * Measure text dimensions
     *
     * @param string $text Text to measure
     * @param array $options Text options (size, font)
     * @return array{width: int, height: int} Text dimensions
     */
    public function measureText(string $text, array $options = []): array
    {
        $size = $options['size'] ?? 1;
        
        // Basic measurement (6 pixels per character width, 8 pixels height)
        // This is approximate - actual rendering may vary with fonts
        $charWidth = 6 * $size;
        $charHeight = 8 * $size;
        
        return [
            'width' => strlen($text) * $charWidth,
            'height' => $charHeight
        ];
    }

    /**
     * Apply a text effect
     *
     * @param TextEffect $effect The effect to apply
     * @param string $text Text to render
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param float $progress Effect progress (0.0 to 1.0)
     * @return void
     */
    public function applyEffect(TextEffect $effect, string $text, int $x, int $y, float $progress = 1.0): void
    {
        $effect->render($this->display, $text, $x, $y, $progress);
    }

    /**
     * Render text with a specific effect, animating to completion
     *
     * @param TextEffect $effect The effect to apply
     * @param string $text Text to render
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @param int $steps Number of animation steps
     * @param int $delayMs Delay between steps in milliseconds
     * @return void
     */
    public function animateEffect(TextEffect $effect, string $text, int $x, int $y, int $steps = 30, int $delayMs = 33): void
    {
        for ($i = 0; $i <= $steps; $i++) {
            $progress = $i / $steps;
            
            $this->display->clearDisplay();
            $effect->render($this->display, $text, $x, $y, $progress);
            $this->display->display();
            
            if ($i < $steps) {
                usleep($delayMs * 1000);
            }
        }
    }

    /**
     * Apply text options to display
     *
     * @param array $options Text options
     * @return void
     */
    private function applyTextOptions(array $options): void
    {
        if (isset($options['size'])) {
            $sizeY = $options['sizeY'] ?? $options['size'];
            $this->display->setTextSize($options['size'], $sizeY);
        }
        
        if (isset($options['color'])) {
            if (isset($options['background'])) {
                $this->display->setTextColor($options['color'], $options['background']);
            } else {
                $this->display->setTextColor($options['color']);
            }
        }
        
        if (isset($options['wrap'])) {
            $this->display->setTextWrap($options['wrap']);
        }
    }
}

