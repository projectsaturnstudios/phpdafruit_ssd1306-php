<?php

declare(strict_types=1);

// Direct extension hello world (no intermediate class)
if (!extension_loaded('ssd1306')) {
    fwrite(STDERR, "ssd1306 extension not loaded\n");
    exit(1);
}

// Yahboom Cube uses I2C bus 7, address 0x3C, 128x32, SWITCHCAPVCC=2
ssd1306_begin(7, 0x3C, 128, 32, 2);
ssd1306_clear_display();

// Big HELLO at top-left
ssd1306_set_text_size(1);
ssd1306_set_text_color(1); // white
ssd1306_set_cursor(0, 0);
ssd1306_print('HELLO PHP');
ssd1306_display();

// Draw a solid bar at the bottom for visibility
for ($x = 0; $x < 128; $x++) {
    for ($y = 24; $y < 32; $y++) {
        ssd1306_draw_pixel($x, $y, 1);
    }
}
ssd1306_display();

// Keep visible for a bit
sleep(8);



