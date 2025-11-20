<?php

use Illuminate\Support\Facades\Route;
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

Route::group(['middleware'=>'auth'],function()
{
    Route::get('home',function()
    {
        return view('home');
    });
    Route::get('home',function()
    {
        return view('home');
    });
});

Auth::routes();
Route::group(['namespace' => 'App\Http\Controllers\Auth'],function()
{
    // ----------------------------login ------------------------------//
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authenticate');
        Route::get('/logout', 'logout')->name('logout');
        Route::post('change/password', 'changePassword')->name('change/password');
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
        Route::get('user/profile/page', 'userProfile')->middleware('auth')->name('user/profile/page');
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
    Route::controller(Setting::class)->group(function () {
        Route::get('setting/page', 'index')->middleware('auth')->name('setting/page');
    });

    // ------------------------ student -------------------------------//
    Route::controller(StudentController::class)->group(function () {
        Route::get('student/list', 'student')->middleware('auth')->name('student/list'); // list student
        Route::get('student/grid', 'studentGrid')->middleware('auth')->name('student/grid'); // grid student
        Route::get('student/add/page', 'studentAdd')->middleware('auth')->name('student/add/page'); // page student
        Route::post('student/add/save', 'studentSave')->middleware('auth')->name('student/add/save'); // save record student
        Route::get('student/edit/{id}', 'studentEdit')->middleware('auth')->name('student/edit'); // view for edit
        Route::post('student/update', 'studentUpdate')->middleware('auth')->name('student/update'); // update record student
        Route::post('student/delete', 'studentDelete')->middleware('auth')->name('student/delete'); // delete record student
        Route::get('student/profile/{id}', 'studentProfile')->middleware('auth'); // profile student
        Route::post('student/{student}/terms', 'storeTerm')->middleware('auth')->name('student.terms.store');
        Route::post('student/{student}/terms/{term}/payment', 'recordTermPayment')->middleware('auth')->name('student.terms.payment');
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
        Route::get('teacher/profile/{id}', 'teacherProfile')->middleware('auth')->name('teacher/profile'); // teacher profile
    });

    // ----------------------- department -----------------------------//
    Route::controller(DepartmentController::class)->group(function () {
        Route::get('department/list/page', 'departmentList')->middleware('auth')->name('department/list/page'); // department/list/page
        Route::get('department/add/page', 'indexDepartment')->middleware('auth')->name('department/add/page'); // page add department
        Route::get('department/edit/{department_id}', 'editDepartment')->middleware('auth'); // page add department
        Route::post('department/save', 'saveRecord')->middleware('auth')->name('department/save'); // department/save
        Route::post('department/update', 'updateRecord')->middleware('auth')->name('department/update'); // department/update
        Route::post('department/delete', 'deleteRecord')->middleware('auth')->name('department/delete'); // department/delete
        Route::get('get-data-list', 'getDataList')->middleware('auth')->name('get-data-list'); // get data list

    });

    // ----------------------- subject -----------------------------//
    Route::controller(SubjectController::class)->group(function () {
        Route::get('subject/list/page', 'subjectList')->middleware('auth')->name('subject/list/page'); // subject/list/page
        Route::get('subject/add/page', 'subjectAdd')->middleware('auth')->name('subject/add/page'); // subject/add/page
        Route::post('subject/save', 'saveRecord')->middleware('auth')->name('subject.save'); // subject/save
        Route::post('subject/update', 'updateRecord')->middleware('auth')->name('subject/update'); // subject/update
        Route::post('subject/delete', 'deleteRecord')->middleware('auth')->name('subject/delete'); // subject/delete
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
        Route::post('exam/save', 'saveExam')->middleware('auth')->name('exam.save');
        Route::get('exam/edit/{id}', 'edit')->middleware('auth')->name('exam.edit');
        Route::put('exams/{id}', 'update')->middleware('auth')->name('exam.update');
        Route::delete('exams/{id}', 'destroy')->middleware('auth')->name('exam.destroy');
        Route::get('exam/enter-marks', 'enterMarks')->middleware('auth')->name('exam.enter-marks');
        Route::post('exam/save-marks', 'saveMarks')->middleware('auth')->name('exam.save-marks');
        Route::get('exam/view-results', 'viewResults')->middleware('auth')->name('exam.view-results');
        Route::delete('exam/delete-group', 'deleteGroup')->middleware('auth')->name('exam.delete-group');
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
    });


});
