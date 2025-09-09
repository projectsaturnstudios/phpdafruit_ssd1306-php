<?php

declare(strict_types=1);

// Local package usage without composer: include class directly
require_once __DIR__ . '/../src/SSD1306.php';

use ProjectSaturnStudios\SSD1306\SSD1306;

// Simple, direct system stats example using the SSD1306-PHP package API (which uses the C extension)

function readCpuTimes(): ?array
{
    $line = @file_get_contents('/proc/stat');
    if ($line === false) return null;
    $first = strtok($line, "\n");
    if (!$first || strpos($first, 'cpu ') !== 0) return null;
    $parts = preg_split('/\s+/', trim($first));
    // user nice system idle iowait irq softirq steal guest guest_nice
    $vals = array_map('intval', array_slice($parts, 1, 10));
    $idleAll = ($vals[3] ?? 0) + ($vals[4] ?? 0);
    $total = array_sum($vals);
    return [$total, $idleAll];
}

function getCpuUsagePercent(?array &$prev): int
{
    $now = readCpuTimes();
    if ($now === null) return 0;
    if ($prev === null) {
        $prev = $now;
        usleep(100_000);
        $now = readCpuTimes() ?? $now;
    }
    [$tNow, $iNow] = $now;
    [$tPrev, $iPrev] = $prev;
    $dt = $tNow - $tPrev;
    $di = $iNow - $iPrev;
    $prev = $now;
    if ($dt <= 0) return 0;
    $pct = (int)round((($dt - $di) * 100.0) / $dt);
    return max(0, min(100, $pct));
}

function getCpuTempC(): ?int
{
    // Try common thermal paths first
    $candidates = [
        '/sys/class/thermal/thermal_zone0/temp',
        '/sys/class/thermal/thermal_zone1/temp',
    ];
    foreach ($candidates as $f) {
        if (is_file($f)) {
            $raw = @file_get_contents($f);
            if ($raw !== false) {
                $m = (int)trim($raw);
                return (int)round($m / 1000.0);
            }
        }
    }
    // Fallback scan under /sys/devices/virtual/thermal
    $base = '/sys/devices/virtual/thermal';
    if (is_dir($base)) {
        foreach (@scandir($base) ?: [] as $name) {
            if (strpos($name, 'thermal_zone') !== 0) continue;
            $tempFile = $base . '/' . $name . '/temp';
            if (is_file($tempFile)) {
                $raw = @file_get_contents($tempFile);
                if ($raw !== false) {
                    $m = (int)trim($raw);
                    return (int)round($m / 1000.0);
                }
            }
        }
    }
    return null;
}

function getRamStats(): array
{
    $meminfo = @file_get_contents('/proc/meminfo');
    if ($meminfo === false) return [0, 0.0];
    $map = [];
    foreach (explode("\n", $meminfo) as $line) {
        if (preg_match('/^(\w+):\s*(\d+)\s*kB/', $line, $m)) {
            $map[$m[1]] = (int)$m[2];
        }
    }
    $totalKb = $map['MemTotal'] ?? 0;
    $availKb = $map['MemAvailable'] ?? ($map['MemFree'] ?? 0);
    $usedKb = max(0, $totalKb - $availKb);
    $pct = $totalKb > 0 ? (int)round($usedKb * 100 / $totalKb) : 0;
    $gb = round($totalKb / 1024 / 1024, 1);
    return [$pct, $gb];
}

function getDiskStats(): array
{
    $out = @shell_exec("df -h / | tail -1");
    if (!$out) return [0, 0.0];
    $parts = preg_split('/\s+/', trim($out));
    if (count($parts) < 5) return [0, 0.0];
    $usedPct = (int)str_replace('%', '', $parts[4]);
    $size = $parts[1];
    $gb = 0.0;
    if (preg_match('/^(\d+(?:\.\d+)?)([KMGT]?)/', $size, $m)) {
        $val = (float)$m[1];
        $unit = $m[2] ?? '';
        $gb = match ($unit) {
            'T' => $val * 1024,
            'G' => $val,
            'M' => $val / 1024,
            'K' => $val / 1024 / 1024,
            default => $val / 1024 / 1024 / 1024,
        };
    }
    return [$usedPct, round($gb, 1)];
}

// Initialize display (SWITCHCAPVCC=2 is set inside SSD1306::begin())
$d = new SSD1306(width: 128, height: 32, i2cBus: 7, i2cAddress: 0x3C, debug: true);
if (!$d->begin()) {
    fwrite(STDERR, "Failed to initialize OLED\n");
    exit(1);
}

// Splash
$d->clear();
$d->text('CPU: --%', 0, 0);
$d->text('Temp: --C', 0, 8);
$d->text('RAM: --% -> --GB', 0, 16);
$d->text('Disk: --% -> --GB', 0, 24);
$d->display();
sleep(2);

$prev = null;
$running = true;
if (function_exists('pcntl_signal')) {
    pcntl_signal(SIGINT, function () use (&$running) { $running = false; });
}

while ($running) {
    $cpu = getCpuUsagePercent($prev);
    $temp = getCpuTempC();
    [$ramPct, $ramTotal] = getRamStats();
    [$diskPct, $diskTotal] = getDiskStats();

    $d->clear();
    $d->text(sprintf('CPU: %d%%', $cpu), 0, 0);
    $d->text('Temp: ' . ($temp !== null ? $temp . 'C' : 'NA'), 0, 8);
    $d->text(sprintf('RAM: %d%% -> %.1fGB', $ramPct, $ramTotal), 0, 16);
    $d->text(sprintf('Disk: %d%% -> %.1fGB', $diskPct, $diskTotal), 0, 24);
    $d->display();

    if (function_exists('pcntl_signal_dispatch')) {
        pcntl_signal_dispatch();
    }
    sleep(2);
}


