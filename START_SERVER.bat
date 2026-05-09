@echo off
title ObR Tracker - Real-Time Server
echo ==========================================
echo Starting ObR Tracker Real-Time Server...
echo Broadcasting to all PCs on the network.
echo ==========================================
php artisan reverb:start --host=0.0.0.0 --port=8080
pause