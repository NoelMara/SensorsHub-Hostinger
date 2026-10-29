<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Sections in this file, in order:
|   1. Public          — anyone, no login needed
|   2. Auth (guest)    — login / register / 2FA / admin entry
|   3. Logout          — authenticated users only
|   4. Notifications   — authenticated users only
|   5. Report          — authenticated users only
|   6. AI Chat         — authenticated users only, throttled
|   7. Student         — /dashboard/* (students, instructors, admins via auth.redirect)
|   8. Email Verify    — authenticated users only
|   9. Instructor      — /instructor/* (instructor role only)
|  10. Administrator   — /administrator/* (administrator role only)
|
*/

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Instructor\DashboardController as AdminDashboardController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Instructor\SuggestionController as AdminSuggestionController;
use App\Http\Controllers\Administrator\DashboardController as AdministratorDashboardController;
use App\Http\Controllers\Administrator\UserController as AdministratorUserController;
use App\Http\Controllers\Administrator\ProfileController as AdministratorProfileController;
use App\Http\Controllers\Administrator\SuggestionController as AdministratorSuggestionController;
use App\Http\Controllers\Administrator\ContentController as AdministratorContentController;
use App\Http\Controllers\EmailVerificationController;

// ─────────────────────────────────────────────────────────────────────────────
// 1. PUBLIC CONTENT — visitors can browse sensors, projects, videos, products, and community suggestions.
// ─────────────────────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sensors', [SensorController::class, 'index'])->name('sensors.index');
Route::get('/sensors/{slug}', [SensorController::class, 'show'])->name('sensors.show');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
Route::get('/shop', [ProductController::class, 'index'])->name('shop.index');
Route::get('/shop/{id}', [ProductController::class, 'show'])->name('shop.show');

// Displays community suggestions for everyone. Signed-in users can add comments.
Route::get('/community', [SuggestionController::class, 'community'])->name('suggestions.community');

// ─────────────────────────────────────────────────────────────────────────────
// 2. GUEST AUTHENTICATION — registration, login, login-code verification, and administrator entry.
// ─────────────────────────────────────────────────────────────────────────────
// NOTE: No route-level throttle on login — rate limiting is handled inside
//       LoginController using RateLimiter::hit/tooManyAttempts, so only
//       FAILED attempts count against the user, not every request.
Route::middleware('guest')->group(function () {
    // Shows the regular registration and login pages and processes their forms.
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);

    // Completes the verification-code step after login and sends a new code when requested.
    Route::post('/login/verify', [LoginController::class, 'verifyCode'])->name('login.verify');
    Route::post('/login/resend', [LoginController::class, 'resendCode'])->name('login.resend');

    // Provides the separate administrator sign-in page and checks administrator credentials.
    Route::get('/sys/secure-entry', [LoginController::class, 'showAdministratorLoginForm'])->name('administrator.login');
    Route::post('/sys/secure-entry', [LoginController::class, 'administratorLogin'])->name('administrator.login.submit');
});

// ─────────────────────────────────────────────────────────────────────────────
// 3. LOGOUT — signs out the currently authenticated user.
// ─────────────────────────────────────────────────────────────────────────────
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ─────────────────────────────────────────────────────────────────────────────
// 4. NOTIFICATIONS — shows a user's notifications and lets them mark or remove them.
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/notifications/{notification}/read', function (\App\Models\Notification $notification) {
        abort_unless($notification->user_id === auth()->id(), 403);
        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    })->name('notifications.read');
    
    Route::get('/notifications/count', function () {
        return response()->json([
            'count' => auth()->user()->notifications()->where('is_read', false)->count(),
        ]);
    })->name('notifications.count');

    Route::post('/notifications/read-all', function () {
        auth()->user()->notifications()->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    })->name('notifications.read-all');

    Route::get('/notifications', function () {
        $notifications = auth()->user()->notifications()->latest()->paginate(5);
        return view('notifications.index', compact('notifications'));
    })->name('notifications.index');

    Route::delete('/notifications/clear-read', function () {
        auth()->user()->notifications()->where('is_read', true)->delete();
        return back()->with('success', 'All read notifications cleared.');
    })->name('notifications.clear-read');

    Route::delete('/notifications/{notification}', function (\App\Models\Notification $notification) {
        if ($notification->user_id !== auth()->id()) abort(403);
        $notification->delete();
        return back()->with('success', 'Notification deleted.');
    })->name('notifications.destroy');
});

