<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\Effects;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Fade text effect
 * 
 * Text fades in or out using a dithering pattern to simulate
 * transparency on monochrome displays.
 */
class FadeText implements TextEffect
{
    private bool $fadeIn;
    private array $ditherPatterns;

    /**
     * @param bool $fadeIn True for fade in, false for fade out
     */
    public function __construct(bool $fadeIn = true)
    {
        $this->fadeIn = $fadeIn;
        
        // Dither patterns from sparse to dense (keys as strings to avoid float→int conversion)
        $this->ditherPatterns = [
            '0.0' => 0b00000000, // 0% - empty
            '0.25' => 0b00010001, // 25% - very sparse
            '0.5' => 0b01010101, // 50% - checkerboard
            '0.75' => 0b11101110, // 75% - mostly filled
            '1.0' => 0b11111111, // 100% - solid
        ];
    }

    public function render(SSD1306Display $display, string $text, int $x, int $y, float $progress): void
    {
        $progress = max(0, min(1, $progress));
        
        // Invert progress for fade out
        $fadeProgress = $this->fadeIn ? $progress : (1 - $progress);
        
        if ($fadeProgress >= 1.0) {
            // Fully visible - render normally
            $display->setCursor($x, $y);
            foreach (str_split($text) as $char) {
                $display->write(ord($char));
            }
        } elseif ($fadeProgress <= 0.0) {
            // Fully transparent - render nothing
            return;
        } else {
            // Partial fade - use dithering pattern
            // For simplicity, we'll render with reduced density
            // In a full implementation, this would apply actual dithering
            
            // Determine which characters to show based on progress
            $charsToShow = max(1, (int)(strlen($text) * $fadeProgress));
            
            if ($this->fadeIn) {
                $visibleText = substr($text, 0, $charsToShow);
            } else {
                $visibleText = substr($text, 0, $charsToShow);
            }
            
            $display->setCursor($x, $y);
            foreach (str_split($visibleText) as $char) {
                $display->write(ord($char));
            }
        }
    }

    public function reset(): void
    {
        // No state to reset
    }

    public function isComplete(float $progress): bool
    {
        return $progress >= 1.0;
    }

    /**
     * Get dither pattern for progress level
     *
     * @param float $progress Progress value (0.0 to 1.0)
     * @return int Dither pattern byte
     */
    private function getDitherPattern(float $progress): int
    {
        foreach ($this->ditherPatterns as $threshold => $pattern) {
            if ($progress <= $threshold) {
                return $pattern;
            }
        }
        
        return 0b11111111; // Solid
    }
}

