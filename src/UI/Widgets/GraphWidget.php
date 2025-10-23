<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\UI\Widgets;

use PhpdaFruit\SSD1306\UI\Widget;

/**
 * Graph display widget
 * 
 * Displays data as a line graph with auto-scaling.
 */
class GraphWidget extends Widget
{
    private array $dataPoints = [];
    private int $maxPoints = 50;
    private string $label = '';
    private bool $autoScale = true;
    private float $minValue = 0;
    private float $maxValue = 100;

    /**
     * Add data point
     *
     * @param float $value Data value
     * @return self
     */
    public function addDataPoint(float $value): self
    {
        $this->dataPoints[] = $value;
        
        // Remove oldest point if exceeding max
        if (count($this->dataPoints) > $this->maxPoints) {
            array_shift($this->dataPoints);
        }
        
        return $this;
    }

    /**
     * Set data points
     *
     * @param array $points Array of values
     * @return self
     */
    public function setDataPoints(array $points): self
    {
        $this->dataPoints = array_slice($points, -$this->maxPoints);
        return $this;
    }

    /**
     * Set max points to display
     *
     * @param int $max Maximum points
     * @return self
     */
    public function setMaxPoints(int $max): self
    {
        $this->maxPoints = max(2, $max);
        return $this;
    }

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
     * Set value range
     *
     * @param float $min Minimum value
     * @param float $max Maximum value
     * @return self
     */
    public function setRange(float $min, float $max): self
    {
        $this->minValue = $min;
        $this->maxValue = $max;
        $this->autoScale = false;
        return $this;
    }

    public function render(): void
    {
        if (!$this->visible || empty($this->dataPoints)) {
            return;
        }

        // Draw label if set
        $graphY = $this->y + 2;
        if ($this->label) {
            $this->display->setCursor($this->x + 2, $graphY);
            $this->display->setTextSize(1);
            $this->display->setTextColor(1);
            
            foreach (str_split($this->label) as $char) {
                $this->display->write(ord($char));
            }
            
            $graphY += 10;
        }

        $graphHeight = $this->height - ($graphY - $this->y) - 2;
        $graphWidth = $this->width - 4;

        // Auto-scale if enabled
        if ($this->autoScale) {
            $this->minValue = min($this->dataPoints);
            $this->maxValue = max($this->dataPoints);
            
            // Add padding
            $range = $this->maxValue - $this->minValue;
            if ($range > 0) {
                $this->minValue -= $range * 0.1;
                $this->maxValue += $range * 0.1;
            }
        }

        $valueRange = $this->maxValue - $this->minValue;
        if ($valueRange == 0) {
            $valueRange = 1; // Avoid division by zero
        }

        // Draw graph points
        $pointCount = count($this->dataPoints);
        $xStep = $pointCount > 1 ? $graphWidth / ($pointCount - 1) : 0;

        for ($i = 0; $i < $pointCount - 1; $i++) {
            $value1 = $this->dataPoints[$i];
            $value2 = $this->dataPoints[$i + 1];
            
            $x1 = $this->x + 2 + (int)($i * $xStep);
            $x2 = $this->x + 2 + (int)(($i + 1) * $xStep);
            
            $y1 = $graphY + $graphHeight - (int)((($value1 - $this->minValue) / $valueRange) * $graphHeight);
            $y2 = $graphY + $graphHeight - (int)((($value2 - $this->minValue) / $valueRange) * $graphHeight);
            
            $this->display->drawLine($x1, $y1, $x2, $y2, 1);
        }

        // Draw border
        $this->display->drawRect($this->x, $graphY, $this->width, $graphHeight + 2, 1);
    }
}

