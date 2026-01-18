<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/** for side bar menu active */
function set_active( $route ) {
    if( is_array( $route ) ){
        return in_array(Request::path(), $route) ? 'active' : '';
    }
    return Request::path() == $route ? 'active' : '';
}

Route::get('/', function () {
    return view('auth.login');
});

// Removed duplicate home route - handled by HomeController

Auth::routes();
Route::group(['namespace' => 'App\Http\Controllers\Auth'],function()
{
    // ----------------------------login ------------------------------//
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authenticate');
        Route::get('/logout', 'logout')->name('logout');
    });

    // ----------------------------- register -------------------------//
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'register')->name('register');
        Route::post('/register','storeUser')->name('register');    
    });
});

Route::group(['namespace' => 'App\Http\Controllers'],function()
{
    // -------------------------- main dashboard ----------------------//
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index')->middleware('auth')->name('home');
        Route::get('/home/pending-fees', 'getPendingFees')->middleware('auth')->name('home.pending-fees');
        Route::get('user/profile/page', 'userProfile')->middleware('auth')->name('user/profile/page');
        Route::post('user/profile/update', 'updateProfile')->middleware('auth')->name('user/profile/update');
        Route::get('teacher/dashboard', 'teacherDashboardIndex')->middleware('auth')->name('teacher/dashboard');
        Route::get('student/dashboard', 'studentDashboardIndex')->middleware('auth')->name('student/dashboard');
    });

    // ----------------------------- user controller ---------------------//
    Route::controller(UserManagementController::class)->group(function () {
        Route::get('list/users', 'index')->middleware('auth')->name('list/users');
        Route::post('change/password', 'changePassword')->middleware('auth')->name('change/password');
        Route::get('view/user/edit/{id}', 'userView')->middleware('auth');
        Route::post('user/update', 'userUpdate')->middleware('auth')->name('user/update');
        Route::post('user/delete', 'userDelete')->middleware('auth')->name('user/delete');
        Route::get('get-users-data', 'getUsersData')->middleware('auth')->name('get-users-data'); /** get all data users */

    });

    // ------------------------ setting -------------------------------//
    // Settings section removed - not needed
    // Route::controller(Setting::class)->group(function () {
    //     Route::get('setting/page', 'index')->middleware('auth')->name('setting/page');
    // });

    // ------------------------ financial settings -------------------------------//
    Route::controller(FinancialSettingsController::class)->group(function () {
        Route::get('settings/financial', 'index')->middleware('auth')->name('financial.settings');
        Route::post('settings/financial/update', 'update')->middleware('auth')->name('financial.settings.update');
        Route::post('settings/financial/apply', 'applyToAllStudents')->middleware('auth')->name('financial.settings.apply');
    });

    // ------------------------ student -------------------------------//
    Route::controller(StudentController::class)->group(function () {
        Route::get('student/list', 'student')->middleware('auth')->name('student/list'); // list student
        Route::get('student/list-by-class', 'studentsByClass')->middleware('auth')->name('student/list-by-class'); // list students by class
        Route::get('student/export-by-class', 'exportStudentsByClass')->middleware('auth')->name('student/export-by-class'); // export students by class
        Route::get('student/grid', 'studentGrid')->middleware('auth')->name('student/grid'); // grid student
        Route::get('student/add/page', 'studentAdd')->middleware('auth')->name('student/add/page'); // page student
        Route::post('student/add/save', 'studentSave')->middleware('auth')->name('student/add/save'); // save record student
        Route::get('student/edit/{id}', 'studentEdit')->middleware('auth')->name('student/edit'); // view for edit
        Route::post('student/update', 'studentUpdate')->middleware('auth')->name('student/update'); // update record student
        Route::post('student/delete', 'studentDelete')->middleware('auth')->name('student/delete'); // delete record student
        Route::post('students/bulk-delete', 'bulkDelete')->middleware('auth')->name('students.bulk-delete'); // bulk delete students
        Route::get('student/export', 'exportExcel')->middleware('auth')->name('student/export'); // export students to excel
        Route::get('student/profile/{id}', 'studentProfile')->middleware('auth'); // profile student
        Route::post('student/{student}/terms', 'storeTerm')->middleware('auth')->name('student.terms.store');
        Route::post('student/{student}/terms/{term}/payment', 'recordTermPayment')->middleware('auth')->name('student.terms.payment');
        Route::patch('student/{student}/terms/{term}/update-year', 'updateTermYear')->middleware('auth')->name('student.terms.update-year');
        Route::patch('student/{student}/terms/{term}/update-fee', 'updateTermFee')->middleware('auth')->name('student.terms.update-fee');
        Route::get('student/photo/{filename}', 'studentPhoto')->name('student.photo');

    });

    // ------------------------ teacher -------------------------------//
    Route::controller(TeacherController::class)->group(function () {
        Route::get('teacher/add/page', 'teacherAdd')->middleware('auth')->name('teacher/add/page'); // page teacher
        Route::get('teacher/list/page', 'teacherList')->middleware('auth')->name('teacher/list/page'); // page teacher
        Route::get('teacher/grid/page', 'teacherGrid')->middleware('auth')->name('teacher/grid/page'); // page grid teacher
        Route::get('teacher/profiles', 'teacherProfiles')->middleware('auth')->name('teacher/profiles'); // teacher profiles list
        Route::post('teacher/save', 'saveRecord')->middleware('auth')->name('teacher/save'); // save record
        Route::get('teacher/edit/{id}', 'editRecord')->middleware('auth'); // view teacher record
        Route::post('teacher/update', 'updateRecordTeacher')->middleware('auth')->name('teacher/update'); // update record
        Route::post('teacher/delete', 'teacherDelete')->middleware('auth')->name('teacher/delete'); // delete record teacher
        Route::post('teachers/bulk-delete', 'bulkDelete')->middleware('auth')->name('teachers.bulk-delete'); // bulk delete teachers
        Route::get('teacher/profile/{id}', 'teacherProfile')->middleware('auth')->name('teacher/profile'); // teacher profile
        Route::get('teacher/export', 'exportExcel')->middleware('auth')->name('teacher/export'); // export teachers to excel
    });

    // ----------------------- department -----------------------------//
    Route::controller(DepartmentController::class)->group(function () {
        Route::get('department/list/page', 'departmentList')->middleware('auth')->name('department/list/page'); // department/list/page
        Route::get('department/add/page', 'indexDepartment')->middleware('auth')->name('department/add/page'); // page add department
        Route::get('department/edit/{department_id}', 'editDepartment')->middleware('auth'); // page add department
        Route::post('department/save', 'saveRecord')->middleware('auth')->name('department/save'); // department/save
        Route::post('department/update', 'updateRecord')->middleware('auth')->name('department/update'); // department/update
        Route::post('department/delete', 'deleteRecord')->middleware('auth')->name('department/delete'); // department/delete
        Route::post('departments/bulk-delete', 'bulkDelete')->middleware('auth')->name('departments.bulk-delete'); // bulk delete departments
        Route::get('get-data-list', 'getDataList')->middleware('auth')->name('get-data-list'); // get data list

    });

    // ----------------------- subject -----------------------------//
    Route::controller(SubjectController::class)->group(function () {
        Route::get('subject/list/page', 'subjectList')->middleware('auth')->name('subject/list/page'); // subject/list/page
        Route::get('subject/add/page', 'subjectAdd')->middleware('auth')->name('subject/add/page'); // subject/add/page
        Route::post('subject/save', 'saveRecord')->middleware('auth')->name('subject.save'); // subject/save
        Route::post('subject/update', 'updateRecord')->middleware('auth')->name('subject/update'); // subject/update
        Route::post('subject/delete', 'deleteRecord')->middleware('auth')->name('subject/delete'); // subject/delete
        Route::post('subjects/bulk-delete', 'bulkDelete')->middleware('auth')->name('subjects.bulk-delete'); // bulk delete
        Route::get('subject/edit/{subject_id}', 'subjectEdit')->middleware('auth'); // subject/edit/page
    });

    // ----------------------- exams -----------------------------//
    Route::controller(ExamController::class)->group(function () {
        Route::get('exam/list/page', 'ExamList')->middleware('auth')->name('exam/list/page');
        Route::get('exam/add/page', 'ExamAdd')->middleware('auth')->name('exam/add/page');
    });


    // ----------------------- invoice -----------------------------//
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('invoice/list/page', 'invoiceList')->middleware('auth')->name('invoice/list/page'); // subjeinvoicect/list/page
        Route::get('invoice/paid/page', 'invoicePaid')->middleware('auth')->name('invoice/paid/page'); // invoice/paid/page
        Route::get('invoice/overdue/page', 'invoiceOverdue')->middleware('auth')->name('invoice/overdue/page'); // invoice/overdue/page
        Route::get('invoice/draft/page', 'invoiceDraft')->middleware('auth')->name('invoice/draft/page'); // invoice/draft/page
        Route::get('invoice/recurring/page', 'invoiceRecurring')->middleware('auth')->name('invoice/recurring/page'); // invoice/recurring/page
        Route::get('invoice/cancelled/page', 'invoiceCancelled')->middleware('auth')->name('invoice/cancelled/page'); // invoice/cancelled/page
        Route::get('invoice/grid/page', 'invoiceGrid')->middleware('auth')->name('invoice/grid/page'); // invoice/grid/page
        Route::get('invoice/add/page', 'invoiceAdd')->middleware('auth')->name('invoice/add/page'); // invoice/add/page
        Route::post('invoice/add/save', 'saveRecord')->middleware('auth')->name('invoice/add/save'); // invoice/add/save
        Route::post('invoice/update/save', 'updateRecord')->middleware('auth')->name('invoice/update/save'); // invoice/update/save
        Route::post('invoice/delete', 'deleteRecord')->middleware('auth')->name('invoice/delete'); // invoice/delete
        Route::get('invoice/edit/{invoice_id}', 'invoiceEdit')->middleware('auth')->name('invoice/edit/page'); // invoice/edit/page
        Route::get('invoice/view/{invoice_id}', 'invoiceView')->middleware('auth')->name('invoice/view/page'); // invoice/view/page
        Route::get('invoice/settings/page', 'invoiceSettings')->middleware('auth')->name('invoice/settings/page'); // invoice/settings/page
        Route::get('invoice/settings/tax/page', 'invoiceSettingsTax')->middleware('auth')->name('invoice/settings/tax/page'); // invoice/settings/tax/page
        Route::get('invoice/settings/bank/page', 'invoiceSettingsBank')->middleware('auth')->name('invoice/settings/bank/page'); // invoice/settings/bank/page
    });

   // ----------------------- accounts ----------------------------//
    Route::controller(AccountsController::class)->group(function () {
        Route::get('account/fees/collections/page', 'index')->middleware('auth')->name('account/fees/collections/page');
        Route::get('add/fees/collection/page', 'addFeesCollection')->middleware('auth')->name('add/fees/collection/page');
        Route::post('fees/collection/save', 'saveRecord')->middleware('auth')->name('fees/collection/save');
        Route::get('student/search', 'search')->middleware('auth')->name('student.search');
        Route::get('student/fees-info/{id}', 'getFeesInfo')->middleware('auth')->name('student.fees.info');
    });

    // ----------------------- exams ----------------------------//
    Route::controller(ExamController::class)->group(function () {
        Route::get('exams', 'index')->middleware('auth')->name('exams.page');
        Route::get('add/exam/page', 'addExam')->middleware('auth')->name('add/exam/page');
        Route::post('exam/create', 'createExam')->middleware('auth')->name('exam.create');
        Route::get('exam/edit/{id}', 'edit')->middleware('auth')->name('exam.edit');
        Route::put('exams/{id}', 'update')->middleware('auth')->name('exam.update');
        Route::delete('exams/{id}', 'destroy')->middleware('auth')->name('exam.destroy');
        Route::get('exam/enter-marks', 'enterMarks')->middleware('auth')->name('exam.enter-marks');
        Route::post('exam/save-marks', 'saveMarks')->middleware('auth')->name('exam.save-marks');
        Route::get('exam/view-results', 'viewResults')->middleware('auth')->name('exam.view-results');
        Route::delete('exam/delete-group', 'deleteGroup')->middleware('auth')->name('exam.delete-group');
        Route::post('exams/bulk-delete', 'bulkDelete')->middleware('auth')->name('exams.bulk-delete'); // bulk delete exams
        Route::post('exams/get-ids-by-groups', 'getExamIdsByGroups')->middleware('auth')->name('exams.get-ids-by-groups'); // get exam IDs by groups
        Route::get('exam/results/entry', 'resultsEntry')->middleware('auth')->name('exam.results.entry');
        Route::post('exam/results/save', 'resultsSave')->middleware('auth')->name('exam.results.save');
    });

    // ----------------------- attendance ----------------------------//
    Route::controller(AttendanceController::class)->group(function () {
        Route::get('attendance', 'index')->middleware('auth')->name('attendance.index');
        Route::post('attendance', 'store')->middleware('auth')->name('attendance.store');
        Route::get('attendance/reports', 'reports')->middleware('auth')->name('attendance.reports');
    });

    // ----------------------- timetable ----------------------------//
    Route::controller(TimetableController::class)->group(function () {
        Route::get('timetable', 'index')->middleware('auth')->name('timetable.index');
        Route::get('timetable/create', 'create')->middleware('auth')->name('timetable.create');
        Route::post('timetable', 'store')->middleware('auth')->name('timetable.store');
        Route::delete('timetable/{classId}', 'destroy')->middleware('auth')->name('timetable.destroy');
        Route::get('timetable/get-teacher', 'getTeacherForSubject')->middleware('auth')->name('timetable.get-teacher');
        Route::post('timetable/check-collision', 'checkTeacherCollision')->middleware('auth')->name('timetable.check-collision');
    });

    // ----------------------- report cards ----------------------------//
    Route::controller(ReportCardController::class)->group(function () {
        Route::get('report-cards', 'index')->middleware('auth')->name('report-cards.index');
        Route::get('report-cards/generate/{studentId}', 'generate')->middleware('auth')->name('report-cards.generate');
        Route::get('report-cards/transcript/{studentId}', 'transcript')->middleware('auth')->name('report-cards.transcript');
    });

    // ----------------------- employers ----------------------------//
    Route::controller(EmployerController::class)->group(function () {
        Route::get('employers', 'index')->middleware('auth')->name('employers.index');
        Route::get('employers/create', 'create')->middleware('auth')->name('employers.create');
        Route::post('employers', 'store')->middleware('auth')->name('employers.store');
        Route::get('employers/{id}', 'show')->middleware('auth')->name('employers.show');
        Route::get('employers/{id}/edit', 'edit')->middleware('auth')->name('employers.edit');
        Route::put('employers/{id}', 'update')->middleware('auth')->name('employers.update');
        Route::delete('employers/{id}', 'destroy')->middleware('auth')->name('employers.destroy');
    });

    // ----------------------- salary ----------------------------//
    Route::controller(SalaryController::class)->group(function () {
        Route::get('account/salary', 'index')->middleware('auth')->name('account/salary');
        Route::get('account/salary/create', 'create')->middleware('auth')->name('account/salary/create');
        Route::post('account/salary', 'store')->middleware('auth')->name('account/salary/store');
        Route::get('account/salary/search-staff', 'searchStaff')->middleware('auth')->name('account/salary/search-staff');
    });

    // ----------------------- expenses ----------------------------//
    Route::controller(ExpenseController::class)->group(function () {
        Route::get('account/expenses', 'index')->middleware('auth')->name('account/expenses');
        Route::get('account/expenses/create', 'create')->middleware('auth')->name('account/expenses/create');
        Route::post('account/expenses', 'store')->middleware('auth')->name('account/expenses/store');
    });

    // ----------------------- finance overview ----------------------------//
    Route::controller(AccountsController::class)->group(function () {
        Route::get('account/finance/overview', 'financeOverview')->middleware('auth')->name('account/finance/overview');
        Route::get('account/finance/export-balance', 'exportStudentsByBalance')->middleware('auth')->name('account/finance/export-balance');
    });

    // ----------------------- payments ----------------------------//
    Route::controller(PaymentController::class)->group(function () {
        Route::get('payments/student/{studentId}', 'showPaymentForm')->middleware('auth')->name('payments.create');
        Route::post('payments/initiate', 'initiatePayment')->middleware('auth')->name('payments.initiate');
        Route::get('payments/success', 'paymentSuccess')->middleware('auth')->name('payments.success');
        Route::get('payments/failure/{transactionId}', 'paymentFailure')->middleware('auth')->name('payments.failure');
        Route::get('payments/receipt/{transactionId}', 'receipt')->middleware('auth')->name('payments.receipt');
        Route::get('payments/receipt/{transactionId}/download', 'downloadReceipt')->middleware('auth')->name('payments.receipt.download');
        Route::get('payments/mpesa/status/{transactionId}', 'checkMpesaStatus')->middleware('auth')->name('payments.mpesa.status');
        Route::post('payments/webhook', 'webhook')->name('payments.webhook'); // Stripe webhook
        Route::post('payments/daraja/callback', 'darajaCallback')->name('payments.daraja.callback'); // M-Pesa callback (no auth)
    });

    // ----------------------- notifications ----------------------------//
    Route::controller(NotificationController::class)->group(function () {
        Route::get('notifications', 'index')->middleware('auth')->name('notifications.index');
        Route::get('notifications/unread-count', 'unreadCount')->middleware('auth')->name('notifications.unread-count');
        Route::get('notifications/recent', 'recent')->middleware('auth')->name('notifications.recent');
        Route::post('notifications/{id}/read', 'markAsRead')->middleware('auth')->name('notifications.mark-read');
        Route::post('notifications/mark-all-read', 'markAllAsRead')->middleware('auth')->name('notifications.mark-all-read');
        Route::delete('notifications/{id}', 'destroy')->middleware('auth')->name('notifications.destroy');
    });

    // ----------------------- events ----------------------------//
    Route::controller(EventController::class)->group(function () {
        Route::get('events', 'index')->middleware('auth')->name('events.index');
        Route::get('events/json', 'getEvents')->middleware('auth')->name('events.json');
        Route::get('events/create', 'create')->middleware('auth')->name('events.create');
        Route::post('events', 'store')->middleware('auth')->name('events.store');
        Route::get('events/{id}', 'show')->middleware('auth')->name('events.show');
        Route::get('events/{id}/edit', 'edit')->middleware('auth')->name('events.edit');
        Route::put('events/{id}', 'update')->middleware('auth')->name('events.update');
        Route::delete('events/{id}', 'destroy')->middleware('auth')->name('events.destroy');
        Route::post('events/bulk-delete', 'bulkDelete')->middleware('auth')->name('events.bulk-delete'); // bulk delete events
    });

    // ----------------------- transport ----------------------------//
    Route::controller(TransportController::class)->group(function () {
        // Buses
        Route::get('transport/buses', 'buses')->middleware('auth')->name('transport.buses');
        Route::get('transport/buses/create', 'createBus')->middleware('auth')->name('transport.buses.create');
        Route::post('transport/buses', 'storeBus')->middleware('auth')->name('transport.buses.store');
        Route::get('transport/buses/{id}/edit', 'editBus')->middleware('auth')->name('transport.buses.edit');
        Route::put('transport/buses/{id}', 'updateBus')->middleware('auth')->name('transport.buses.update');
        Route::delete('transport/buses/{id}', 'destroyBus')->middleware('auth')->name('transport.buses.destroy');

        // Routes
        Route::get('transport/routes', 'routes')->middleware('auth')->name('transport.routes');
        Route::get('transport/routes/create', 'createRoute')->middleware('auth')->name('transport.routes.create');
        Route::post('transport/routes', 'storeRoute')->middleware('auth')->name('transport.routes.store');
        Route::get('transport/routes/{id}/edit', 'editRoute')->middleware('auth')->name('transport.routes.edit');
        Route::put('transport/routes/{id}', 'updateRoute')->middleware('auth')->name('transport.routes.update');
        Route::delete('transport/routes/{id}', 'destroyRoute')->middleware('auth')->name('transport.routes.destroy');

        // Route Stops
        Route::post('transport/routes/{routeId}/stops', 'storeStop')->middleware('auth')->name('transport.stops.store');
        Route::put('transport/stops/{id}', 'updateStop')->middleware('auth')->name('transport.stops.update');
        Route::delete('transport/stops/{id}', 'destroyStop')->middleware('auth')->name('transport.stops.destroy');
        Route::get('transport/routes/{routeId}/stops', 'getRouteStops')->middleware('auth')->name('transport.stops.get');

        // Student Assignments
        Route::get('transport/assignments', 'assignments')->middleware('auth')->name('transport.assignments');
        Route::get('transport/assignments/create', 'createAssignment')->middleware('auth')->name('transport.assignments.create');
        Route::post('transport/assignments', 'storeAssignment')->middleware('auth')->name('transport.assignments.store');
        Route::put('transport/assignments/{id}', 'updateAssignment')->middleware('auth')->name('transport.assignments.update');
        Route::delete('transport/assignments/{id}', 'destroyAssignment')->middleware('auth')->name('transport.assignments.destroy');
    });


});
