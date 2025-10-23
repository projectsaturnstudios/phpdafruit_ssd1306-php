<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\StateMachine\DisplayState;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

// Test concrete state for testing abstract class
class TestState extends DisplayState {
    public bool $enterCalled = false;
    public bool $updateCalled = false;
    public bool $exitCalled = false;
    public bool $renderCalled = false;
    public array $enterContext = [];
    public float $lastDeltaTime = 0;

    public function enter(array $context = []): void {
        $this->enterCalled = true;
        $this->enterContext = $context;
    }

    public function update(float $deltaTime): void {
        $this->updateCalled = true;
        $this->lastDeltaTime = $deltaTime;
    }

    public function exit(): array {
        $this->exitCalled = true;
        return ['test_data' => 'exit_value'];
    }

    public function render(): void {
        $this->renderCalled = true;
    }
}

describe('DisplayState Lifecycle', function () {
    it('calls enter method', function () {
        $display = DisplayFactory::forTesting();
        $state = new TestState($display);
        
        $state->enter(['key' => 'value']);
        
        expect($state->enterCalled)->toBeTrue()
            ->and($state->enterContext)->toBe(['key' => 'value']);
    });

    it('calls update method', function () {
        $display = DisplayFactory::forTesting();
        $state = new TestState($display);
        
        $state->update(0.016);
        
        expect($state->updateCalled)->toBeTrue()
            ->and($state->lastDeltaTime)->toBe(0.016);
    });

    it('calls exit method and returns data', function () {
        $display = DisplayFactory::forTesting();
        $state = new TestState($display);
        
        $data = $state->exit();
        
        expect($state->exitCalled)->toBeTrue()
            ->and($data)->toBe(['test_data' => 'exit_value']);
    });

    it('calls render method', function () {
        $display = DisplayFactory::forTesting();
        $state = new TestState($display);
        
        $state->render();
        
        expect($state->renderCalled)->toBeTrue();
    });
});

describe('DisplayState Data Management', function () {
    it('sets and gets data', function () {
        $display = DisplayFactory::forTesting();
        $state = new TestState($display);
        
        $state->setData('key', 'value');
        
        expect($state->getData('key'))->toBe('value');
    });

    it('returns default value for missing key', function () {
        $display = DisplayFactory::forTesting();
        $state = new TestState($display);
        
        $value = $state->getData('missing', 'default');
        
        expect($value)->toBe('default');
    });

    it('checks if data key exists', function () {
        $display = DisplayFactory::forTesting();
        $state = new TestState($display);
        
        $state->setData('exists', 'value');
        
        expect($state->hasData('exists'))->toBeTrue()
            ->and($state->hasData('missing'))->toBeFalse();
    });

    it('gets all data', function () {
        $display = DisplayFactory::forTesting();
        $state = new TestState($display);
        
        $state->setData('key1', 'value1')
              ->setData('key2', 'value2');
        
        $all = $state->getAllData();
        
        expect($all)->toBe(['key1' => 'value1', 'key2' => 'value2']);
    });

    it('clears all data', function () {
        $display = DisplayFactory::forTesting();
        $state = new TestState($display);
        
        $state->setData('key', 'value')->clearData();
        
        expect($state->getAllData())->toBe([]);
    });

    it('chains setData calls', function () {
        $display = DisplayFactory::forTesting();
        $state = new TestState($display);
        
        $result = $state->setData('key1', 'value1')
                       ->setData('key2', 'value2');
        
        expect($result)->toBe($state)
            ->and($state->getData('key1'))->toBe('value1')
            ->and($state->getData('key2'))->toBe('value2');
    });
});

describe('DisplayState Name', function () {
    it('gets state name from class', function () {
        $display = DisplayFactory::forTesting();
        $state = new TestState($display);
        
        $name = $state->getName();
        
        expect($name)->toBe('TestState');
    });
});

