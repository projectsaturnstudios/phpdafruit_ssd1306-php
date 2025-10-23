<?php

declare(strict_types=1);

use PhpdaFruit\SSD1306\StateMachine\StateMachine;
use PhpdaFruit\SSD1306\StateMachine\Transition;
use PhpdaFruit\SSD1306\StateMachine\States\IdleState;
use PhpdaFruit\SSD1306\StateMachine\States\AlertState;
use PhpdaFruit\SSD1306\StateMachine\States\DashboardState;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

describe('StateMachine Basic Integration', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(1000000); // 1s pause to see output
        }
    });

    it('renders idle state', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $machine = new StateMachine($display);
        
        $machine->addState('idle', new IdleState($display));
        
        // Render for 2 seconds to show pulsing
        for ($i = 0; $i < 20; $i++) {
            $machine->update();
            $machine->render();
            usleep(100000); // 100ms per frame
        }

        expect($machine->getCurrentStateName())->toBe('idle');
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders alert state', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $machine = new StateMachine($display);
        
        $machine->addState('alert', new AlertState($display));
        
        // Render for 2 seconds to show blinking
        for ($i = 0; $i < 20; $i++) {
            $machine->update();
            $machine->render();
            usleep(100000); // 100ms per frame
        }

        expect($machine->getCurrentStateName())->toBe('alert');
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('renders dashboard state', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $machine = new StateMachine($display);
        
        $machine->addState('dashboard', new DashboardState($display));
        
        // Render for 2 seconds to show animated values
        for ($i = 0; $i < 20; $i++) {
            $machine->update();
            $machine->render();
            usleep(100000); // 100ms per frame
        }

        expect($machine->getCurrentStateName())->toBe('dashboard');
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('StateMachine State Transitions', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(500000); // 0.5s pause
        }
    });

    it('transitions from idle to alert instantly', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $machine = new StateMachine($display);
        
        $machine->addState('idle', new IdleState($display))
                ->addState('alert', new AlertState($display));
        
        // Show idle for 1s
        for ($i = 0; $i < 10; $i++) {
            $machine->update();
            $machine->render();
            usleep(100000);
        }
        
        // Instant transition to alert
        $machine->transition('alert');
        
        // Show alert for 1s
        for ($i = 0; $i < 10; $i++) {
            $machine->update();
            $machine->render();
            usleep(100000);
        }

        expect($machine->getCurrentStateName())->toBe('alert');
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('transitions with fade effect', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $machine = new StateMachine($display);
        
        $machine->addState('idle', new IdleState($display))
                ->addState('dashboard', new DashboardState($display));
        
        // Show idle
        for ($i = 0; $i < 5; $i++) {
            $machine->update();
            $machine->render();
            usleep(100000);
        }
        
        // Fade transition to dashboard
        $machine->transition('dashboard', Transition::fade(0.5));
        
        // Render during transition and after
        for ($i = 0; $i < 15; $i++) {
            $machine->update();
            $machine->render();
            usleep(100000);
        }

        expect($machine->getCurrentStateName())->toBe('dashboard');
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('transitions with wipe effect', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $machine = new StateMachine($display);
        
        $machine->addState('dashboard', new DashboardState($display))
                ->addState('alert', new AlertState($display));
        
        // Show dashboard
        for ($i = 0; $i < 5; $i++) {
            $machine->update();
            $machine->render();
            usleep(100000);
        }
        
        // Wipe transition to alert
        $machine->transition('alert', Transition::wipe('left', 0.4));
        
        // Render during transition and after
        for ($i = 0; $i < 15; $i++) {
            $machine->update();
            $machine->render();
            usleep(100000);
        }

        expect($machine->getCurrentStateName())->toBe('alert');
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('StateMachine Multi-State Flow', function () {
    afterEach(function () {
        if (file_exists('/dev/i2c-7')) {
            usleep(500000); // 0.5s pause
        }
    });

    it('cycles through all states', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $machine = new StateMachine($display);
        
        $machine->addState('idle', new IdleState($display))
                ->addState('alert', new AlertState($display))
                ->addState('dashboard', new DashboardState($display));
        
        // Idle → Alert → Dashboard → Idle
        $states = ['alert', 'dashboard', 'idle'];
        
        foreach ($states as $stateName) {
            // Show current state briefly
            for ($i = 0; $i < 5; $i++) {
                $machine->update();
                $machine->render();
                usleep(80000);
            }
            
            // Transition to next
            $machine->transition($stateName, Transition::fade(0.3));
            
            // Show transition
            for ($i = 0; $i < 8; $i++) {
                $machine->update();
                $machine->render();
                usleep(80000);
            }
        }

        expect($machine->getCurrentStateName())->toBe('idle');
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('passes context between states', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $machine = new StateMachine($display);
        
        $machine->addState('idle', new IdleState($display))
                ->addState('alert', new AlertState($display));
        
        // Show idle
        for ($i = 0; $i < 5; $i++) {
            $machine->update();
            $machine->render();
            usleep(100000);
        }
        
        // Transition with custom message
        $machine->transition('alert', null, ['message' => 'CUSTOM!']);
        
        // Show alert with custom message
        for ($i = 0; $i < 10; $i++) {
            $machine->update();
            $machine->render();
            usleep(100000);
        }

        expect($machine->getCurrentStateName())->toBe('alert');
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

describe('StateMachine Run Loop', function () {
    it('runs for fixed iterations', function () {
        if (!file_exists('/dev/i2c-7')) {
            $this->markTestSkipped('I2C device not available');
        }

        $display = DisplayFactory::standard('/dev/i2c-7');
        $machine = new StateMachine($display);
        
        $machine->addState('idle', new IdleState($display));
        
        // Run for 30 frames at 30 fps (1 second)
        $machine->run(30, 30);

        expect($machine->getCurrentStateName())->toBe('idle');
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

