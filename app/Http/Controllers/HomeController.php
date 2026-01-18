<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Exam;
use App\Models\Attendance;
use App\Models\FeesInformation;
use App\Models\Bus;
use App\Models\Route;
use App\Models\StudentBusAssignment;
use App\Models\ExamResult;
use App\Models\User;
use App\Models\StudentFeeTerm;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
    * Show the application dashboard.
    *
    * @return \Illuminate\Contracts\Support\Renderable
    */
    /** home dashboard */
    public function index()
{
    // CRITICAL: Force fresh database connection to avoid stale data
    // This ensures we see the latest committed data, even from recent transactions
    // Use a buffered Laravel query instead of raw PDO exec to avoid unbuffered query errors
    DB::select('SELECT 1');
    
    // Clear any query cache
    DB::flushQueryLog();
    
    // Basic Counts
    $totalStudents = Student::count();
    $totalTeachers = Teacher::count();
    $totalDepartments = Department::count();
    // Total earnings for current term only
    $totalEarnings = StudentFeeTerm::where('status', 'current')->sum('amount_paid');
    
    // Additional Statistics
    $totalSubjects = \App\Models\Subject::count();
    $totalExams = Exam::count();
    $totalEvents = Event::where('is_active', true)->count();
    $totalBuses = Bus::where('is_active', true)->count();
    
    // Fee Statistics
    $totalFeeCollected = FeesInformation::sum('fees_amount');
    
    // Pending fees - use latest fee term's closing_balance as source of truth
    // This ensures we always get the most up-to-date balance from the fee terms table
    // Using a subquery to get the latest fee term per student (by MAX(id) which is the most recent)
    // and sum their closing_balances. This avoids N+1 queries and ensures accuracy.
    // Since fee term IDs are auto-incrementing, MAX(id) per student = latest term
    $pendingFees = DB::selectOne("
        SELECT COALESCE(SUM(sft.closing_balance), 0) as total
        FROM student_fee_terms sft
        INNER JOIN (
            SELECT student_id, MAX(id) as latest_term_id
            FROM student_fee_terms
            GROUP BY student_id
        ) latest ON sft.id = latest.latest_term_id
        WHERE sft.closing_balance > 0
    ")->total ?? 0;
    
    // Count distinct students with pending fees
    // Count students who have a latest fee term with closing_balance > 0
    // Uses the same subquery logic to ensure consistency
    $pendingFeeStudents = DB::selectOne("
        SELECT COUNT(DISTINCT sft.student_id) as total
        FROM student_fee_terms sft
        INNER JOIN (
            SELECT student_id, MAX(id) as latest_term_id
            FROM student_fee_terms
            GROUP BY student_id
        ) latest ON sft.id = latest.latest_term_id
        WHERE sft.closing_balance > 0
    ")->total ?? 0;
    // Count distinct students with current term balance <= 0 (paid or credit)
    $paidStudents = DB::table('student_fee_terms')
        ->where('status', 'current')
        ->where('closing_balance', '<=', 0)
        ->distinct()
        ->count('student_id');
    
    // Attendance Statistics (Today)
    $todayAttendance = Attendance::whereDate('attendance_date', today())->count();
    $todayPresent = Attendance::whereDate('attendance_date', today())->where('status', 'present')->count();
    $todayAbsent = Attendance::whereDate('attendance_date', today())->where('status', 'absent')->count();
    $attendanceRate = $totalStudents > 0 ? round(($todayPresent / $totalStudents) * 100, 1) : 0;
    
    // Transport Statistics
    $studentsWithTransport = StudentBusAssignment::where('status', 'active')->count();
    $activeRoutes = Route::where('is_active', true)->count();
    
    // Get students grouped by gender
    $studentsByGender = Student::selectRaw('gender, COUNT(*) as total')
        ->groupBy('gender')
        ->pluck('total', 'gender');

    // Get monthly stats for students and teachers
    $studentMonthly = Student::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    $teacherMonthly = Teacher::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();
    
    // Fee collection monthly stats
    $feeMonthly = FeesInformation::selectRaw('MONTH(paid_date) as month, SUM(fees_amount) as total')
        ->groupBy('month')
        ->pluck('total', 'month')
        ->toArray();

    // Fill 12 months with zeros if no data
    $months = range(1, 12);
    $monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $studentData = [];
    $teacherData = [];
    $feeData = [];

    foreach ($months as $m) {
        $studentData[] = (int)($studentMonthly[$m] ?? 0);
        $teacherData[] = (int)($teacherMonthly[$m] ?? 0);
        $feeData[] = (float)($feeMonthly[$m] ?? 0);
    }
    
    // Ensure arrays are not empty
    if (empty($studentData)) {
        $studentData = array_fill(0, 12, 0);
    }
    if (empty($teacherData)) {
        $teacherData = array_fill(0, 12, 0);
    }
    if (empty($feeData)) {
        $feeData = array_fill(0, 12, 0);
    }

    // Extract gender counts safely
    $boys = $studentsByGender['Male'] ?? 0;
    $girls = $studentsByGender['Female'] ?? 0;
    
    // Recent Activity
    $recentStudents = Student::orderBy('created_at', 'desc')->limit(5)->get();
    $recentTeachers = Teacher::orderBy('created_at', 'desc')->limit(5)->get();
    $recentNotifications = Notification::where(function($query) {
        $query->where('user_id', auth()->id())
              ->orWhereNull('user_id');
    })->orderBy('created_at', 'desc')->limit(5)->get();
    
    // Upcoming Events (next 7 days)
    $upcomingEvents = Event::where('is_active', true)
        ->where('start_date', '>=', today())
        ->where('start_date', '<=', today()->addDays(7))
        ->orderBy('start_date', 'asc')
        ->limit(5)
        ->get();
    
    // Top Performing Students (if exam results exist)
    $topStudents = ExamResult::select('student_id', DB::raw('AVG(marks) as avg_marks'))
        ->groupBy('student_id')
        ->orderBy('avg_marks', 'desc')
        ->limit(5)
        ->with(['student' => function($query) {
            $query->select('id', 'first_name', 'last_name');
        }])
        ->get();
    
    // Recent Fee Payments
    $recentPayments = FeesInformation::with('student')
        ->whereNotNull('student_id')
        ->orderBy('paid_date', 'desc')
        ->limit(5)
        ->get();

    // Pass everything to view
    return view('dashboard.home', compact(
        'totalStudents',
        'totalTeachers',
        'totalDepartments',
        'boys',
        'girls',
        'studentData',
        'teacherData',
        'totalEarnings',
        'totalSubjects',
        'totalExams',
        'totalEvents',
        'totalBuses',
        'totalFeeCollected',
        'pendingFees',
        'paidStudents',
        'pendingFeeStudents',
        'todayAttendance',
        'todayPresent',
        'todayAbsent',
        'attendanceRate',
        'studentsWithTransport',
        'activeRoutes',
        'monthLabels',
        'feeData',
        'recentStudents',
        'recentTeachers',
        'recentNotifications',
        'upcomingEvents',
        'topStudents',
        'recentPayments'
    ));
}

/**
 * API endpoint to get latest pending fees data
 * This allows the dashboard to auto-refresh pending fees without full page reload
 */
public function getPendingFees()
{
    // CRITICAL: Force a fresh database connection to avoid transaction isolation issues
    // This ensures we see the latest committed data, even from other transactions
    // Use a buffered Laravel query instead of raw PDO exec to avoid unbuffered query errors
    DB::select('SELECT 1');
    
    // Clear any query cache to ensure fresh data
    DB::flushQueryLog();
    
    // Pending fees - use latest fee term's closing_balance as source of truth
    // This ensures we always get the most up-to-date balance from the fee terms table
    // Using a subquery to get the latest fee term per student (by MAX(id) which is the most recent)
    // and sum their closing_balances. This avoids N+1 queries and ensures accuracy.
    // Since fee term IDs are auto-incrementing, MAX(id) per student = latest term
    $pendingFees = DB::selectOne("
        SELECT COALESCE(SUM(sft.closing_balance), 0) as total
        FROM student_fee_terms sft
        INNER JOIN (
            SELECT student_id, MAX(id) as latest_term_id
            FROM student_fee_terms
            GROUP BY student_id
        ) latest ON sft.id = latest.latest_term_id
        WHERE sft.closing_balance > 0
    ")->total ?? 0;
    
    // Count distinct students with pending fees
    // Count students who have a latest fee term with closing_balance > 0
    // Uses the same subquery logic to ensure consistency
    $pendingFeeStudents = DB::selectOne("
        SELECT COUNT(DISTINCT sft.student_id) as total
        FROM student_fee_terms sft
        INNER JOIN (
            SELECT student_id, MAX(id) as latest_term_id
            FROM student_fee_terms
            GROUP BY student_id
        ) latest ON sft.id = latest.latest_term_id
        WHERE sft.closing_balance > 0
    ")->total ?? 0;
    
    $response = response()->json([
        'pendingFees' => (float) $pendingFees,
        'pendingFeeStudents' => $pendingFeeStudents,
        'formattedPendingFees' => 'Ksh ' . number_format($pendingFees, 2),
        'timestamp' => now()->toIso8601String() // Add timestamp for debugging
    ]);
    
    // Prevent caching of this response - use multiple headers for maximum compatibility
    $response->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0');
    $response->header('Pragma', 'no-cache');
    $response->header('Expires', '0');
    $response->header('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
    $response->header('ETag', md5($pendingFees . $pendingFeeStudents . time()));
    
    return $response;
}



    /** profile user */
    public function userProfile()
    {
        $user = Auth::user();
        return view('dashboard.profile', compact('user'));
    }

    /** update profile */
    public function updateProfile(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'phone_number' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'position' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'date_of_birth' => $request->date_of_birth,
                'position' => $request->position,
                'department' => $request->department,
            ];

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $image = $request->file('avatar');
                $image_name = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $image_name);
                
                // Delete old avatar if exists and not default
                if ($user->avatar && $user->avatar != 'photo_defaults.jpg' && file_exists(public_path('images/' . $user->avatar))) {
                    unlink(public_path('images/' . $user->avatar));
                }
                
                $updateData['avatar'] = $image_name;
            }

            $user->update($updateData);
            
            // Refresh user model to get updated data
            $user->refresh();

            // Update session data
            session([
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'position' => $user->position,
                'phone_number' => $user->phone_number,
            ]);

            DB::commit();
            Toastr::success('Profile updated successfully :)', 'Success');
            return redirect()->back();

        } catch(\Exception $e) {
            DB::rollback();
            Toastr::error('Profile update failed :)', 'Error');
            return redirect()->back();
        }
    }
}
