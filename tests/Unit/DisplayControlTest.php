<?php

declare(strict_types=1);

describe('Display Control', function () {
    beforeEach(function () {
        $this->display = getSharedDisplay();
        clearTestDisplay();
    });
    
    afterEach(function () {
        if (isDisplayAvailable()) {
            pauseToView(0.5); // Pause to view the output
            clearTestDisplay();
            // Reset to normal state
            $this->display->invertDisplay(false);
            $this->display->dim(false);
            $this->display->setRotation(0);
        }
    });

    it('dims and brightens display', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        // Dim display (0x00 contrast)
        $this->display->dim(true);
        expect(true)->toBeTrue();
        
        // Normal brightness (0xCF contrast)
        $this->display->dim(false);
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('inverts display colors', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        // Invert (0=white, 1=black)
        $this->display->invertDisplay(true);
        expect(true)->toBeTrue();
        
        // Normal (0=black, 1=white)
        $this->display->invertDisplay(false);
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('sends low-level commands', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        // Send single command
        $this->display->ssd1306_command(0x81); // Set contrast command
        $this->display->ssd1306_command(0x7F); // Contrast value
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('sends command list', function () {
        if (!isDisplayAvailable()) {
            $this->markTestSkipped('Display not available');
        }
        
        $commands = [0x81, 0x7F]; // Set contrast
        $this->display->ssd1306_commandList($commands);
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('rotates display', function () {
        $this->display->setRotation(0);
        expect($this->display->getRotation())->toBe(0);
        
        $this->display->setRotation(1);
        expect($this->display->getRotation())->toBe(1);
        
        $this->display->setRotation(2);
        expect($this->display->getRotation())->toBe(2);
        
        $this->display->setRotation(3);
        expect($this->display->getRotation())->toBe(3);
    });
});

describe('Hardware Scrolling', function () {
    beforeEach(function () {
        $this->display = getSharedDisplay();
        if (isDisplayAvailable()) {
            $this->display->stopscroll(); // Stop any previous scrolling
            clearTestDisplay();
            // Draw some vertical lines for scrolling visualization
            for ($x = 10; $x < 120; $x += 20) {
                $this->display->drawFastVLine($x, 0, 32, 1);
            }
            $this->display->display();
        }
    });
    
    afterEach(function () {
        if (isDisplayAvailable()) {
            pauseToView(1.0); // Pause longer to see scrolling effects
            $this->display->stopscroll(); // Always stop scrolling after test
            clearTestDisplay();
        }
    });

    it('scrolls right', function () {
        $this->display->startscrollright(0, 3);
        expect(true)->toBeTrue();
        
        usleep(500000); // 0.5 seconds
        $this->display->stopscroll();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('scrolls left', function () {
        $this->display->startscrollleft(0, 3);
        expect(true)->toBeTrue();
        
        usleep(500000);
        $this->display->stopscroll();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('scrolls diagonally right', function () {
        $this->display->startscrolldiagright(0, 3);
        expect(true)->toBeTrue();
        
        usleep(500000);
        $this->display->stopscroll();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('scrolls diagonally left', function () {
        $this->display->startscrolldiagleft(0, 3);
        expect(true)->toBeTrue();
        
        usleep(500000);
        $this->display->stopscroll();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');

    it('stops scrolling', function () {
        $this->display->startscrollright(0, 3);
        usleep(100000);
        $this->display->stopscroll();
        
        expect(true)->toBeTrue();
    })->skip(fn () => !file_exists('/dev/i2c-7'), 'I2C device not available');
});