// ─────────────────────────────────────────────────────────────────────────────
// 5. REPORTING — lets signed-in users report inappropriate or incorrect content to administrators.
// ─────────────────────────────────────────────────────────────────────────────
Route::post('/report', [App\Http\Controllers\ReportController::class, 'store'])
    ->middleware('auth')
    ->name('report.store');

// ─────────────────────────────────────────────────────────────────────────────
// 6. AI CHAT — sends signed-in users' questions to the AI service with a request limit.
// ─────────────────────────────────────────────────────────────────────────────
Route::post('/api/chat', [ChatController::class, 'send'])
    ->middleware(['auth', 'throttle:5,1'])
    ->name('chat.send');

// ─────────────────────────────────────────────────────────────────────────────
// 7. USER DASHBOARD AND CLASSROOMS — /dashboard/* for authenticated users.
//    This is the normal signed-in user area for profiles, saved projects, suggestions,
//    feedback, classes, learning modules, assessments, announcements, and quizzes.
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth.redirect'])->prefix('dashboard')->name('dashboard.')->group(function () {

    // Opens the signed-in user's main dashboard.
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Lets a user update their account details, profile image, and password.
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Shows saved projects and lets a user save or remove a project from their list.
    Route::get('/saved-projects', [ProjectController::class, 'saved'])->name('saved');
    Route::post('/projects/{project}/save', [ProjectController::class, 'toggleSave'])->name('projects.save');

    // Lets a user submit suggestions and manage suggestions they created.
    Route::get('/suggestions', [SuggestionController::class, 'mySuggestions'])->name('suggestions');
    Route::post('/suggestions', [SuggestionController::class, 'store'])
        ->middleware('throttle:10,5')
        ->name('suggestions.store');
    Route::get('/suggestions/{suggestion}/edit', [SuggestionController::class, 'edit'])->name('suggestions.edit');
    Route::put('/suggestions/{suggestion}', [SuggestionController::class, 'update'])->name('suggestions.update');
    Route::delete('/suggestions/{suggestion}', [SuggestionController::class, 'destroy'])->name('suggestions.destroy');

    // Opens a suggestion with its details, status, and related discussion.
    Route::get('/suggestions/{suggestion}/view', [SuggestionController::class, 'show'])->name('suggestions.show');

    // Lets signed-in users join the discussion by adding or editing comments.
    Route::post('/suggestions/{suggestion}/comment', [SuggestionController::class, 'storeComment'])
        ->middleware('throttle:10,5')
        ->name('suggestions.comment.store');
    Route::put('/suggestions/{suggestion}/comment/{comment}', [SuggestionController::class, 'updateComment'])->name('suggestions.comment.update');

    // Shows a student's classes and lets them join a class using its join code.
    Route::get('/classes', [ClassroomController::class, 'studentClasses'])->name('classes.index');
    Route::post('/classes/join', [ClassroomController::class, 'join'])
        ->middleware('throttle:5,1')
        ->name('classes.join');

    // Lets students and instructors send feedback about the platform.
    Route::get('/feedback', [\App\Http\Controllers\FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])
        ->middleware('throttle:5,10')
        ->name('feedback.store');

    // Opens classroom content such as announcements, modules, assessments, and quizzes.
    Route::get('/classes/{class}', [ClassroomController::class, 'studentShow'])->name('classes.show');
    Route::get('/classes/{class}/modules/{module}', [ModuleController::class, 'show'])->name('classes.modules.show');
    Route::get('/classes/{class}/modules/{module}/download', [ModuleController::class, 'download'])->name('classes.modules.download');
    Route::get('/classes/{class}/assessments/{assessment}', [AssessmentController::class, 'show'])->name('classes.assessments.show');
    Route::post('/classes/{class}/assessments/{assessment}/submit', [AssessmentController::class, 'submit'])->name('classes.assessments.submit');
    Route::get('/classes/{class}/announcements', [AnnouncementController::class, 'studentIndex'])->name('classes.announcements.index');
    Route::get('/classes/{class}/modules', [ModuleController::class, 'studentIndex'])->name('classes.modules.index');
    Route::get('/classes/{class}/assessments', [AssessmentController::class, 'studentIndex'])->name('classes.assessments.index');

    // Provides the student quiz process: view, start, save answers, and submit.
    Route::get('/classes/{class}/quizzes', [QuizController::class, 'studentIndex'])->name('classes.quizzes.index');
    Route::get('/classes/{class}/quizzes/{quiz}', [QuizController::class, 'show'])->name('classes.quizzes.show');
    Route::post('/classes/{class}/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('classes.quizzes.start');
    Route::post('/classes/{class}/quizzes/{quiz}/save-answer', [QuizController::class, 'saveAnswer'])->name('classes.quizzes.save-answer');
    Route::post('/classes/{class}/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('classes.quizzes.submit');
    // Records tab switches while a student is taking a quiz.
    Route::post('/classes/{class}/quizzes/{quiz}/sync-switches', [QuizController::class, 'syncSwitches'])
        ->name('classes.quizzes.sync-switches');
});

