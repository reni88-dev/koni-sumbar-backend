<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\CaborController;
use App\Http\Controllers\Master\EducationLevelController;
use App\Http\Controllers\Master\CompetitionClassController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AthleteController;
use App\Http\Controllers\FormBuilderController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\ActivityLogController;

// Public routes with specific rate limiting
Route::middleware('throttle:login')->post('/login', [AuthController::class, 'login']);
Route::middleware('throttle:register')->post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user()->load('role.permissions');
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'permissions' => $user->getPermissions(),
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Master Data Routes
    Route::prefix('master')->group(function () {
        // Roles
        Route::get('/permissions', [RoleController::class, 'permissions']);
        Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions']);
        Route::apiResource('roles', RoleController::class);
        
        // Users
        Route::get('/users/roles', [UserController::class, 'roles']);
        Route::apiResource('users', UserController::class);
        
        // Cabors
        Route::get('/cabors/all', [CaborController::class, 'all']);
        Route::apiResource('cabors', CaborController::class);
        
        // Education Levels
        Route::get('/education-levels/all', [EducationLevelController::class, 'all']);
        Route::apiResource('education-levels', EducationLevelController::class);
        
        // Competition Classes
        Route::get('/competition-classes/all', [CompetitionClassController::class, 'all']);
        Route::post('/competition-classes/batch', [CompetitionClassController::class, 'storeBatch']);
        Route::apiResource('competition-classes', CompetitionClassController::class);
    });

    // Athletes Routes
    Route::get('/athletes/all', [AthleteController::class, 'all']);
    Route::get('/athletes/{athlete}/events', [AthleteController::class, 'events']);
    Route::apiResource('athletes', AthleteController::class);
    
    // Master Data Dropdowns (accessible by all authenticated users)
    Route::get('/cabors/all', [CaborController::class, 'all']);
    Route::get('/education-levels/all', [EducationLevelController::class, 'all']);
    Route::get('/competition-classes/all', [CompetitionClassController::class, 'all']);

    // Events Routes
    Route::get('/events/all', [EventController::class, 'all']);
    Route::get('/events/{event}/athletes', [EventController::class, 'athletes']);
    Route::post('/events/{event}/athletes', [EventController::class, 'registerAthlete']);
    Route::put('/events/{event}/athletes/{athlete}', [EventController::class, 'updateAthleteStatus']);
    Route::delete('/events/{event}/athletes/{athlete}', [EventController::class, 'removeAthlete']);
    Route::apiResource('events', EventController::class);

    // Form Builder Routes (Admin only)
    Route::prefix('form-builder')->group(function () {
        // Available models for data source
        Route::get('/models', [FormBuilderController::class, 'getAvailableModels']);
        Route::get('/models/{model}/fields', [FormBuilderController::class, 'getModelFields']);
        Route::get('/models/{model}/records', [FormBuilderController::class, 'getModelRecords']);
        
        // Form templates CRUD
        Route::get('/templates', [FormBuilderController::class, 'index']);
        Route::post('/templates', [FormBuilderController::class, 'store']);
        Route::get('/templates/{formTemplate}', [FormBuilderController::class, 'show']);
        Route::put('/templates/{formTemplate}', [FormBuilderController::class, 'update']);
        Route::delete('/templates/{formTemplate}', [FormBuilderController::class, 'destroy']);
        
        // Get reference data for a template
        Route::get('/templates/{formTemplate}/reference/{referenceId}', [FormBuilderController::class, 'getReferenceData']);
        
        // Submissions for a template
        Route::get('/templates/{formTemplate}/submissions', [FormSubmissionController::class, 'index']);
        Route::post('/templates/{formTemplate}/submissions', [FormSubmissionController::class, 'store']);
        
        // Individual submission
        Route::get('/submissions/{formSubmission}', [FormSubmissionController::class, 'show']);
        Route::put('/submissions/{formSubmission}', [FormSubmissionController::class, 'update']);
        Route::delete('/submissions/{formSubmission}', [FormSubmissionController::class, 'destroy']);
        
        // Grading preview
        Route::post('/grading/preview', [FormSubmissionController::class, 'previewGrading']);
    });

    // Activity Logs Routes (Super Admin only)
    Route::middleware('superadmin')->prefix('activity-logs')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index']);
        Route::get('/stats', [ActivityLogController::class, 'stats']);
        Route::get('/users', [ActivityLogController::class, 'users']);
        Route::get('/export', [ActivityLogController::class, 'export']);
        Route::get('/{id}', [ActivityLogController::class, 'show']);
        Route::delete('/cleanup', [ActivityLogController::class, 'cleanup']);
    });

    // Error Logs Routes (Super Admin only)
    Route::middleware('superadmin')->prefix('error-logs')->group(function () {
        Route::get('/', [ActivityLogController::class, 'errorIndex']);
        Route::get('/stats', [ActivityLogController::class, 'errorStats']);
        Route::get('/{id}', [ActivityLogController::class, 'errorShow']);
        Route::post('/{id}/resolve', [ActivityLogController::class, 'resolveError']);
        Route::delete('/cleanup', [ActivityLogController::class, 'errorCleanup']);
    });
});
