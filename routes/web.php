<?php

use App\Models\Document;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Livewire\SharedDocuments;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\DocumentController;

Route::view('/', 'welcome');

Route::get('/locale/{locale}', function ($locale) {
    app()->setLocale($locale);
    session()->put('locale', $locale);
    return redirect()->back();
});

Route::get('/share/{token}', function (Request $request, $token) {
    $document = Document::where('sharing_token', $token)
        ->where('is_public', true)
        ->with(['threads.messages' => function($query) {
            $query->orderBy('id', 'asc');
        }])
        ->firstOrFail();
    
    $pdfUrl = Storage::temporaryUrl(
        $document->file_path, now()->addMinutes(5)
    );
    
    return view('documents.public', compact('document', 'pdfUrl'));
})->name('documents.public');

Route::group(['middleware' => 'auth'], function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('documents', 'dashboard')->name('documents.index');
    Route::view('profile', 'profile')->name('profile');
    Route::get('shared-documents', SharedDocuments::class)->name('documents.shared');
    Route::post('documents/{document}/copy', [App\Http\Controllers\DocumentController::class, 'copy'])->name('documents.copy');
    Route::post('/upload-documents', [DocumentController::class, 'upload'])->name('documents.upload');

    Route::get('/documents/{document}', function (Request $request, Document $document) {
        if (config('app.require_email_verification') && !$request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        $pdfUrl = Storage::temporaryUrl(
            $document->file_path, now()->addMinutes(5)
        );
        return view('documents.show', compact('document', 'pdfUrl'));
    })->name('documents.show');

    Route::post('/documents/{document}/share', function (Request $request, Document $document) {
        if ($document->user_id !== $request->user()->id) {
            abort(403);
        }

        if (!$document->sharing_token) {
            $document->update([
                'sharing_token' => Str::random(32),
                'is_public' => true,
            ]);
        } else {
            $document->update(['is_public' => !$document->is_public]);
        }

        return back()->with('message', $document->is_public ? 'Document is now public' : 'Document is now private');
    })->name('documents.share');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', __('Verification link sent!'));
    })
    ->middleware(['throttle:2,1'])
    ->name('verification.send');

    Route::supportBubble();
});

Route::post('/login/google/callback', FirebaseController::class)->name('login.google.callback');


require __DIR__.'/auth.php';
