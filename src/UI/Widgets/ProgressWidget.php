<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\UI\Widgets;

use PhpdaFruit\SSD1306\UI\Widget;

/**
 * Progress bar widget
 * 
 * Displays progress with optional label and percentage.
 */
class ProgressWidget extends Widget
{
    private string $label = '';
    private float $value = 0;
    private float $min = 0;
    private float $max = 100;
    private bool $showPercent = true;

    /**
     * Set widget label
     *
     * @param string $label Label text
     * @return self
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Set progress value
     *
     * @param float $value Current value
     * @return self
     */
    public function setValue(float $value): self
    {
        $this->value = max($this->min, min($this->max, $value));
        return $this;
    }

    /**
     * Set value range
     *
     * @param float $min Minimum value
     * @param float $max Maximum value
     * @return self
     */
    public function setRange(float $min, float $max): self
    {
        $this->min = $min;
        $this->max = $max;
        $this->value = max($min, min($max, $this->value));
        return $this;
    }

    /**
     * Set whether to show percentage
     *
     * @param bool $show Show percentage
     * @return self
     */
    public function setShowPercent(bool $show): self
    {
        $this->showPercent = $show;
        return $this;
    }

    public function render(): void
    {
        if (!$this->visible) {
            return;
        }

        // Draw label if set
        $progressY = $this->y + 2;
        if ($this->label) {
            $this->display->setCursor($this->x + 2, $progressY);
            $this->display->setTextSize(1);
            $this->display->setTextColor(1);
            
            foreach (str_split($this->label) as $char) {
                $this->display->write(ord($char));
            }
            
            $progressY += 10;
        }

        // Calculate progress
        $range = $this->max - $this->min;
        $percent = $range > 0 ? (($this->value - $this->min) / $range) * 100 : 0;
        
        // Draw progress bar
        $barWidth = $this->width - 4;
        $barHeight = 8;
        $barX = $this->x + 2;
        
        $this->display->drawRect($barX, $progressY, $barWidth, $barHeight, 1);
        
        $filledWidth = (int)(($barWidth - 2) * ($percent / 100));
        if ($filledWidth > 0) {
            $this->display->fillRect($barX + 1, $progressY + 1, $filledWidth, $barHeight - 2, 1);
        }

        // Draw percentage
        if ($this->showPercent) {
            $percentText = sprintf('%d%%', (int)$percent);
            $textX = $barX + (int)($barWidth / 2) - (strlen($percentText) * 3);
            $this->display->setCursor($textX, $progressY + 1);
            $this->display->setTextSize(1);
            $this->display->setTextColor($filledWidth > $barWidth / 2 ? 0 : 1);
            
            foreach (str_split($percentText) as $char) {
                $this->display->write(ord($char));
            }
        }
    }
}

