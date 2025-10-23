<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Effects\ScrollingText;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

describe('ScrollingText Construction', function () {
    it('creates with default parameters', function () {
        $effect = new ScrollingText();
        
        expect($effect)->toBeInstanceOf(ScrollingText::class);
    });

    it('creates with custom direction', function () {
        $effect = new ScrollingText(ScrollingText::DIRECTION_RIGHT);
        
        expect($effect)->toBeInstanceOf(ScrollingText::class);
    });

    it('creates with custom distance', function () {
        $effect = new ScrollingText(ScrollingText::DIRECTION_LEFT, 200);
        
        expect($effect)->toBeInstanceOf(ScrollingText::class);
    });
});

describe('ScrollingText Rendering', function () {
    it('renders at start position', function () {
        $display = DisplayFactory::forTesting();
        $effect = new ScrollingText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, 0.0);
        })->not->toThrow(Exception::class);
    });

    it('renders at middle position', function () {
        $display = DisplayFactory::forTesting();
        $effect = new ScrollingText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });

    it('renders at end position', function () {
        $display = DisplayFactory::forTesting();
        $effect = new ScrollingText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, 1.0);
        })->not->toThrow(Exception::class);
    });

    it('handles all directions', function () {
        $display = DisplayFactory::forTesting();
        
        $directions = [
            ScrollingText::DIRECTION_LEFT,
            ScrollingText::DIRECTION_RIGHT,
            ScrollingText::DIRECTION_UP,
            ScrollingText::DIRECTION_DOWN
        ];
        
        foreach ($directions as $direction) {
            $effect = new ScrollingText($direction);
            expect(function () use ($display, $effect) {
                $effect->render($display, 'Test', 0, 0, 0.5);
            })->not->toThrow(Exception::class);
        }
    });
});

describe('ScrollingText Progress', function () {
    it('clamps progress below 0', function () {
        $display = DisplayFactory::forTesting();
        $effect = new ScrollingText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, -0.5);
        })->not->toThrow(Exception::class);
    });

    it('clamps progress above 1', function () {
        $display = DisplayFactory::forTesting();
        $effect = new ScrollingText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, 1.5);
        })->not->toThrow(Exception::class);
    });

    it('reports complete at progress 1', function () {
        $effect = new ScrollingText();
        
        expect($effect->isComplete(1.0))->toBeTrue();
    });

    it('reports not complete at progress 0.5', function () {
        $effect = new ScrollingText();
        
        expect($effect->isComplete(0.5))->toBeFalse();
    });
});

describe('ScrollingText Reset', function () {
    it('resets without error', function () {
        $effect = new ScrollingText();
        
        expect(function () use ($effect) {
            $effect->reset();
        })->not->toThrow(Exception::class);
    });
});

