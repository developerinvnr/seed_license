<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LicenseTypeController;
use App\Http\Controllers\LicenseNameController;
use App\Http\Controllers\LicenseListController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResponsibleController;
use App\Http\Controllers\CoreController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\AuthDraftMasterController;
use App\Http\Controllers\CropMasterController;

Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* user */
    Route::group(['middleware' => ['permission:view-user']], function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
    });
    Route::group(['middleware' => ['permission:add-user']], function () {
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    });
    Route::group(['middleware' => ['permission:edit-user']], function () {
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    });

    //permission
    Route::group(['middleware' => ['permission:view-permission']], function () {
        Route::get('/permissions', [PermissionController::class, 'index'])->name('users.permission');
    });

    // Role management routes
    Route::group(['middleware' => ['permission:view-role']], function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('users.role');
    });
    Route::group(['middleware' => ['permission:add-role']], function () {
        Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
    });
    Route::group(['middleware' => ['permission:edit-role']], function () {
        Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    });
    Route::group(['middleware' => ['permission:delete-role']], function () {
        Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    /* license type */
    Route::group(['middleware' => ['permission:view-License Type']], function () {
        Route::get('/license-type', [LicenseTypeController::class, 'index'])->name('license-type');
    });
    Route::group(['middleware' => ['permission:add-License Type']], function () {
        Route::post('/license-type', [LicenseTypeController::class, 'store'])->name('license_type.store');
    });
    Route::group(['middleware' => ['permission:edit-License Type']], function () {
        Route::get('/license-type/edit/{id}', [LicenseTypeController::class, 'edit'])->name('license_type.edit');
        Route::post('/license-type/update', [LicenseTypeController::class, 'update'])->name('license_type.update');
    });

    /* license name */
    Route::group(['middleware' => ['permission:view-License Name']], function () {
        Route::get('/license-name', [LicenseNameController::class, 'index'])->name('license-name');
    });
    Route::group(['middleware' => ['permission:add-License Name']], function () {
        Route::post('/license-name', [LicenseNameController::class, 'store'])->name('license_name.store');
    });
    Route::group(['middleware' => ['permission:edit-License Name']], function () {
        Route::get('/license-name/edit/{id}', [LicenseNameController::class, 'edit'])->name('license_name.edit');
        Route::post('/license-name/update', [LicenseNameController::class, 'update'])->name('license_name.update');
    });
    Route::get('/license-name-fields/{id}', [LicenseNameController::class, 'getFields']);
    Route::get('/get-districts/{stateId}', [LicenseNameController::class, 'getDistricts']);
    Route::get('/get-city-villages/{districtId}', [LicenseNameController::class, 'getCityVillages']);
    Route::get('/get-sub-fields/{labelId}', [LicenseNameController::class, 'getSubFields'])->name('license_name.get_sub_fields');

    /* license list */
    Route::group(['middleware' => ['permission:view-Add License']], function () {
        Route::get('/license-list', [LicenseListController::class, 'index'])->name('license-list');
    });
    Route::group(['middleware' => ['permission:add-Add License']], function () {
        Route::post('/license/store', [LicenseListController::class, 'store'])->name('license.store');
    });
    Route::group(['middleware' => ['permission:edit-Add License']], function () {
        Route::put('/license/update', [LicenseListController::class, 'update'])->name('license.update');
        Route::post('/license/renew', [LicenseListController::class, 'renew'])->name('license.renew');
    });
    Route::get('/license-names/{licenseTypeId}', [LicenseListController::class, 'getLicenseNames']);
    Route::get('/license-address/{id}', [LicenseListController::class, 'getLicenseAddress']);
    Route::get('/get-license-details/{id}', [LicenseListController::class, 'getLicenseDetails']);
    // Route::get('/get-license-fields/{id}', [LicenseListController::class, 'getMappedFields']);
    Route::get('/license-mapped-fields/{id}', [LicenseListController::class, 'getMappedFields']);
    Route::post('/get-responsible-details', [LicenseListController::class, 'getResponsibleDetails'])->name('get-responsible-details');    
    Route::get('/send-reminder-emails', [LicenseListController::class, 'sendReminderEmails'])->name('send.reminder.emails');
    Route::get('/get-employee-details/{emp_id}', [LicenseListController::class, 'getEmployeeDetails'])->name('get-employee-details');
    Route::get('/license/{id}', [LicenseListController::class, 'popupDetails'])->name('licenses.popup');
    Route::get('/get-label-data/{licenseId}/{tableName}', [LicenseListController::class, 'getLabelData']);
    Route::get('/get-license-history/{licenseTypeId}/{licenseNameId}', [LicenseListController::class, 'getLicenseHistory'])->name('license.history');
    Route::get('/activity-log', [LicenseListController::class, 'activityLog'])->name('license.activity-log');
    Route::get('/get-districts/{stateId}', [LicenseListController::class, 'getDistricts']);
    Route::get('/get-city-villages/{districtId}', [LicenseListController::class, 'getCityVillages']);
    Route::get('/license-group-companies', [LicenseListController::class, 'getGroupCompanies'])->name('license.group-companies');
    Route::get('/check-license-name-responsible/{licenseNameId}', [LicenseListController::class, 'checkLicenseNameResponsible'])->name('check-license-name-responsible');
    

    // Responsible Master Routes
    Route::group(['middleware' => ['permission:view-company Responsible Person']], function () {
        Route::get('/responsible', [ResponsibleController::class, 'index'])->name('responsible');
        Route::post('/responsible', [ResponsibleController::class, 'store'])->name('responsible.store');
        Route::get('/custom_api/employees', [ResponsibleController::class, 'getEmployees'])->name('api.employees');
        Route::get('/custom_api/license-types', [ResponsibleController::class, 'getLicenseTypes'])->name('api.license-types');
        Route::get('/custom_api/groupcom-companies', [ResponsibleController::class, 'getGroupcomCompanies']);
        Route::get('/custom_api/groupcom-employees', [ResponsibleController::class, 'getGroupcomEmployees']);
        Route::get('/get-license-names/{licenseTypeId}', [ResponsibleController::class, 'getLicenseNames']);
        Route::put('/responsible/{responsible}', [ResponsibleController::class, 'update'])->name('responsible.update');
        Route::get('/responsible/get-draft-content/{id}', [ResponsibleController::class, 'getDraftContent']);
        Route::put('/responsible/{id}/certificate', [ResponsibleController::class, 'updateCertificate'])->name('responsible.updateCertificate');
        ('responsible.update');
        Route::get('/check-license-availability', [ResponsibleController::class, 'checkLicenseAvailability'])->name('checkLicenseAvailability');
        Route::get('/responsible/{id}/history', [ResponsibleController::class, 'getHistory'])->name('responsible.history');
        Route::get('/custom_api/license-types', function () {
            return response()->json(\App\Models\LicenseType::all());
        });
        Route::get('/get-license-names/{licenseTypeId}', function ($licenseTypeId) {
            return response()->json([
                'license_names' => \App\Models\LicenseName::where('license_type_id', $licenseTypeId)->get()
            ]);
        });
    });

    // Auth Draft Master Routes for Responsible Person
    Route::get('/auth-draft-master', [AuthDraftMasterController::class, 'index'])->name('auth-draft-master');
    Route::post('/auth-draft-master', [AuthDraftMasterController::class, 'store'])->name('auth-draft-master.store');
    Route::put('/auth-draft-master/{id}', [AuthDraftMasterController::class, 'update'])->name('auth-draft-master.update');

    //core data
    Route::group(['middleware' => ['permission:add-Core API Data']], function () {
        Route::get('/core', [CoreController::class, 'coreApis'])->name('core');
    });
    Route::group(['middleware' => ['permission:add-Core API Data']], function () {
        Route::get('/core/fetch-apis', [CoreController::class, 'fetchApis'])->name('core.fetch_apis');
        Route::get('/core/sync-apis', [CoreController::class, 'syncApis'])->name('core.sync_apis');
        Route::post('/core/sync-single-api', [CoreController::class, 'syncSingleApi'])->name('core.sync_single_api');
        Route::post('/core/get-api-data', [CoreController::class, 'getApiData'])->name('core.get_api_data');
        Route::post('/core/empty-table', [CoreController::class, 'emptyTable'])->name('core.empty_table');
        Route::post('/core/drop-table', [CoreController::class, 'dropTable'])->name('core.drop_table');
    });

    //license label
    Route::group(['middleware' => ['permission:view-License Label']], function () {
        Route::get('/license-label', [LicenseController::class, 'label'])->name('license_label');
    });
    Route::group(['middleware' => ['permission:add-License Label']], function () {
        Route::post('/license-label', [LicenseController::class, 'store'])->name('license_label.store');
    });
    Route::group(['middleware' => ['permission:edit-License Label']], function () {
        Route::get('/license-label/{id}/edit', [LicenseController::class, 'edit'])->name('license_label.edit');
        Route::put('/license-label/{id}', [LicenseController::class, 'update'])->name('license_label.update');
    });

    //sub label field 

    Route::group(['middleware' => ['permission:view-Lable sub field']], function () {
        Route::get('/license-label-sub-field', [LicenseController::class, 'labelSubField'])->name('license_label_sub_field');
    });
    Route::group(['middleware' => ['permission:add-Lable sub field']], function () {
        Route::post('/license-label-sub-field/store', [LicenseController::class, 'storeLabelSubField'])->name('store_label_sub_field');
    });
    Route::group(['middleware' => ['permission:edit-Lable sub field']], function () {
        Route::post('/license-label-sub-field/update', [LicenseController::class, 'updateLabelSubField'])->name('update_label_sub_field');
        Route::get('/get-table-columns/{table}', [LicenseController::class, 'getTableColumns']);
        Route::get('/get-columns/{table}', function ($table) {
            $columns = Schema::getColumnListing($table);
            return response()->json($columns);
        });
        Route::get('/license/get-labels-by-sub-field/{id}', [LicenseController::class, 'getLabelsBySubField']);
        Route::post('/license/map-sub-fields', [LicenseController::class, 'mapSubFields'])->name('map.sub.fields');
        Route::get('/license/get-mapped-sub-fields/{id}', [LicenseController::class, 'getMappedSubFields']);
    });

    // Company Master Routes
    Route::group(['middleware' => ['auth', 'permission:view-Company']], function () {
        Route::get('/company', [CompanyController::class, 'index'])->name('company');
        Route::get('/company/fetch_companies', [CompanyController::class, 'fetchCompanies'])->name('company.fetch_companies');
    });
    Route::group(['middleware' => ['auth', 'permission:add-Company']], function () {
        Route::post('/company/store', [CompanyController::class, 'store'])->name('company.store');
    });
    Route::group(['middleware' => ['auth', 'permission:edit-Company']], function () {
        Route::get('/company/edit/{id}', [CompanyController::class, 'edit'])->name('company.edit');
        Route::put('/company/update/{id}', [CompanyController::class, 'update'])->name('company.update');
    });
    Route::group(['middleware' => ['auth', 'permission:delete-Company']], function () {
        Route::delete('/company/destroy/{id}', [CompanyController::class, 'destroy'])->name('company.destroy');
        Route::delete('/company/document/{id}', [CompanyController::class, 'destroyDocument'])->name('company.document.destroy');
        Route::delete('/company/directors/document/{directorId}/{documentType}', [CompanyController::class, 'destroyDirectorDocument'])->name('company.directors.document.destroy');
        Route::delete('/company/document/{id}/{documentType}', [CompanyController::class, 'destroyDocument'])->name('company.document.destroy');
    });

    // Crop Master Routes
    Route::get('/crop-master', [CropMasterController::class, 'index'])->name('crop-master');
    Route::get('/get-crops/{vertical_id}', [CropMasterController::class, 'getCrops']);
    Route::get('/get-varieties/{crop_id}', [CropMasterController::class, 'getVarieties']);

    Route::get('/check-permission', function () {
    return \Spatie\Permission\Models\Permission::where('name', 'view-company Responsible Person')->get();
});
});

require __DIR__ . '/auth.php';
