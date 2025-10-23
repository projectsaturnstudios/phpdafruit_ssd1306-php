<?php
declare(strict_types=1);
use PhpdaFruit\SSD1306\UI\Menu;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

describe('Menu Construction', function () {
    it('creates menu with display', function () {
        $display = DisplayFactory::forTesting();
        $menu = new Menu($display);
        
        expect($menu)->toBeInstanceOf(Menu::class)
            ->and($menu->getItemCount())->toBe(0);
    });
});

describe('Menu Item Management', function () {
    it('adds items', function () {
        $display = DisplayFactory::forTesting();
        $menu = new Menu($display);
        
        $menu->addItem('Item 1')->addItem('Item 2');
        
        expect($menu->getItemCount())->toBe(2);
    });

    it('clears items', function () {
        $display = DisplayFactory::forTesting();
        $menu = new Menu($display);
        
        $menu->addItem('Item 1')->addItem('Item 2')->clearItems();
        
        expect($menu->getItemCount())->toBe(0);
    });

    it('gets all items', function () {
        $display = DisplayFactory::forTesting();
        $menu = new Menu($display);
        
        $menu->addItem('Item 1')->addItem('Item 2');
        $items = $menu->getItems();
        
        expect($items)->toHaveCount(2)
            ->and($items[0]['label'])->toBe('Item 1');
    });
});

describe('Menu Navigation', function () {
    it('selects next item', function () {
        $display = DisplayFactory::forTesting();
        $menu = new Menu($display);
        
        $menu->addItem('A')->addItem('B')->addItem('C');
        $menu->selectNext();
        
        expect($menu->getSelectedIndex())->toBe(1);
    });

    it('wraps to first when selecting next from last', function () {
        $display = DisplayFactory::forTesting();
        $menu = new Menu($display);
        
        $menu->addItem('A')->addItem('B');
        $menu->setSelectedIndex(1)->selectNext();
        
        expect($menu->getSelectedIndex())->toBe(0);
    });

    it('selects previous item', function () {
        $display = DisplayFactory::forTesting();
        $menu = new Menu($display);
        
        $menu->addItem('A')->addItem('B')->addItem('C');
        $menu->setSelectedIndex(2)->selectPrevious();
        
        expect($menu->getSelectedIndex())->toBe(1);
    });

    it('wraps to last when selecting previous from first', function () {
        $display = DisplayFactory::forTesting();
        $menu = new Menu($display);
        
        $menu->addItem('A')->addItem('B');
        $menu->selectPrevious();
        
        expect($menu->getSelectedIndex())->toBe(1);
    });
});

describe('Menu Activation', function () {
    it('executes callback on activate', function () {
        $display = DisplayFactory::forTesting();
        $menu = new Menu($display);
        $called = false;
        
        $menu->addItem('Test', function() use (&$called) {
            $called = true;
            return 'result';
        });
        
        $result = $menu->activate();
        
        expect($called)->toBeTrue()
            ->and($result)->toBe('result');
    });

    it('returns null for empty menu', function () {
        $display = DisplayFactory::forTesting();
        $menu = new Menu($display);
        
        $result = $menu->activate();
        
        expect($result)->toBeNull();
    });
});

