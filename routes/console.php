<?php

Symfony::command('inspire', function ($command) {
    return $command->comment(\Mild\Supports\Inspiring::quote());
})->describe('Display an inspiring quote');