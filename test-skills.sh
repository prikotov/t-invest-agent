#!/bin/bash
# Тестирование skills

set -e
cd "$(dirname "$0")"

echo "=== Portfolio ==="
./vendor/bin/t-invest portfolio:show

echo ""
echo "=== MOEX Trade Data ==="
./vendor/bin/moex security:trade-data SBER

echo ""
echo "=== News ==="
./vendor/bin/news news:fetch --ticker SBER

echo ""
echo "=== Done ==="
