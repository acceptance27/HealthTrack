@echo off
REM HealthTrack setup window. Double-click this file.
REM
REM -STA is required by WinForms; -ExecutionPolicy Bypass avoids having to
REM change the machine's script policy just to run this one file.

powershell.exe -NoProfile -ExecutionPolicy Bypass -STA -File "%~dp0tools\setup\gui.ps1"

if errorlevel 1 (
    echo.
    echo The setup window closed with an error. The message above should say why.
    pause
)
