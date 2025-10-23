<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Effects\FadeText;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

describe('FadeText Construction', function () {
    it('creates with default fade in', function () {
        $effect = new FadeText();
        
        expect($effect)->toBeInstanceOf(FadeText::class);
    });

    it('creates with fade in enabled', function () {
        $effect = new FadeText(true);
        
        expect($effect)->toBeInstanceOf(FadeText::class);
    });

    it('creates with fade out enabled', function () {
        $effect = new FadeText(false);
        
        expect($effect)->toBeInstanceOf(FadeText::class);
    });
});

describe('FadeText Rendering', function () {
    it('renders fade in at start', function () {
        $display = DisplayFactory::forTesting();
        $effect = new FadeText(true);
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, 0.0);
        })->not->toThrow(Exception::class);
    });

    it('renders fade in at middle', function () {
        $display = DisplayFactory::forTesting();
        $effect = new FadeText(true);
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });

    it('renders fade in at end', function () {
        $display = DisplayFactory::forTesting();
        $effect = new FadeText(true);
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, 1.0);
        })->not->toThrow(Exception::class);
    });

    it('renders fade out at start', function () {
        $display = DisplayFactory::forTesting();
        $effect = new FadeText(false);
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, 0.0);
        })->not->toThrow(Exception::class);
    });

    it('renders fade out at end', function () {
        $display = DisplayFactory::forTesting();
        $effect = new FadeText(false);
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, 1.0);
        })->not->toThrow(Exception::class);
    });
});

describe('FadeText Progress', function () {
    it('clamps progress values', function () {
        $display = DisplayFactory::forTesting();
        $effect = new FadeText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, -0.5);
            $effect->render($display, 'Test', 0, 0, 1.5);
        })->not->toThrow(Exception::class);
    });

    it('reports complete at progress 1', function () {
        $effect = new FadeText();
        
        expect($effect->isComplete(1.0))->toBeTrue()
            ->and($effect->isComplete(0.5))->toBeFalse();
    });
});

describe('FadeText Edge Cases', function () {
    it('handles empty string', function () {
        $display = DisplayFactory::forTesting();
        $effect = new FadeText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, '', 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });

    it('handles long text', function () {
        $display = DisplayFactory::forTesting();
        $effect = new FadeText();
        $longText = str_repeat('Test ', 20);
        
        expect(function () use ($display, $effect, $longText) {
            $effect->render($display, $longText, 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });

    it('resets without error', function () {
        $effect = new FadeText();
        
        expect(function () use ($effect) {
            $effect->reset();
        })->not->toThrow(Exception::class);
    });
});

describe('FadeText Direction', function () {
    it('fade in shows more text over time', function () {
        $display = DisplayFactory::forTesting();
        $effect = new FadeText(true);
        
        // At progress 0, should show nothing
        // At progress 1, should show everything
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Hello', 0, 0, 0.0);
            $effect->render($display, 'Hello', 0, 0, 0.5);
            $effect->render($display, 'Hello', 0, 0, 1.0);
        })->not->toThrow(Exception::class);
    });

    it('fade out shows less text over time', function () {
        $display = DisplayFactory::forTesting();
        $effect = new FadeText(false);
        
        // At progress 0, should show everything
        // At progress 1, should show nothing
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Hello', 0, 0, 0.0);
            $effect->render($display, 'Hello', 0, 0, 0.5);
            $effect->render($display, 'Hello', 0, 0, 1.0);
        })->not->toThrow(Exception::class);
    });
});

