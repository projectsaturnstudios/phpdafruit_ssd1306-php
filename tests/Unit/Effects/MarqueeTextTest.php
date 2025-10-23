<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Effects\MarqueeText;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

describe('MarqueeText Construction', function () {
    it('creates with default parameters', function () {
        $effect = new MarqueeText();
        
        expect($effect)->toBeInstanceOf(MarqueeText::class);
    });

    it('creates with custom speed', function () {
        $effect = new MarqueeText(5);
        
        expect($effect)->toBeInstanceOf(MarqueeText::class);
    });

    it('creates with custom padding', function () {
        $effect = new MarqueeText(2, 40);
        
        expect($effect)->toBeInstanceOf(MarqueeText::class);
    });
});

describe('MarqueeText Rendering', function () {
    it('renders at start position', function () {
        $display = DisplayFactory::forTesting();
        $effect = new MarqueeText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Scrolling Message', 0, 0, 0.0);
        })->not->toThrow(Exception::class);
    });

    it('renders at middle position', function () {
        $display = DisplayFactory::forTesting();
        $effect = new MarqueeText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Scrolling Message', 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });

    it('renders at loop boundary', function () {
        $display = DisplayFactory::forTesting();
        $effect = new MarqueeText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Scrolling Message', 0, 0, 0.95);
        })->not->toThrow(Exception::class);
    });

    it('loops continuously', function () {
        $display = DisplayFactory::forTesting();
        $effect = new MarqueeText();
        
        // Progress > 1 should loop
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, 1.5);
            $effect->render($display, 'Test', 0, 0, 2.0);
            $effect->render($display, 'Test', 0, 0, 3.7);
        })->not->toThrow(Exception::class);
    });
});

describe('MarqueeText Loop Behavior', function () {
    it('never reports complete', function () {
        $effect = new MarqueeText();
        
        expect($effect->isComplete(0.0))->toBeFalse()
            ->and($effect->isComplete(0.5))->toBeFalse()
            ->and($effect->isComplete(1.0))->toBeFalse()
            ->and($effect->isComplete(5.0))->toBeFalse();
    });

    it('handles very long text', function () {
        $display = DisplayFactory::forTesting();
        $effect = new MarqueeText();
        $longText = str_repeat('A', 100);
        
        expect(function () use ($display, $effect, $longText) {
            $effect->render($display, $longText, 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });
});

describe('MarqueeText Edge Cases', function () {
    it('handles empty string', function () {
        $display = DisplayFactory::forTesting();
        $effect = new MarqueeText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, '', 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });

    it('handles single character', function () {
        $display = DisplayFactory::forTesting();
        $effect = new MarqueeText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'A', 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });

    it('resets without error', function () {
        $effect = new MarqueeText();
        
        expect(function () use ($effect) {
            $effect->reset();
        })->not->toThrow(Exception::class);
    });
});

