<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Project;
use App\Models\Sensor;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\Video;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::whereIn('role', ['user', 'student'])->count(),
            'instructors' => User::where('role', 'instructor')->count(),
            'administrators' => User::where('role', 'administrator')->count(),
            'sensors' => Sensor::count(),
            'projects' => Project::count(),
            'products' => Product::count(),
            'videos' => Video::count(),
            'suggestions' => Suggestion::count(),
            'pending_suggestions' => Suggestion::where('status', 'pending')->count(),
        ];

        $recentUsers = User::latest()->take(6)->get();
        $recentSuggestions = Suggestion::with('user')->latest()->take(5)->get();
        // Recent comments from users only (excludes administrators)
        $recentComments = Comment::with(['user', 'suggestion'])
            ->whereHas('user', function ($userQuery) {
                $userQuery->where('role', '!=', 'administrator');
            })
            ->latest()
            ->take(5)
            ->get();

        return view('administrator.dashboard', compact('stats', 'recentUsers', 'recentSuggestions', 'recentComments'));
    }

    public function analytics()
    {
        $totalUsers = User::whereIn('role', ['user', 'student'])->count();
        $totalInstructors = User::where('role', 'instructor')->count();
        $totalClasses = Classroom::count();
        $totalContent = Sensor::count() + Project::count() + Product::count() + Video::count();
        $newThisMonth = User::where('created_at', '>=', now()->subDays(30))
            ->where('role', '!=', 'administrator')
            ->count();
        $bannedCount = User::whereNotNull('banned_at')->count();

        // User growth chart (last 30 days)
        $userGrowth = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $userGrowth[] = [
                'date' => $date,
                'count' => User::whereDate('created_at', $date)
                    ->where('role', '!=', 'administrator')
                    ->count(),
            ];
        }

        // Top classes (by student count + average score)
        $topClasses = Classroom::withCount('students')
            ->with(['assessments', 'quizzes'])
            ->get()
            ->map(function ($class) {
                $totalPoints = 0;
                $totalPossible = 0;

                foreach ($class->assessments as $assessment) {
                    $totalPossible += $assessment->points;
                    $totalPoints += $assessment->submissions()->whereNotNull('score')->sum('score');
                }
                foreach ($class->quizzes as $quiz) {
                    $totalPossible += $quiz->points;
                    $totalPoints += $quiz->submissions()->whereNotNull('score')->sum('score');
                }

                $avgScore = $totalPossible > 0 ? round(($totalPoints / $totalPossible) * 100, 1) : 0;

                return [
                    'name' => $class->name,
                    'section' => $class->section,
                    'instructor' => $class->instructor->name ?? 'Unknown',
                    'students_count' => $class->students_count,
                    'avg_score' => $avgScore,
                ];
            })
            ->sortByDesc('students_count')
            ->take(5)
            ->values();

        // Content breakdown
        $contentBreakdown = [
            'sensors' => Sensor::count(),
            'projects' => Project::count(),
            'videos' => Video::count(),
            'products' => Product::count(),
        ];

        return view('administrator.analytics', compact(
            'totalUsers', 'totalInstructors', 'totalClasses', 'totalContent',
            'newThisMonth', 'bannedCount', 'userGrowth', 'topClasses', 'contentBreakdown'
        ));
    }

    public function logs()
    {
        $logs = ActivityLog::latest()->paginate(10);
        return view('administrator.logs', compact('logs'));
    }

    public function backup()
    {
        if (!auth()->user()->isAdministrator()) {
            abort(403);
        }

        $backupPath = storage_path('app/backups');
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $files = glob($backupPath . '/*.sql');
        $totalBackups = count($files);

        if ($totalBackups >= 5) {
            unlink($files[0]);
            $totalBackups = 4;
        }

        // ─────────────────────────────────────────────────────────────
        // MySQL: list all tables (uses information_schema, portable)
        // ─────────────────────────────────────────────────────────────
        $tables = DB::select("
            SELECT table_name AS tablename
            FROM information_schema.tables
            WHERE table_schema = DATABASE()
        ");

        $tables = collect($tables)->sortBy(function ($table) {
            $priority = [
                'users' => 0,
                'sensors' => 1,
                'projects' => 1,
                'products' => 1,
                'videos' => 1,
                'classes' => 2,
                'suggestions' => 2,
                'announcements' => 3,
                'modules' => 3,
                'assessments' => 3,
                'quizzes' => 3,
                'class_student' => 3,
                'class_resource' => 3,
                'notifications' => 3,
                'saved_projects' => 3,
                'comments' => 3,
                'quiz_questions' => 4,
                'quiz_options' => 5,
                'assessment_submissions' => 5,
                'quiz_submissions' => 5,
                'quiz_answers' => 6,
                'reports' => 4,
                'activity_logs' => 7,
            ];
            return $priority[$table->tablename] ?? 99;
        })->values()->all();

        $output = "-- SensorsHub Database Backup\n";
        $output .= "-- Generated: " . now()->toDateTimeString() . "\n";
        $output .= "-- For MySQL\n\n";
        $output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $skipTables = ['migrations', 'sessions', 'cache', 'jobs', 'failed_jobs', 'personal_access_tokens', 'password_reset_tokens'];

        foreach ($tables as $table) {
            $tableName = $table->tablename;
            if (in_array($tableName, $skipTables)) continue;

            $rows = DB::table($tableName)->get();
            if ($rows->isEmpty()) continue;

            $output .= "-- Table: `{$tableName}`\n";

            // MySQL uses backticks for identifiers
            $columns = array_keys((array) $rows->first());
            $columnList = implode(', ', array_map(fn($c) => '`' . $c . '`', $columns));

            foreach ($rows as $row) {
                $values = [];
                foreach ((array) $row as $value) {
                    if (is_null($value)) {
                        $values[] = 'NULL';
                    } elseif (is_bool($value)) {
                        // MySQL uses 1/0 for booleans
                        $values[] = $value ? '1' : '0';
                    } elseif (is_int($value) || is_float($value)) {
                        $values[] = $value;
                    } else {
                        // Escape single quotes and backslashes for MySQL
                        $escaped = str_replace(['\\', "'"], ['\\\\', "''"], $value);
                        $values[] = "'" . $escaped . "'";
                    }
                }
                $output .= "INSERT INTO `{$tableName}` ({$columnList}) VALUES (" . implode(', ', $values) . ");\n";
            }
            $output .= "\n";
        }

        // MySQL uses AUTO_INCREMENT instead of sequences — no explicit reset needed.
        // MySQL automatically sets the next ID based on MAX(id) when the next insert happens.
        // (Optional explicit reset not required.)

        $output .= "SET FOREIGN_KEY_CHECKS=1;\n";

        $filename = 'sensorshub-backup-' . now()->format('Y-m-d-H-i-s') . '.sql';
        file_put_contents($backupPath . '/' . $filename, $output);

        return response($output)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function downloadBackup($filename)
    {
        if (!auth()->user()->isAdministrator()) {
            abort(403);
        }

        if (!preg_match('/^sensorshub-backup-[\d\-]+\.sql$/', $filename)) {
            abort(404);
        }

        $path = storage_path('app/backups/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }

    public function deleteBackup($filename)
    {
        if (!auth()->user()->isAdministrator()) {
            abort(403);
        }

        if (!preg_match('/^sensorshub-backup-[\d\-]+\.sql$/', $filename)) {
            abort(404);
        }

        $path = storage_path('app/backups/' . $filename);

        if (file_exists($path)) {
            unlink($path);
        }

        return back()->with('success', 'Backup deleted.');
    }

    public function clearLogs()
    {
        ActivityLog::truncate();
        return back()->with('success', 'All activity logs have been cleared.');
    }
}
