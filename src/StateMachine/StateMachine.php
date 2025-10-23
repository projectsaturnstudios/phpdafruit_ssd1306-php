<?php

declare(strict_types=1);

namespace PhpdaFruit\SSD1306\StateMachine;

use PhpdaFruit\SSD1306\SSD1306Display;

/**
 * State machine for managing display states and transitions
 * 
 * Coordinates state lifecycle, transitions, and rendering.
 */
class StateMachine
{
    private array $states = [];
    private ?DisplayState $currentState = null;
    private ?DisplayState $previousState = null;
    private ?Transition $activeTransition = null;
    private float $lastUpdateTime = 0;

    public function __construct(
        private SSD1306Display $display
    ) {
        $this->lastUpdateTime = microtime(true);
    }

    /**
     * Register a state with the machine
     *
     * @param string $name State identifier
     * @param DisplayState $state State instance
     * @return self
     */
    public function addState(string $name, DisplayState $state): self
    {
        $this->states[$name] = $state;
        
        // Set first added state as initial state
        if ($this->currentState === null) {
            $this->currentState = $state;
            $state->enter([]);
        }
        
        return $this;
    }

    /**
     * Transition to a new state
     *
     * @param string $name Target state name
     * @param Transition|null $transition Optional transition effect
     * @param array $context Data to pass to new state
     * @return bool Success
     */
    public function transition(string $name, ?Transition $transition = null, array $context = []): bool
    {
        if (!isset($this->states[$name])) {
            return false; // State not found
        }

        $newState = $this->states[$name];
        
        if ($newState === $this->currentState) {
            return true; // Already in target state
        }

        // Exit current state
        if ($this->currentState) {
            $exitData = $this->currentState->exit();
            $context = array_merge($exitData, $context);
        }

        // Store previous state
        $this->previousState = $this->currentState;
        
        // Start transition if provided
        if ($transition) {
            $this->activeTransition = $transition;
            $this->activeTransition->start();
        }
        
        // Enter new state
        $this->currentState = $newState;
        $this->currentState->enter($context);
        
        return true;
    }

    /**
     * Update current state and transitions
     *
     * @return void
     */
    public function update(): void
    {
        $currentTime = microtime(true);
        $deltaTime = $currentTime - $this->lastUpdateTime;
        $this->lastUpdateTime = $currentTime;

        // Update current state
        if ($this->currentState) {
            $this->currentState->update((float)$deltaTime);
        }

        // Complete transition if active and finished
        if ($this->activeTransition && $this->activeTransition->isComplete()) {
            $this->activeTransition = null;
            $this->previousState = null;
        }
    }

    /**
     * Render current state with optional transition
     *
     * @return void
     */
    public function render(): void
    {
        $this->display->clearDisplay();

        if ($this->activeTransition && $this->previousState && $this->currentState) {
            // Render transition between states
            $this->activeTransition->render($this->display, $this->previousState, $this->currentState);
        } elseif ($this->currentState) {
            // Render current state
            $this->currentState->render();
        }

        $this->display->display();
    }

    /**
     * Get current state
     *
     * @return DisplayState|null
     */
    public function getCurrentState(): ?DisplayState
    {
        return $this->currentState;
    }

    /**
     * Get current state name
     *
     * @return string|null
     */
    public function getCurrentStateName(): ?string
    {
        if (!$this->currentState) {
            return null;
        }

        // Find state name by instance
        foreach ($this->states as $name => $state) {
            if ($state === $this->currentState) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Check if a transition is active
     *
     * @return bool
     */
    public function isTransitioning(): bool
    {
        return $this->activeTransition !== null;
    }

    /**
     * Get active transition
     *
     * @return Transition|null
     */
    public function getActiveTransition(): ?Transition
    {
        return $this->activeTransition;
    }

    /**
     * Check if state exists
     *
     * @param string $name State name
     * @return bool
     */
    public function hasState(string $name): bool
    {
        return isset($this->states[$name]);
    }

    /**
     * Get state by name
     *
     * @param string $name State name
     * @return DisplayState|null
     */
    public function getState(string $name): ?DisplayState
    {
        return $this->states[$name] ?? null;
    }

    /**
     * Get all registered states
     *
     * @return array<string, DisplayState>
     */
    public function getAllStates(): array
    {
        return $this->states;
    }

    /**
     * Get all state names
     *
     * @return array<string>
     */
    public function getStateNames(): array
    {
        return array_keys($this->states);
    }

    /**
     * Remove a state
     *
     * @param string $name State name
     * @return bool Success
     */
    public function removeState(string $name): bool
    {
        if (!isset($this->states[$name])) {
            return false;
        }

        // Don't remove current state
        if ($this->states[$name] === $this->currentState) {
            return false;
        }

        unset($this->states[$name]);
        return true;
    }

    /**
     * Run the state machine (update + render loop)
     *
     * @param int $iterations Number of iterations to run (0 = infinite)
     * @param int $fps Target frames per second
     * @return void
     */
    public function run(int $iterations = 0, int $fps = 30): void
    {
        $frameTime = 1.0 / $fps;
        $iteration = 0;

        while ($iterations === 0 || $iteration < $iterations) {
            $frameStart = microtime(true);

            $this->update();
            $this->render();

            // Sleep to maintain target FPS
            $elapsed = microtime(true) - $frameStart;
            $sleepTime = $frameTime - $elapsed;
            
            if ($sleepTime > 0) {
                usleep((int)($sleepTime * 1000000));
            }

            $iteration++;
        }
    }
}

