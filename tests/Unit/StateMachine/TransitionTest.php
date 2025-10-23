<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\StateMachine\Transition;

describe('Transition Construction', function () {
    it('creates with default effect', function () {
        $transition = new Transition();
        
        expect($transition->getEffect())->toBe(Transition::EFFECT_NONE)
            ->and($transition->getDuration())->toBe(0.3);
    });

    it('creates with custom effect and duration', function () {
        $transition = new Transition(Transition::EFFECT_FADE, 0.5);
        
        expect($transition->getEffect())->toBe(Transition::EFFECT_FADE)
            ->and($transition->getDuration())->toBe(0.5);
    });
});

describe('Transition Progress', function () {
    it('starts at zero progress', function () {
        $transition = new Transition();
        
        expect($transition->getProgress())->toBe(0.0)
            ->and($transition->isComplete())->toBeFalse();
    });

    it('tracks progress after start', function () {
        $transition = new Transition(Transition::EFFECT_FADE, 0.1);
        
        $transition->start();
        usleep(60000); // 60ms
        
        $progress = $transition->getProgress();
        
        expect($progress)->toBeGreaterThan(0.0)
            ->and($progress)->toBeLessThanOrEqual(1.0);
    });

    it('completes when progress reaches 1.0', function () {
        $transition = new Transition(Transition::EFFECT_FADE, 0.05);
        
        $transition->start();
        usleep(60000); // 60ms - should be complete
        
        expect($transition->isComplete())->toBeTrue();
    });

    it('resets to initial state', function () {
        $transition = new Transition();
        
        $transition->start();
        usleep(10000);
        $transition->reset();
        
        expect($transition->getProgress())->toBe(0.0)
            ->and($transition->isComplete())->toBeFalse();
    });
});

describe('Transition Static Factories', function () {
    it('creates fade transition', function () {
        $transition = Transition::fade(0.4);
        
        expect($transition->getEffect())->toBe(Transition::EFFECT_FADE)
            ->and($transition->getDuration())->toBe(0.4);
    });

    it('creates slide left transition', function () {
        $transition = Transition::slide('left', 0.5);
        
        expect($transition->getEffect())->toBe(Transition::EFFECT_SLIDE_LEFT)
            ->and($transition->getDuration())->toBe(0.5);
    });

    it('creates slide right transition', function () {
        $transition = Transition::slide('right', 0.3);
        
        expect($transition->getEffect())->toBe(Transition::EFFECT_SLIDE_RIGHT);
    });

    it('creates slide up transition', function () {
        $transition = Transition::slide('up', 0.3);
        
        expect($transition->getEffect())->toBe(Transition::EFFECT_SLIDE_UP);
    });

    it('creates slide down transition', function () {
        $transition = Transition::slide('down', 0.3);
        
        expect($transition->getEffect())->toBe(Transition::EFFECT_SLIDE_DOWN);
    });

    it('creates wipe transition', function () {
        $transition = Transition::wipe('left', 0.6);
        
        expect($transition->getEffect())->toBe(Transition::EFFECT_WIPE_LEFT)
            ->and($transition->getDuration())->toBe(0.6);
    });

    it('creates instant transition', function () {
        $transition = Transition::instant();
        
        expect($transition->getEffect())->toBe(Transition::EFFECT_NONE)
            ->and($transition->getDuration())->toBe(0.0);
    });
});

describe('Transition Effect Types', function () {
    it('supports all effect constants', function () {
        expect(Transition::EFFECT_NONE)->toBeString()
            ->and(Transition::EFFECT_FADE)->toBeString()
            ->and(Transition::EFFECT_SLIDE_LEFT)->toBeString()
            ->and(Transition::EFFECT_SLIDE_RIGHT)->toBeString()
            ->and(Transition::EFFECT_SLIDE_UP)->toBeString()
            ->and(Transition::EFFECT_SLIDE_DOWN)->toBeString()
            ->and(Transition::EFFECT_WIPE_LEFT)->toBeString()
            ->and(Transition::EFFECT_WIPE_RIGHT)->toBeString();
    });
});

