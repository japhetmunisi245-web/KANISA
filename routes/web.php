<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ChurchOfficeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Public Document Verification (QR Code)
Route::get('/verify-letter/{token}', [LetterController::class, 'verify'])->name('letters.verify');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/church-office', [ChurchOfficeController::class, 'index'])->name('church-office.index');
    Route::post('/church-office/minutes', [ChurchOfficeController::class, 'storeMinute'])->name('church-office.minutes.store');
    Route::post('/church-office/schedules', [ChurchOfficeController::class, 'storeSchedule'])->name('church-office.schedules.store');
    Route::post('/church-office/announcements', [ChurchOfficeController::class, 'storeAnnouncement'])->name('church-office.announcements.store');
    Route::post('/church-office/attendance', [ChurchOfficeController::class, 'storeAttendance'])->name('church-office.attendance.store');
    Route::post('/church-office/offerings', [ChurchOfficeController::class, 'storeOffering'])->name('church-office.offerings.store');
    Route::patch('/church-office/members/{member}/status', [ChurchOfficeController::class, 'updateMemberStatus'])->name('church-office.members.status.update');
    Route::post('/complete-onboarding', [DashboardController::class, 'completeOnboarding'])->name('onboarding.complete');

    // Official Letter System
    Route::post('/official-letters/store', [LetterController::class, 'store'])->name('official_letters.store');
    Route::post('/official-letters/{id}/approve', [LetterController::class, 'approve'])->name('official_letters.approve');
    Route::post('/official-letters/{id}/reject', [LetterController::class, 'reject'])->name('official_letters.reject');
    Route::get('/official-letters/{id}/download', [LetterController::class, 'downloadPdf'])->name('official_letters.download');
    Route::get('/official-letters/{id}/view', [LetterController::class, 'viewPdf'])->name('official_letters.view');

    // Payments & Receipts System
    Route::post('/contributions/store', [PaymentController::class, 'storeContributionCampaign'])->name('contributions.store');
    Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/receipts/{id}/download', [PaymentController::class, 'downloadReceipt'])->name('receipts.download');

    // Church Settings (Mchungaji)
    Route::post('/church-settings/update', [SettingController::class, 'update'])->name('church_settings.update');

    // Leader specific actions
    Route::post('/announcements', [DashboardController::class, 'storeAnnouncement'])->name('announcements.store');
    Route::post('/reports', [DashboardController::class, 'storeReport'])->name('reports.store');
    Route::get('/reports/{id}', [DashboardController::class, 'showReport'])->name('reports.show');
    Route::get('/reports/{id}/download', [DashboardController::class, 'downloadReport'])->name('reports.download');
    Route::post('/letters', [DashboardController::class, 'storeLetter'])->name('letters.store');
    Route::post('/sms/send', [DashboardController::class, 'sendSms'])->name('sms.send');

    // Member management (Kiongozi / Mchungaji)
    Route::post('/members/add', [DashboardController::class, 'addMember'])->name('members.add');
    Route::post('/members/remove', [DashboardController::class, 'removeMember'])->name('members.remove');

    // Help messages
    Route::post('/help/send', [DashboardController::class, 'sendHelp'])->name('help.send');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [DashboardController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
