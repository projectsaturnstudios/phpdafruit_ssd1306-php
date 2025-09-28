#!/usr/bin/env bash
set -euo pipefail

ACTION="none" # none|clear|off
if [[ ${1:-} == "--clear" ]]; then ACTION="clear"; fi
if [[ ${1:-} == "--off" ]]; then ACTION="off"; fi

echo "[kill_oled] Stopping OLED-related processes..."
patterns=(
  "/packages/SSD1306-PHP/examples/system_stats.php"
  "/packages/SSD1306-PHP/examples/starfield.php"
  "/packages/SSD1306-PHP/examples/hello_extension.php"
  "/packages/CubeNanoLib/examples/system_stats.php"
  "/packages/CubeNanoLib/examples/starfield_demo.php"
  "/packages/CubeNanoLib/examples/hello_oled.php"
  "oled_persist.py"
  "oled_starfield.py"
)

for pat in "${patterns[@]}"; do
  if pgrep -f "$pat" >/dev/null 2>&1; then
    echo " - killing: $pat"
    pkill -f "$pat" || true
  fi
done

sleep 0.5

echo "[kill_oled] Removing PID files..."
pidfiles=(/tmp/oled_system_stats.pid /tmp/oled_starfield.pid /tmp/oled_persist.pid /tmp/ssd1306_hello.pid)
for pf in "${pidfiles[@]}"; do
  [[ -f "$pf" ]] && rm -f "$pf"
done

case "$ACTION" in
  clear)
    echo "[kill_oled] Clearing display (leaves panel on)"
    php -r 'if(!extension_loaded("ssd1306")) exit(0); ssd1306_begin(7,0x3C,128,32,2); ssd1306_clear_display(); ssd1306_display();' || true
    ;;
  off)
    echo "[kill_oled] Turning display off"
    php -r 'if(!extension_loaded("ssd1306")) exit(0); ssd1306_begin(7,0x3C,128,32,2); ssd1306_end();' || true
    ;;
  *) ;;
esac

echo "[kill_oled] Done."















