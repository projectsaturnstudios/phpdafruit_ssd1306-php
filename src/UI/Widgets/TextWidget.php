<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\UI\Widgets;

use PhpdaFruit\SSD1306\UI\Widget;

/**
 * Text display widget
 * 
 * Displays simple text with optional title and border.
 */
class TextWidget extends Widget
{
    private string $title = '';
    private string $text = '';
    private bool $showBorder = true;

    /**
     * Set widget title
     *
     * @param string $title Title text
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set widget text
     *
     * @param string $text Main text content
     * @return self
     */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Set border visibility
     *
     * @param bool $show Show border
     * @return self
     */
    public function setShowBorder(bool $show): self
    {
        $this->showBorder = $show;
        return $this;
    }

    public function render(): void
    {
        if (!$this->visible) {
            return;
        }

        // Draw border
        if ($this->showBorder) {
            $this->display->drawRect($this->x, $this->y, $this->width, $this->height, 1);
        }

        // Draw title if set
        $contentY = $this->y + 2;
        if ($this->title) {
            $this->display->setCursor($this->x + 2, $contentY);
            $this->display->setTextSize(1);
            $this->display->setTextColor(1);
            
            foreach (str_split($this->title) as $char) {
                $this->display->write(ord($char));
            }
            
            $contentY += 10; // Move down for main text
        }

        // Draw main text
        if ($this->text) {
            $this->display->setCursor($this->x + 2, $contentY);
            $this->display->setTextSize(1);
            $this->display->setTextColor(1);
            
            foreach (str_split($this->text) as $char) {
                $this->display->write(ord($char));
            }
        }
    }
}

