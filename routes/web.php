<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Employee\ProjectController as EmployeeProjectController;
use App\Http\Controllers\Employee\InventoryController as EmployeeInventoryController;
use App\Http\Controllers\Employee\ProfileController as EmployeeProfileController;
use App\Http\Controllers\Employee\TeamController as EmployeeTeamController;

Route::get('/', function () {
    return redirect()->route('projects.index');
});

Route::middleware(['auth'])->group(function () {
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'employee'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/home', function () {
        return redirect()->route('employee.projects.employeedashboard');
    })->name('home');

    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/', [EmployeeTeamController::class, 'index'])->name('index');
        Route::post('/projects/{projectId}/submit-report', [EmployeeTeamController::class, 'submitReport'])->name('submit-report');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [EmployeeProjectController::class, 'dashboard'])->name('employeedashboard');
        Route::get('/{id}', [EmployeeProjectController::class, 'show'])->name('show');
    });

    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [EmployeeInventoryController::class, 'index'])->name('index');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [EmployeeProfileController::class, 'show'])->name('show');
    });
});

Route::middleware(['auth', 'admin'])->group(function () {

    // Projects
    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('/create', [ProjectController::class, 'create'])->name('create');
        Route::post('/', [ProjectController::class, 'store'])->name('store');
        Route::get('/{id}', [ProjectController::class, 'show'])->name('show');
        Route::put('/{id}', [ProjectController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProjectController::class, 'destroy'])->name('destroy');
    });

    // Team
    Route::prefix('team')->name('team.')->group(function () {
        Route::get('/', [TeamController::class, 'index'])->name('index');
        Route::get('/documents', [TeamController::class, 'documents'])->name('documents');
        Route::get('/payroll', [TeamController::class, 'payroll'])->name('payroll');
        Route::get('/assign', [TeamController::class, 'assign'])->name('assign');
        Route::get('/attendance', [TeamController::class, 'attendance'])->name('attendance');

        Route::post('/attendance/{id}/approve', [TeamController::class, 'approveAttendance'])->name('attendance.approve');
        Route::post('/attendance/{id}/reject', [TeamController::class, 'rejectAttendance'])->name('attendance.reject');

        Route::post('/projects/{projectId}/assign', [TeamController::class, 'assignToProject'])->name('assign-to-project');

        Route::post('/update-requests/{id}/approve', [TeamController::class, 'approveUpdateRequest'])->name('update-requests.approve');
        Route::post('/update-requests/{id}/reject', [TeamController::class, 'rejectUpdateRequest'])->name('update-requests.reject');

        Route::get('/members/list', [TeamController::class, 'listMembers'])->name('members.list');
    });

    // Finance
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('index');
        Route::post('/payroll/{id}/complete', [FinanceController::class, 'completePayroll'])->name('payroll.complete');
        Route::post('/expense/{id}/complete', [FinanceController::class, 'completeExpense'])->name('expense.complete');
        Route::post('/manual-record', [FinanceController::class, 'storeManualRecord'])->name('manual-record.store');
        Route::post('/funds/add', [FinanceController::class, 'addFunds'])->name('funds.add');
    });

    // Account (Create Employee Login)
    Route::prefix('admin/account')->name('admin.account.')->group(function () {
        Route::get('/create', [AccountController::class, 'create'])->name('create');
        Route::post('/create', [AccountController::class, 'store'])->name('store');
    });

    // Inventory (IMS)
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/stock-inout', [InventoryController::class, 'stockInOut'])->name('stock-inout');
        Route::get('/threshold', [InventoryController::class, 'threshold'])->name('threshold');
        Route::get('/history', [InventoryController::class, 'history'])->name('history');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('low-stock');

        Route::post('/add-stock', [InventoryController::class, 'addStock'])->name('add-stock');
        Route::post('/use-stock', [InventoryController::class, 'useStock'])->name('use-stock');
        Route::post('/update-threshold', [InventoryController::class, 'updateThreshold'])->name('update-threshold');
    });

    // ✅ NEW Materials (MMS) — REPLACEMENT BLOCK
    Route::prefix('materials')->name('materials.')->group(function () {

        Route::get('/', [MaterialController::class, 'overview'])->name('overview');

        // create flow
        Route::get('/create', [MaterialController::class, 'chooseSupplier'])->name('create');
        Route::get('/supplier/{supplier}', [MaterialController::class, 'supplierProducts'])->name('supplier.products');
        Route::post('/supplier/{supplier}/products', [MaterialController::class, 'addSupplierProduct'])->name('supplier.products.store');
        Route::post('/suppliers', [MaterialController::class, 'storeSupplier'])->name('suppliers.store');

        // cart (session)
        Route::post('/cart/add', [MaterialController::class, 'cartAdd'])->name('cart.add');
        Route::post('/cart/remove', [MaterialController::class, 'cartRemove'])->name('cart.remove');
        Route::get('/cart', [MaterialController::class, 'cartView'])->name('cart.view');
        Route::post('/cart/checkout', [MaterialController::class, 'cartCheckout'])->name('cart.checkout');

        // crud actions
        Route::post('/{material}/update', [MaterialController::class, 'update'])->name('update');
        Route::post('/{material}/delete', [MaterialController::class, 'delete'])->name('delete');

        // history
        Route::get('/history', [MaterialController::class, 'history'])->name('history');
    });

});

require __DIR__ . '/auth.php';
