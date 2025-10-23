<?php
declare(strict_types=1);
use PhpdaFruit\SSD1306\UI\Notification;
use PhpdaFruit\SSD1306\Builder\DisplayFactory;

describe('Notification Creation', function () {
    it('creates notification', function () {
        $display = DisplayFactory::forTesting();
        $notif = new Notification($display, 'Test message');
        
        expect($notif)->toBeInstanceOf(Notification::class)
            ->and($notif->getMessage())->toBe('Test message');
    });

    it('creates info notification', function () {
        $display = DisplayFactory::forTesting();
        $notif = Notification::info($display, 'Info');
        
        expect($notif->getPriority())->toBe(Notification::PRIORITY_INFO);
    });

    it('creates warning notification', function () {
        $display = DisplayFactory::forTesting();
        $notif = Notification::warning($display, 'Warning');
        
        expect($notif->getPriority())->toBe(Notification::PRIORITY_WARNING);
    });

    it('creates error notification', function () {
        $display = DisplayFactory::forTesting();
        $notif = Notification::error($display, 'Error');
        
        expect($notif->getPriority())->toBe(Notification::PRIORITY_ERROR);
    });
});

describe('Notification State', function () {
    it('starts inactive', function () {
        $display = DisplayFactory::forTesting();
        $notif = new Notification($display, 'Test');
        
        expect($notif->isActive())->toBeFalse();
    });

    it('activates when shown', function () {
        $display = DisplayFactory::forTesting();
        $notif = new Notification($display, 'Test');
        
        $notif->show();
        
        expect($notif->isActive())->toBeTrue();
    });

    it('deactivates when dismissed', function () {
        $display = DisplayFactory::forTesting();
        $notif = new Notification($display, 'Test');
        
        $notif->show();
        $notif->dismiss();
        
        expect($notif->isActive())->toBeFalse();
    });
});

