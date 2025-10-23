<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\UI\Widgets;

use PhpdaFruit\SSD1306\UI\Widget;
use PhpdaFruit\SSD1306\Shapes\Icon;

/**
 * Icon display widget
 * 
 * Displays an icon with optional label.
 */
class IconWidget extends Widget
{
    private string $iconName = '';
    private string $label = '';
    private int $iconScale = 1;

    /**
     * Set icon name
     *
     * @param string $iconName Icon name from Icon registry
     * @return self
     */
    public function setIcon(string $iconName): self
    {
        $this->iconName = $iconName;
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
     * Set icon scale
     *
     * @param int $scale Scale factor
     * @return self
     */
    public function setIconScale(int $scale): self
    {
        $this->iconScale = max(1, $scale);
        return $this;
    }

    public function render(): void
    {
        if (!$this->visible || !$this->iconName) {
            return;
        }

        $icon = Icon::get($this->iconName);
        if (!$icon) {
            return;
        }

        // Center icon in widget
        $iconSize = $icon->getSize();
        $scaledWidth = $iconSize['width'] * $this->iconScale;
        $scaledHeight = $iconSize['height'] * $this->iconScale;
        
        $iconX = $this->x + (int)(($this->width - $scaledWidth) / 2);
        $iconY = $this->y + 2;

        // Draw icon
        $bitmap = $icon->getBitmap();
        for ($row = 0; $row < $iconSize['height']; $row++) {
            for ($col = 0; $col < $iconSize['width']; $col++) {
                $byte = $bitmap[$row];
                $bit = ($byte >> (7 - $col)) & 1;
                
                if ($bit) {
                    if ($this->iconScale === 1) {
                        $this->display->drawPixel($iconX + $col, $iconY + $row, 1);
                    } else {
                        $this->display->fillRect(
                            $iconX + ($col * $this->iconScale),
                            $iconY + ($row * $this->iconScale),
                            $this->iconScale,
                            $this->iconScale,
                            1
                        );
                    }
                }
            }
        }

        // Draw label below icon
        if ($this->label) {
            $labelY = $iconY + $scaledHeight + 2;
            $labelX = $this->x + (int)(($this->width - (strlen($this->label) * 6)) / 2);
            
            $this->display->setCursor($labelX, $labelY);
            $this->display->setTextSize(1);
            $this->display->setTextColor(1);
            
            foreach (str_split($this->label) as $char) {
                $this->display->write(ord($char));
            }
        }
    }
}

