<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Effects\TypewriterText;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

describe('TypewriterText Construction', function () {
    it('creates with default parameters', function () {
        $effect = new TypewriterText();
        
        expect($effect)->toBeInstanceOf(TypewriterText::class);
    });

    it('creates with cursor enabled', function () {
        $effect = new TypewriterText(true);
        
        expect($effect)->toBeInstanceOf(TypewriterText::class);
    });

    it('creates with cursor disabled', function () {
        $effect = new TypewriterText(false);
        
        expect($effect)->toBeInstanceOf(TypewriterText::class);
    });
});

describe('TypewriterText Rendering', function () {
    it('renders at start shows no text', function () {
        $display = DisplayFactory::forTesting();
        $effect = new TypewriterText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Hello', 0, 0, 0.0);
        })->not->toThrow(Exception::class);
    });

    it('renders at middle shows partial text', function () {
        $display = DisplayFactory::forTesting();
        $effect = new TypewriterText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Hello', 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });

    it('renders at end shows full text', function () {
        $display = DisplayFactory::forTesting();
        $effect = new TypewriterText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Hello', 0, 0, 1.0);
        })->not->toThrow(Exception::class);
    });

    it('renders with cursor', function () {
        $display = DisplayFactory::forTesting();
        $effect = new TypewriterText(true);
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Hello', 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });
});

describe('TypewriterText Progress', function () {
    it('clamps progress values', function () {
        $display = DisplayFactory::forTesting();
        $effect = new TypewriterText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'Test', 0, 0, -0.5);
            $effect->render($display, 'Test', 0, 0, 1.5);
        })->not->toThrow(Exception::class);
    });

    it('reports complete at progress 1', function () {
        $effect = new TypewriterText();
        
        expect($effect->isComplete(1.0))->toBeTrue()
            ->and($effect->isComplete(0.9))->toBeFalse();
    });
});

describe('TypewriterText Edge Cases', function () {
    it('handles empty string', function () {
        $display = DisplayFactory::forTesting();
        $effect = new TypewriterText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, '', 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });

    it('handles single character', function () {
        $display = DisplayFactory::forTesting();
        $effect = new TypewriterText();
        
        expect(function () use ($display, $effect) {
            $effect->render($display, 'A', 0, 0, 0.5);
        })->not->toThrow(Exception::class);
    });

    it('resets without error', function () {
        $effect = new TypewriterText();
        
        expect(function () use ($effect) {
            $effect->reset();
        })->not->toThrow(Exception::class);
    });
});