// ─────────────────────────────────────────────────────────────────────────────
// 8. EMAIL VERIFICATION — confirms a user's email address using the code sent by Brevo.
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');
    Route::post('/email/verify', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
});

// ─────────────────────────────────────────────────────────────────────────────
// 9. INSTRUCTOR TOOLS — /instructor/* for class and learning-content management.
//    This area lets instructors build classes, publish learning content, review work,
//    manage resources, and view class progress.
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth.redirect', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {

    // Opens the instructor dashboard with class and student information.
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Lets instructors create classes, edit them, and approve or reject students.
    Route::get('/classes', [ClassroomController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [ClassroomController::class, 'create'])->name('classes.create');
    Route::post('/classes', [ClassroomController::class, 'store'])->name('classes.store');
    Route::get('/classes/{class}', [ClassroomController::class, 'show'])->name('classes.show');
    Route::get('/classes/{class}/edit', [ClassroomController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{class}', [ClassroomController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [ClassroomController::class, 'destroy'])->name('classes.destroy');
    Route::post('/classes/{class}/approve/{user}', [ClassroomController::class, 'approve'])->name('classes.approve');
    Route::post('/classes/{class}/approve-all', [ClassroomController::class, 'approveAll'])->name('classes.approve-all');
    Route::delete('/classes/{class}/reject/{user}', [ClassroomController::class, 'reject'])->name('classes.reject');

    // Lets instructors publish, import, edit, and remove class announcements.
    Route::get('/classes/{class}/announcements', [AnnouncementController::class, 'index'])->name('classes.announcements.index');
    Route::get('/classes/{class}/announcements/create', [AnnouncementController::class, 'create'])->name('classes.announcements.create');
    Route::post('/classes/{class}/announcements', [AnnouncementController::class, 'store'])->name('classes.announcements.store');
    Route::get('/classes/{class}/announcements/import', [AnnouncementController::class, 'import'])->name('classes.announcements.import');
    Route::post('/classes/{class}/announcements/import', [AnnouncementController::class, 'copyAnnouncements'])->name('classes.announcements.copy');
    Route::get('/classes/{class}/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('classes.announcements.edit');
    Route::put('/classes/{class}/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('classes.announcements.update');
    Route::delete('/classes/{class}/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('classes.announcements.destroy');

    // Lets instructors upload, import, edit, and remove learning modules.
    Route::get('/classes/{class}/modules', [ModuleController::class, 'index'])->name('classes.modules.index');
    Route::get('/classes/{class}/modules/create', [ModuleController::class, 'create'])->name('classes.modules.create');
    Route::post('/classes/{class}/modules', [ModuleController::class, 'store'])->name('classes.modules.store');
    Route::delete('/classes/{class}/modules/{module}', [ModuleController::class, 'destroy'])->name('classes.modules.destroy');
    Route::get('/classes/{class}/modules/import', [ModuleController::class, 'import'])->name('classes.modules.import');
    Route::post('/classes/{class}/modules/import', [ModuleController::class, 'copyModules'])->name('classes.modules.copy');
    Route::get('/classes/{class}/modules/{module}/edit', [ModuleController::class, 'edit'])->name('classes.modules.edit');
    Route::put('/classes/{class}/modules/{module}', [ModuleController::class, 'update'])->name('classes.modules.update');

    // Lets instructors create assessments, review submissions, assign grades, and edit them.
    Route::get('/classes/{class}/assessments', [AssessmentController::class, 'index'])->name('classes.assessments.index');
    Route::get('/classes/{class}/assessments/create', [AssessmentController::class, 'create'])->name('classes.assessments.create');
    Route::post('/classes/{class}/assessments', [AssessmentController::class, 'store'])->name('classes.assessments.store');
    Route::get('/classes/{class}/assessments/import', [AssessmentController::class, 'import'])->name('classes.assessments.import');
    Route::post('/classes/{class}/assessments/import', [AssessmentController::class, 'copyAssessments'])->name('classes.assessments.copy');
    Route::get('/classes/{class}/assessments/{assessment}', [AssessmentController::class, 'show'])->name('classes.assessments.show');
    Route::get('/classes/{class}/assessments/{assessment}/edit', [AssessmentController::class, 'edit'])->name('classes.assessments.edit');
    Route::put('/classes/{class}/assessments/{assessment}', [AssessmentController::class, 'update'])->name('classes.assessments.update');
    Route::get('/classes/{class}/assessments/{assessment}/submissions', [AssessmentController::class, 'submissions'])->name('classes.assessments.submissions');
    Route::post('/classes/{class}/assessments/{assessment}/grade/{submission}', [AssessmentController::class, 'grade'])->name('classes.assessments.grade');
    Route::delete('/classes/{class}/assessments/{assessment}', [AssessmentController::class, 'destroy'])->name('classes.assessments.destroy');

    // Lets instructors create quizzes, manage questions, and review student submissions.
    Route::get('/classes/{class}/quizzes', [QuizController::class, 'index'])->name('classes.quizzes.index');
    Route::get('/classes/{class}/quizzes/create', [QuizController::class, 'create'])->name('classes.quizzes.create');
    Route::post('/classes/{class}/quizzes', [QuizController::class, 'store'])->name('classes.quizzes.store');
    Route::get('/classes/{class}/quizzes/import', [QuizController::class, 'import'])->name('classes.quizzes.import');
    Route::post('/classes/{class}/quizzes/import', [QuizController::class, 'copyQuizzes'])->name('classes.quizzes.copy');
    Route::get('/classes/{class}/quizzes/{quiz}', [QuizController::class, 'show'])->name('classes.quizzes.show');
    Route::get('/classes/{class}/quizzes/{quiz}/edit', [QuizController::class, 'edit'])->name('classes.quizzes.edit');
    Route::put('/classes/{class}/quizzes/{quiz}', [QuizController::class, 'update'])->name('classes.quizzes.update');
    Route::delete('/classes/{class}/quizzes/{quiz}', [QuizController::class, 'destroy'])->name('classes.quizzes.destroy');
    Route::get('/classes/{class}/quizzes/{quiz}/submissions', [QuizController::class, 'submissions'])->name('classes.quizzes.submissions');

    // Shows class rankings and learning-progress information.
    Route::get('/classes/{class}/leaderboard', [ClassroomController::class, 'leaderboard'])->name('classes.leaderboard');
    Route::get('/classes/{class}/gradebook/export', [ClassroomController::class, 'exportGradebook'])->name('classes.gradebook.export');
    Route::get('/classes/{class}/gradebook', [ClassroomController::class, 'gradebook'])->name('classes.gradebook');
    Route::get('/classes/{class}/analytics', [ClassroomController::class, 'analytics'])->name('classes.analytics');

    // Lets instructors connect public sensors, projects, and videos to a class.
    Route::get('/classes/{class}/resources', [ClassroomController::class, 'resources'])->name('classes.resources');
    Route::post('/classes/{class}/resources', [ClassroomController::class, 'storeResource'])->name('classes.resources.store');
    Route::delete('/classes/{class}/resources/{resource}', [ClassroomController::class, 'destroyResource'])->name('classes.resources.destroy');

    // Lets instructors send feedback about the platform.
    Route::get('/feedback', [\App\Http\Controllers\FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store'])
        ->middleware('throttle:5,10')
        ->name('feedback.store');

    // Lets instructors review community suggestions and participate in discussions.
    Route::get('/suggestions', [AdminSuggestionController::class, 'index'])->name('suggestions.index');
    Route::get('/suggestions/{suggestion}', [AdminSuggestionController::class, 'show'])->name('suggestions.show');
    Route::put('/suggestions/{suggestion}/status', [AdminSuggestionController::class, 'updateStatus'])->name('suggestions.status');
    Route::post('/suggestions/{suggestion}/comment', [AdminSuggestionController::class, 'storeComment'])->name('suggestions.comment.store');
    Route::put('/suggestions/{suggestion}/comment/{comment}', [AdminSuggestionController::class, 'updateComment'])->name('suggestions.comment.update');
});

// ─────────────────────────────────────────────────────────────────────────────
// 10. ADMINISTRATOR TOOLS — /administrator/* for platform administration.
//     This area is for platform-wide management that should only be available to administrators.
// ─────────────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'administrator'])->prefix('administrator')->name('administrator.')->group(function () {

    // Opens the administrator dashboard and overall platform analytics.
    Route::get('/dashboard', [AdministratorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AdministratorDashboardController::class, 'analytics'])->name('analytics');

    // Lets the administrator update their own profile and password.
    Route::get('/profile', [AdministratorProfileController::class, 'show'])->name('profile');
    Route::match(['put', 'post'], '/profile/update', [AdministratorProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [AdministratorProfileController::class, 'updatePassword'])->name('profile.password');

    // Shows recorded activity and lets the administrator clear old log entries.
    Route::get('/logs', [AdministratorDashboardController::class, 'logs'])->name('logs');
    Route::delete('/logs/clear', [AdministratorDashboardController::class, 'clearLogs'])->name('logs.clear');

    // Lets the administrator review, update, and remove user feedback.
    Route::get('/feedback', [\App\Http\Controllers\FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/{feedback}', [\App\Http\Controllers\FeedbackController::class, 'show'])->name('feedback.show');
    Route::put('/feedback/{feedback}/status', [\App\Http\Controllers\FeedbackController::class, 'updateStatus'])->name('feedback.status');
    Route::delete('/feedback/{feedback}', [\App\Http\Controllers\FeedbackController::class, 'destroy'])->name('feedback.destroy');

    // Creates database backup files and lets the administrator download or delete them.
    Route::get('/backup', function () {
        return view('administrator.backup');
    })->name('backup');
    Route::get('/backup/download', [AdministratorDashboardController::class, 'backup'])->name('backup.download');
    Route::get('/backup/download/{filename}', [AdministratorDashboardController::class, 'downloadBackup'])->name('backup.download-file');
    Route::post('/backup/remove/{filename}', [AdministratorDashboardController::class, 'deleteBackup'])->name('backup.delete');

    // Lets the administrator create, edit, remove, assign roles, warn, ban, and unban users.
    Route::get('/users', [AdministratorUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdministratorUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdministratorUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [AdministratorUserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdministratorUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdministratorUserController::class, 'update'])->name('users.update');
    Route::put('/users/{user}/role', [AdministratorUserController::class, 'updateRole'])->name('users.role');
    Route::delete('/users/{user}', [AdministratorUserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/warn', [AdministratorUserController::class, 'warn'])->name('users.warn');
    Route::post('/users/{user}/ban', [AdministratorUserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [AdministratorUserController::class, 'unban'])->name('users.unban');

    // Lets the administrator review, approve, moderate, and remove suggestions or comments.
    Route::get('/suggestions', [AdministratorSuggestionController::class, 'index'])->name('suggestions.index');
    Route::get('/suggestions/{suggestion}', [AdministratorSuggestionController::class, 'show'])->name('suggestions.show');
    Route::put('/suggestions/{suggestion}/status', [AdministratorSuggestionController::class, 'updateStatus'])->name('suggestions.status');
    Route::post('/suggestions/{suggestion}/approve', [AdministratorSuggestionController::class, 'approve'])->name('suggestions.approve');
    Route::delete('/suggestions/{suggestion}', [AdministratorSuggestionController::class, 'destroy'])->name('suggestions.destroy');
    Route::delete('/suggestions/{suggestion}/comment/{comment}', [AdministratorSuggestionController::class, 'destroyComment'])->name('suggestions.comment.destroy');
    Route::post('/suggestions/{suggestion}/comment', [AdministratorSuggestionController::class, 'storeComment'])->name('suggestions.comment.store');
    Route::put('/suggestions/{suggestion}/comment/{comment}', [AdministratorSuggestionController::class, 'updateComment'])->name('suggestions.comment.update');

    // Lets the administrator create, edit, and remove sensors, projects, products, and videos.
    Route::get('/sensors', [AdministratorContentController::class, 'sensors'])->name('sensors.index');
    Route::get('/projects', [AdministratorContentController::class, 'projects'])->name('projects.index');
    Route::get('/products', [AdministratorContentController::class, 'products'])->name('products.index');
    Route::get('/videos', [AdministratorContentController::class, 'videos'])->name('videos.index');
    Route::get('/{type}/create', [AdministratorContentController::class, 'create'])
        ->whereIn('type', ['sensors', 'projects', 'products', 'videos'])
        ->name('content.create');
    Route::post('/{type}', [AdministratorContentController::class, 'store'])
        ->whereIn('type', ['sensors', 'projects', 'products', 'videos'])
        ->name('content.store');
    Route::get('/{type}/{id}/edit', [AdministratorContentController::class, 'edit'])
        ->whereIn('type', ['sensors', 'projects', 'products', 'videos'])
        ->name('content.edit');
    Route::put('/{type}/{id}', [AdministratorContentController::class, 'update'])
        ->whereIn('type', ['sensors', 'projects', 'products', 'videos'])
        ->name('content.update');
    Route::delete('/{type}/{id}', [AdministratorContentController::class, 'destroy'])
        ->whereIn('type', ['sensors', 'projects', 'products', 'videos'])
        ->name('content.destroy');
});
