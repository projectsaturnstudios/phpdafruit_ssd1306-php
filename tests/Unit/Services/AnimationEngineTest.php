<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\Services\AnimationEngine;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

describe('AnimationEngine Construction', function () {
    it('creates engine with display instance', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        expect($engine)->toBeInstanceOf(AnimationEngine::class)
            ->and($engine->getFrameCount())->toBe(0);
    });
});

describe('AnimationEngine Frame Management', function () {
    it('adds frames to sequence', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $engine->addFrame(fn($d, $p) => null, 100);
        
        expect($engine->getFrameCount())->toBe(1);
    });

    it('adds multiple frames', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $engine->addFrame(fn($d, $p) => null, 100)
               ->addFrame(fn($d, $p) => null, 200)
               ->addFrame(fn($d, $p) => null, 150);
        
        expect($engine->getFrameCount())->toBe(3);
    });

    it('clears all frames', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $engine->addFrame(fn($d, $p) => null, 100)
               ->addFrame(fn($d, $p) => null, 200);
        
        expect($engine->getFrameCount())->toBe(2);
        
        $engine->clearFrames();
        
        expect($engine->getFrameCount())->toBe(0);
    });

    it('calculates total duration', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $engine->addFrame(fn($d, $p) => null, 100)
               ->addFrame(fn($d, $p) => null, 200)
               ->addFrame(fn($d, $p) => null, 150);
        
        expect($engine->getTotalDuration())->toBe(450);
    });
});

describe('AnimationEngine Playback Control', function () {
    it('starts not playing', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        expect($engine->isPlaying())->toBeFalse()
            ->and($engine->isPaused())->toBeFalse();
    });

    it('resets to initial state', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $engine->addFrame(fn($d, $p) => null, 100);
        $engine->reset();
        
        expect($engine->getCurrentFrame())->toBe(0)
            ->and($engine->isPlaying())->toBeFalse();
    });

    it('tracks current frame', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $engine->addFrame(fn($d, $p) => null, 100);
        
        expect($engine->getCurrentFrame())->toBe(0);
    });
});

describe('AnimationEngine Loop Control', function () {
    it('enables looping', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $result = $engine->loop(true);
        
        expect($result)->toBe($engine); // Fluent
    });

    it('disables looping', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $engine->loop(true)->loop(false);
        
        expect($engine)->toBeInstanceOf(AnimationEngine::class);
    });
});

describe('AnimationEngine Callbacks', function () {
    it('sets completion callback', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $called = false;
        $result = $engine->onComplete(function() use (&$called) {
            $called = true;
        });
        
        expect($result)->toBe($engine) // Fluent
            ->and($called)->toBeFalse(); // Not called yet
    });
});

describe('AnimationEngine Progress', function () {
    it('returns zero progress initially', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $engine->addFrame(fn($d, $p) => null, 100);
        
        expect($engine->getProgress())->toBe(0.0);
    });

    it('returns 1.0 for empty animation', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        // No frames added, but playing
        expect($engine->getProgress())->toBe(0.0);
    });
});

describe('AnimationEngine Static Factories', function () {
    it('creates fade animation', function () {
        $display = DisplayFactory::forTesting();
        
        $engine = AnimationEngine::fade(
            $display,
            fn($d) => $d->drawPixel(10, 10, 1),
            500,
            true
        );
        
        expect($engine)->toBeInstanceOf(AnimationEngine::class)
            ->and($engine->getFrameCount())->toBeGreaterThan(0);
    });

    it('creates slide animation', function () {
        $display = DisplayFactory::forTesting();
        
        $engine = AnimationEngine::slide(
            $display,
            fn($d) => $d->drawRect(0, 0, 20, 20, 1),
            500,
            'left'
        );
        
        expect($engine)->toBeInstanceOf(AnimationEngine::class)
            ->and($engine->getFrameCount())->toBeGreaterThan(0);
    });
});

describe('AnimationEngine Fluent Interface', function () {
    it('chains methods fluently', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $result = $engine
            ->addFrame(fn($d, $p) => null, 100)
            ->addFrame(fn($d, $p) => null, 200)
            ->loop(true)
            ->onComplete(fn() => null);
        
        expect($result)->toBe($engine)
            ->and($engine->getFrameCount())->toBe(2);
    });
});

describe('AnimationEngine Edge Cases', function () {
    it('handles zero duration frames', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $engine->addFrame(fn($d, $p) => null, 0);
        
        expect($engine->getTotalDuration())->toBe(0);
    });

    it('handles empty frame list', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        expect($engine->getFrameCount())->toBe(0)
            ->and($engine->getTotalDuration())->toBe(0);
    });

    it('clear frames resets state', function () {
        $display = DisplayFactory::forTesting();
        $engine = new AnimationEngine($display);
        
        $engine->addFrame(fn($d, $p) => null, 100);
        $engine->clearFrames();
        
        expect($engine->getCurrentFrame())->toBe(0)
            ->and($engine->isPlaying())->toBeFalse();
    });
});

