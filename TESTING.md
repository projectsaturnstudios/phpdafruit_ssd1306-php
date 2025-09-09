# SSD1306-PHP Testing Guide

This document explains how to test the SSD1306-PHP library using Pest 4.

## Prerequisites

- PHP 8.1 or higher
- SSD1306 PHP extension installed and loaded
- Pest 4 testing framework (installed via Composer)

## Running Tests

### Run All Tests
```bash
./vendor/bin/pest
```

### Run Specific Test Suites
```bash
# Run only unit tests
./vendor/bin/pest tests/Unit

# Run only feature tests
./vendor/bin/pest tests/Feature
```

### Run Tests with Coverage
```bash
./vendor/bin/pest --coverage
```

### Run Tests in Verbose Mode
```bash
./vendor/bin/pest --verbose
```

## Test Structure

### Unit Tests (`tests/Unit/SSD1306Test.php`)
- **Constructor Tests**: Validate display initialization with various parameters
- **Display Control Tests**: Test begin(), clear(), display(), and end() methods
- **Drawing Function Tests**: Test pixel(), line(), rectangle(), circle(), and text() methods
- **Display Properties Tests**: Test getWidth() and getHeight() methods
- **Advanced Features Tests**: Test invertDisplay(), dim(), setContrast(), and getPixel() methods
- **Scrolling Function Tests**: Test all scrolling methods
- **Integration Tests**: Test complete display cycles and error recovery

### Feature Tests (`tests/Feature/DisplayExampleTest.php`)
- **Hello World Example**: Complete example showing basic functionality
- **Drawing Functions Demo**: Demonstrates all drawing capabilities
- **Error Handling**: Tests graceful error handling
- **6-Method Pattern Compliance**: Validates the core API pattern

## Key Test Features

### Hardware Extension Handling
Tests automatically skip when the SSD1306 extension is not loaded, making them safe to run in CI/development environments without hardware.

### Parameter Validation
Tests verify that invalid parameters throw appropriate exceptions:
- Negative dimensions
- Invalid I2C parameters
- Out-of-range contrast values
- Invalid scroll page numbers

### Boundary Condition Testing
Tests ensure the library handles edge cases gracefully:
- Out-of-bounds pixel operations
- Empty strings
- Negative coordinates

### Integration Testing
Complete workflow tests verify that the library works correctly in real-world scenarios:
- Full display initialization and cleanup cycles
- Rapid updates
- Error recovery

## Expected Test Results

When running on hardware with the SSD1306 extension loaded, all tests should pass. When running without the extension, tests will be skipped with appropriate messages.

## Troubleshooting

### Extension Not Found
If you see "SSD1306 extension not available" messages, ensure:
1. The SSD1306 PHP extension is compiled and installed
2. The extension is loaded in your PHP configuration
3. You have proper permissions to access I2C devices

### I2C Permission Issues
If tests fail with I2C permission errors:
1. Ensure your user is in the `i2c` group
2. Check that I2C devices are accessible: `ls -la /dev/i2c-*`
3. Verify the correct I2C bus number (default is 7 for Yahboom CUBE)

### Hardware Connection Issues
If hardware-related tests fail:
1. Verify SSD1306 display is properly connected
2. Check I2C address (default 0x3C)
3. Test I2C communication: `i2cdetect -y 7`

## Writing Additional Tests

When adding new functionality, follow these patterns:

### Basic Test Structure
```php
it('describes what the test does', function () {
    $display = new SSD1306(128, 32, 7, 0x3C);
    
    // Test setup
    expect($display->begin())->toBeTrue();
    
    // Test the functionality
    expect(fn() => $display->newMethod())->not->toThrow();
    
    // Cleanup
    $display->end();
});
```

### Exception Testing
```php
it('validates parameters correctly', function () {
    $display = new SSD1306();
    
    expect(fn() => $display->methodWithInvalidParam(-1))
        ->toThrow(InvalidArgumentException::class);
});
```

### Hardware Skip Pattern
```php
beforeEach(function () {
    if (!extension_loaded('ssd1306')) {
        $this->markTestSkipped('SSD1306 extension not available');
    }
});
```

This ensures your tests are robust and can run in various environments.

