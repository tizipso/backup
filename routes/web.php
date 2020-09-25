<?php

use Dcat\Admin\Extension\Backup\Http\Controllers;

Route::prefix('backup')->group(function () {
    Route::get('', Controllers\BackupController::class.'@index');
    Route::get('/download', Controllers\BackupController::class.'@download')->name('backup-download');
    Route::post('/run', Controllers\BackupController::class.'@run')->name('backup-run');
    Route::delete('/delete', Controllers\BackupController::class.'@delete')->name('backup-delete');
});