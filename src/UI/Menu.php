<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\UI;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * Menu system with navigation
 * 
 * Provides scrollable menu with selectable items,
 * icons, and callbacks.
 */
class Menu
{
    private array $items = [];
    private int $selectedIndex = 0;
    private int $scrollOffset = 0;
    private int $visibleItems = 4;

    public function __construct(
        private SSD1306Display $display,
        private int $x = 0,
        private int $y = 0,
        private int $width = 128,
        private int $height = 32
    ) {
        $this->visibleItems = (int)($height / 8); // 8 pixels per item
    }

    /**
     * Add menu item
     *
     * @param string $label Item label
     * @param callable|null $callback Callback when item is activated
     * @param string|null $icon Icon name
     * @return self
     */
    public function addItem(string $label, ?callable $callback = null, ?string $icon = null): self
    {
        $this->items[] = [
            'label' => $label,
            'callback' => $callback,
            'icon' => $icon
        ];
        
        return $this;
    }

    /**
     * Select next item
     *
     * @return self
     */
    public function selectNext(): self
    {
        if (empty($this->items)) {
            return $this;
        }

        $this->selectedIndex = ($this->selectedIndex + 1) % count($this->items);
        $this->updateScroll();
        
        return $this;
    }

    /**
     * Select previous item
     *
     * @return self
     */
    public function selectPrevious(): self
    {
        if (empty($this->items)) {
            return $this;
        }

        $this->selectedIndex--;
        if ($this->selectedIndex < 0) {
            $this->selectedIndex = count($this->items) - 1;
        }
        
        $this->updateScroll();
        
        return $this;
    }

    /**
     * Activate selected item (execute callback)
     *
     * @return mixed Callback return value
     */
    public function activate(): mixed
    {
        if (empty($this->items) || !isset($this->items[$this->selectedIndex])) {
            return null;
        }

        $item = $this->items[$this->selectedIndex];
        
        if ($item['callback'] && is_callable($item['callback'])) {
            return call_user_func($item['callback']);
        }
        
        return null;
    }

    /**
     * Get selected item index
     *
     * @return int
     */
    public function getSelectedIndex(): int
    {
        return $this->selectedIndex;
    }

    /**
     * Set selected item index
     *
     * @param int $index Item index
     * @return self
     */
    public function setSelectedIndex(int $index): self
    {
        if ($index >= 0 && $index < count($this->items)) {
            $this->selectedIndex = $index;
            $this->updateScroll();
        }
        
        return $this;
    }

    /**
     * Get all menu items
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get item count
     *
     * @return int
     */
    public function getItemCount(): int
    {
        return count($this->items);
    }

    /**
     * Clear all items
     *
     * @return self
     */
    public function clearItems(): self
    {
        $this->items = [];
        $this->selectedIndex = 0;
        $this->scrollOffset = 0;
        
        return $this;
    }

    /**
     * Render the menu
     *
     * @return void
     */
    public function render(): void
    {
        if (empty($this->items)) {
            return;
        }

        $itemHeight = 8;
        $startIndex = $this->scrollOffset;
        $endIndex = min($startIndex + $this->visibleItems, count($this->items));

        for ($i = $startIndex; $i < $endIndex; $i++) {
            $item = $this->items[$i];
            $yPos = $this->y + (($i - $startIndex) * $itemHeight);
            
            // Draw selection indicator
            if ($i === $this->selectedIndex) {
                $this->display->fillRect($this->x, $yPos, 3, $itemHeight, 1);
            }
            
            // Draw item text
            $textX = $this->x + 6;
            $this->display->setCursor($textX, $yPos);
            $this->display->setTextSize(1);
            $this->display->setTextColor(1);
            
            foreach (str_split($item['label']) as $char) {
                $this->display->write(ord($char));
            }
        }

        // Draw scrollbar if needed
        if (count($this->items) > $this->visibleItems) {
            $this->renderScrollbar();
        }
    }

    /**
     * Update scroll offset based on selection
     *
     * @return void
     */
    private function updateScroll(): void
    {
        if ($this->selectedIndex < $this->scrollOffset) {
            $this->scrollOffset = $this->selectedIndex;
        } elseif ($this->selectedIndex >= $this->scrollOffset + $this->visibleItems) {
            $this->scrollOffset = $this->selectedIndex - $this->visibleItems + 1;
        }
    }

    /**
     * Render scrollbar indicator
     *
     * @return void
     */
    private function renderScrollbar(): void
    {
        $scrollbarX = $this->x + $this->width - 2;
        $scrollbarHeight = $this->height;
        $thumbHeight = max(4, (int)(($this->visibleItems / count($this->items)) * $scrollbarHeight));
        $thumbY = (int)(($this->scrollOffset / (count($this->items) - $this->visibleItems)) * ($scrollbarHeight - $thumbHeight));
        
        $this->display->fillRect($scrollbarX, $this->y + $thumbY, 2, $thumbHeight, 1);
    }
}

