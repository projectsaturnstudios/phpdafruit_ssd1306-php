<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\StateMachine\StateMachine;
use PhpdaFruit\SSD1306\StateMachine\DisplayState;
use PhpdaFruit\SSD1306\StateMachine\Transition;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

// Test states
class TestStateA extends DisplayState {
    public bool $entered = false;
    public bool $exited = false;

    public function enter(array $context = []): void {
        $this->entered = true;
    }

    public function update(float $deltaTime): void {}

    public function exit(): array {
        $this->exited = true;
        return ['from' => 'A'];
    }

    public function render(): void {}
}

class TestStateB extends DisplayState {
    public array $enterContext = [];

    public function enter(array $context = []): void {
        $this->enterContext = $context;
    }

    public function update(float $deltaTime): void {}

    public function exit(): array {
        return [];
    }

    public function render(): void {}
}

describe('StateMachine Construction', function () {
    it('creates with display instance', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        
        expect($machine)->toBeInstanceOf(StateMachine::class)
            ->and($machine->getCurrentState())->toBeNull();
    });
});

describe('StateMachine State Registration', function () {
    it('adds a state', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        $stateA = new TestStateA($display);
        
        $machine->addState('a', $stateA);
        
        expect($machine->hasState('a'))->toBeTrue()
            ->and($machine->getState('a'))->toBe($stateA);
    });

    it('sets first state as current', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        $stateA = new TestStateA($display);
        
        $machine->addState('a', $stateA);
        
        expect($machine->getCurrentState())->toBe($stateA)
            ->and($stateA->entered)->toBeTrue();
    });

    it('gets all state names', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        
        $machine->addState('a', new TestStateA($display))
                ->addState('b', new TestStateB($display));
        
        $names = $machine->getStateNames();
        
        expect($names)->toBe(['a', 'b']);
    });

    it('chains addState calls', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        
        $result = $machine->addState('a', new TestStateA($display))
                          ->addState('b', new TestStateB($display));
        
        expect($result)->toBe($machine);
    });
});

describe('StateMachine State Transitions', function () {
    it('transitions to new state', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        $stateA = new TestStateA($display);
        $stateB = new TestStateB($display);
        
        $machine->addState('a', $stateA)
                ->addState('b', $stateB);
        
        $success = $machine->transition('b');
        
        expect($success)->toBeTrue()
            ->and($machine->getCurrentState())->toBe($stateB)
            ->and($stateA->exited)->toBeTrue();
    });

    it('passes context to new state', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        $stateA = new TestStateA($display);
        $stateB = new TestStateB($display);
        
        $machine->addState('a', $stateA)
                ->addState('b', $stateB);
        
        $machine->transition('b', null, ['custom' => 'data']);
        
        expect($stateB->enterContext)->toHaveKey('custom')
            ->and($stateB->enterContext['custom'])->toBe('data');
    });

    it('merges exit data with context', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        $stateA = new TestStateA($display);
        $stateB = new TestStateB($display);
        
        $machine->addState('a', $stateA)
                ->addState('b', $stateB);
        
        $machine->transition('b');
        
        expect($stateB->enterContext)->toHaveKey('from')
            ->and($stateB->enterContext['from'])->toBe('A');
    });

    it('returns false for non-existent state', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        
        $machine->addState('a', new TestStateA($display));
        
        $success = $machine->transition('non_existent');
        
        expect($success)->toBeFalse();
    });

    it('stays in same state if already there', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        $stateA = new TestStateA($display);
        
        $machine->addState('a', $stateA);
        
        $stateA->entered = false; // Reset flag
        $success = $machine->transition('a');
        
        expect($success)->toBeTrue()
            ->and($stateA->entered)->toBeFalse(); // Not re-entered
    });
});

describe('StateMachine Transition Effects', function () {
    it('tracks active transition', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        $transition = Transition::fade(0.5);
        
        $machine->addState('a', new TestStateA($display))
                ->addState('b', new TestStateB($display));
        
        $machine->transition('b', $transition);
        
        expect($machine->isTransitioning())->toBeTrue()
            ->and($machine->getActiveTransition())->toBe($transition);
    });
});

describe('StateMachine State Management', function () {
    it('gets current state name', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        
        $machine->addState('idle', new TestStateA($display));
        
        expect($machine->getCurrentStateName())->toBe('idle');
    });

    it('removes state', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        
        $machine->addState('a', new TestStateA($display))
                ->addState('b', new TestStateB($display));
        
        $machine->removeState('b');
        
        expect($machine->hasState('b'))->toBeFalse();
    });

    it('cannot remove current state', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        
        $machine->addState('a', new TestStateA($display));
        
        $success = $machine->removeState('a');
        
        expect($success)->toBeFalse()
            ->and($machine->hasState('a'))->toBeTrue();
    });

    it('gets all states', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        $stateA = new TestStateA($display);
        $stateB = new TestStateB($display);
        
        $machine->addState('a', $stateA)
                ->addState('b', $stateB);
        
        $states = $machine->getAllStates();
        
        expect($states)->toBe(['a' => $stateA, 'b' => $stateB]);
    });
});

describe('StateMachine Update', function () {
    it('calls update without error', function () {
        $display = DisplayFactory::forTesting();
        $machine = new StateMachine($display);
        
        $machine->addState('a', new TestStateA($display));
        
        expect(function () use ($machine) {
            $machine->update();
        })->not->toThrow(Exception::class);
    });
});

