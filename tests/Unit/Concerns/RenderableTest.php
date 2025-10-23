<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Concerns\Renderable;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

// Test class implementing Renderable
class TestRenderableComponent implements Renderable {
    private bool $visible = true;
    private int $x = 0;
    private int $y = 0;
    private int $width = 100;
    private int $height = 50;

    public function render(): void {}

    public function getBounds(): array {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'width' => $this->width,
            'height' => $this->height
        ];
    }

    public function setVisible(bool $visible): self {
        $this->visible = $visible;
        return $this;
    }

    public function isVisible(): bool {
        return $this->visible;
    }

    public function setBounds(int $x, int $y, int $width, int $height): self {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
        return $this;
    }
}

describe('Renderable Interface', function () {
    it('implements all required methods', function () {
        $component = new TestRenderableComponent();
        
        expect($component)->toBeInstanceOf(Renderable::class);
    });

    it('provides render method', function () {
        $component = new TestRenderableComponent();
        
        expect(method_exists($component, 'render'))->toBeTrue();
    });

    it('provides getBounds method', function () {
        $component = new TestRenderableComponent();
        $bounds = $component->getBounds();
        
        expect($bounds)->toHaveKey('x')
            ->and($bounds)->toHaveKey('y')
            ->and($bounds)->toHaveKey('width')
            ->and($bounds)->toHaveKey('height');
    });

    it('manages visibility', function () {
        $component = new TestRenderableComponent();
        
        expect($component->isVisible())->toBeTrue();
        
        $component->setVisible(false);
        
        expect($component->isVisible())->toBeFalse();
    });

    it('chains setVisible calls', function () {
        $component = new TestRenderableComponent();
        
        $result = $component->setVisible(false);
        
        expect($result)->toBe($component);
    });

    it('returns correct bounds', function () {
        $component = new TestRenderableComponent();
        $component->setBounds(10, 20, 80, 60);
        
        $bounds = $component->getBounds();
        
        expect($bounds['x'])->toBe(10)
            ->and($bounds['y'])->toBe(20)
            ->and($bounds['width'])->toBe(80)
            ->and($bounds['height'])->toBe(60);
    });
});

