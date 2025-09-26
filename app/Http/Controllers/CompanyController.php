<?php

// namespace App\Http\Controllers;
// use Illuminate\Http\Request;
// use App\Models\CoreCompany;
// use App\Models\CompanyMasterDetail;
// use App\Models\CompanyMasterPersonDetail;
// use App\Models\CompanyDocument;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Str;
// class CompanyController extends Controller
// {
//     public function index()
//     {
//         $companies = CompanyMasterDetail::with('documents', 'directors')->get();
//         return view('license.company', compact('companies'));
//     }

//     public function fetchCompanies()
//     {
//         try {
//             $coreCompanies = CoreCompany::select('company_name', 'company_code', 'registration_number', 'gst_number')->get();
//             return response()->json($coreCompanies);
//         } catch (\Exception $e) {
//             return response()->json(['error' => 'API request failed: ' . $e->getMessage()], 500);
//         }
//     }

//     public function store(Request $request)
//     {
//         $request->validate([
//             'company_name' => 'required|string|max:255',
//             'company_code' => 'nullable|string|max:100',
//             'registration_number' => 'nullable|string|max:100',
//             'gst_number' => 'nullable|string|max:100',
//             'directors' => 'required|array|min:1',
//             'directors.*.name' => 'required|string|max:255',
//             'directors.*.designation' => 'required|string|max:255',
//             'directors.*.din' => 'nullable|string|max:20',
//             'directors.*.pan' => 'nullable|string|max:20',
//             'directors.*.aadhaar' => 'nullable|string|max:20',
//             'directors.*.contact_number' => 'nullable|string|max:20',
//             'directors.*.email' => 'nullable|email|max:255',
//             'directors.*.appointment_date' => 'nullable|date',
//             'directors.*.resignation_date' => 'nullable|date',
//             'documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
//             'directors.*.documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
//         ]);

//         // Store company details
//         $company = CompanyMasterDetail::create([
//             'company_name' => $request->company_name,
//             'company_code' => $request->company_code,
//             'registration_number' => $request->registration_number,
//             'gst_number' => $request->gst_number,
//             'company_id' => 'com0', // Temporary, will be updated after save
//         ]);

//         // Update company_id
//         $company->company_id = 'com' . $company->id;
//         $company->save();

//         // Store directors
//         foreach ($request->directors as $index => $director) {
//             $directorData = CompanyMasterPersonDetail::create([
//                 'company_id' => $company->company_id,
//                 'name' => $director['name'],
//                 'designation' => $director['designation'],
//                 'din' => $director['din'] ?? null,
//                 'pan' => $director['pan'] ?? null,
//                 'aadhaar' => $director['aadhaar'] ?? null,
//                 'contact_number' => $director['contact_number'] ?? null,
//                 'email' => $director['email'] ?? null,
//                 'appointment_date' => $director['appointment_date'] ?? null,
//                 'resignation_date' => $director['resignation_date'] ?? null,
//             ]);

//             // Store director documents
//             if (isset($director['documents']) && is_array($director['documents'])) {
//                 foreach ($director['documents'] as $key => $file) {
//                     if ($file) {
//                         $path = $file->store('director_documents', 'public');
//                         CompanyDocument::create([
//                             'company_id' => $company->id,
//                             'director_id' => $directorData->id,
//                             'document_type' => $key,
//                             'file_path' => $path,
//                         ]);
//                     }
//                 }
//             }
//         }

//         // Store company documents
//         if ($request->hasFile('documents')) {
//             foreach ($request->file('documents') as $key => $file) {
//                 if ($file && $key !== 'other_docs_name') {
//                     $path = $file->store('company_documents', 'public');
//                     CompanyDocument::create([
//                         'company_id' => $company->id,
//                         'director_id' => $directorData->id,
//                         'document_type' => $key,
//                         'file_path' => $path,
//                     ]);
//                 }
//             }
//         }

//         return redirect()->route('company')->with('success', 'Company added successfully.');
//     }

//     public function edit($id)
//     {
//         $company = CompanyMasterDetail::with('documents', 'directors')->findOrFail($id);
//         $companyDocuments = $company->documents->whereNull('director_id')->map(function ($doc) {
//             return [
//                 'id' => $doc->id,
//                 'document_type' => $doc->document_type,
//                 'file_path' => $doc->file_path,
//             ];
//         })->values();

//         $directors = $company->directors->map(function ($director) {
//             $documents = CompanyDocument::where('director_id', $director->id)->pluck('file_path', 'document_type')->toArray();
//             return [
//                 'id' => $director->id,
//                 'name' => $director->name,
//                 'designation' => $director->designation,
//                 'din' => $director->din,
//                 'pan' => $director->pan,
//                 'aadhaar' => $director->aadhaar,
//                 'contact_number' => $director->contact_number,
//                 'email' => $director->email,
//                 'appointment_date' => $director->appointment_date,
//                 'resignation_date' => $director->resignation_date,
//                 'documents' => $documents,
//             ];
//         });

//         return response()->json([
//             'company' => $company,
//             'company_documents' => $companyDocuments,
//             'directors' => $directors,
//         ]);
//     }

//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'company_name' => 'required|string|max:255',
//             'company_code' => 'nullable|string|max:100',
//             'registration_number' => 'nullable|string|max:100',
//             'gst_number' => 'nullable|string|max:100',
//             'directors' => 'required|array|min:1',
//             'directors.*.name' => 'required|string|max:255',
//             'directors.*.designation' => 'required|string|max:255',
//             'directors.*.din' => 'nullable|string|max:20',
//             'directors.*.pan' => 'nullable|string|max:20',
//             'directors.*.aadhaar' => 'nullable|string|max:20',
//             'directors.*.contact_number' => 'nullable|string|max:20',
//             'directors.*.email' => 'nullable|email|max:255',
//             'directors.*.appointment_date' => 'nullable|date',
//             'directors.*.resignation_date' => 'nullable|date',
//             'documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
//             'directors.*.documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
//         ]);

//         $company = CompanyMasterDetail::findOrFail($id);
//         $company->update([
//             'company_name' => $request->company_name,
//             'company_code' => $request->company_code,
//             'registration_number' => $request->registration_number,
//             'gst_number' => $request->gst_number,
//         ]);

//         // Delete existing directors and their documents
//         CompanyMasterPersonDetail::where('company_id', $company->company_id)->delete();
//         CompanyDocument::where('company_id', $company->id)->whereNotNull('director_id')->delete();

//         // Store new directors
//         foreach ($request->directors as $index => $director) {
//             $directorData = CompanyMasterPersonDetail::create([
//                 'company_id' => $company->company_id,
//                 'name' => $director['name'],
//                 'designation' => $director['designation'],
//                 'din' => $director['din'] ?? null,
//                 'pan' => $director['pan'] ?? null,
//                 'aadhaar' => $director['aadhaar'] ?? null,
//                 'contact_number' => $director['contact_number'] ?? null,
//                 'email' => $director['email'] ?? null,
//                 'appointment_date' => $director['appointment_date'] ?? null,
//                 'resignation_date' => $director['resignation_date'] ?? null,
//             ]);

//             // Store director documents
//             if (isset($director['documents']) && is_array($director['documents'])) {
//                 foreach ($director['documents'] as $key => $file) {
//                     if ($file) {
//                         $path = $file->store('director_documents', 'public');
//                         CompanyDocument::create([
//                             'company_id' => $company->id,
//                             'director_id' => $directorData->id,
//                             'document_type' => $key,
//                             'file_path' => $path,
//                         ]);
//                     }
//                 }
//             }
//         }

//         // Update company documents
//         if ($request->hasFile('documents')) {
//             foreach ($request->file('documents') as $key => $file) {
//                 if ($file && $key !== 'other_docs_name') {
//                     // Delete existing document of the same type
//                     CompanyDocument::where('company_id', $company->id)
//                         ->where('document_type', $key)
//                         ->whereNull('director_id')
//                         ->delete();
//                     $path = $file->store('company_documents', 'public');
//                     CompanyDocument::create([
//                         'company_id' => $company->id,
//                         'document_type' => $key,
//                         'file_path' => $path,
//                     ]);
//                 }
//             }
//         }

//         return redirect()->route('company')->with('success', 'Company updated successfully.');
//     }

//     public function destroy($id)
//     {
//         Log::info('Delete Request:', ['company_id' => $id]);
//         DB::beginTransaction();
//         try {
//             $company = CompanyMasterDetail::findOrFail($id);
//             CompanyMasterPersonDetail::where('company_id', $company->company_id)->delete();
//             $documents = CompanyDocument::where('company_id', $company->id)->get();
//             foreach ($documents as $document) {
//                 Storage::disk('public')->delete($document->file_path);
//                 $document->delete();
//             }
//             $company->delete();

//             DB::commit();
//             Log::info('Company Deleted:', ['company_id' => $id]);
//             return redirect()->route('company')->with('success', 'Company deleted successfully.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Delete Failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
//             return redirect()->back()->withErrors(['error' => 'Failed to delete company: ' . $e->getMessage()]);
//         }
//     }

//     /**
//      * Delete a specific company document.
//      */
//     public function destroyDocument($id)
//     {
//         Log::info('Delete Document Request:', ['document_id' => $id]);
//         try {
//             $document = CompanyDocument::findOrFail($id);
//             Storage::disk('public')->delete($document->file_path);
//             $document->delete();

//             return response()->json(['success' => 'Document deleted successfully.']);
//         } catch (\Exception $e) {
//             Log::error('Delete Document Failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
//             return response()->json(['error' => 'Failed to delete document: ' . $e->getMessage()], 500);
//         }
//     }

//     /**
//      * Delete a specific director document.
//      */
//     public function destroyDirectorDocument($directorId, $documentType)
//     {
//         Log::info('Delete Director Document Request:', ['director_id' => $directorId, 'document_type' => $documentType]);
//         try {
//             $document = CompanyDocument::where('director_id', $directorId)
//                 ->where('document_type', $documentType)
//                 ->firstOrFail();
//             Storage::disk('public')->delete($document->file_path);
//             $document->delete();

//             return response()->json(['success' => 'Director document deleted successfully.']);
//         } catch (\Exception $e) {
//             Log::error('Delete Director Document Failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
//             return response()->json(['error' => 'Failed to delete director document: ' . $e->getMessage()], 500);
//         }
//     }
// }

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\CoreCompany;
// use App\Models\CompanyMasterDetail;
// use App\Models\CompanyMasterPersonDetail;
// use App\Models\CompanyDocument;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Str;

// class CompanyController extends Controller
// {
//     public function index()
//     {
//         $companies = CompanyMasterDetail::with('documents', 'directors')->get();
//         return view('license.company', compact('companies'));
//     }

//     public function fetchCompanies()
//     {
//         try {
//             $coreCompanies = CoreCompany::select('company_name', 'company_code', 'registration_number', 'gst_number')->get();
//             return response()->json($coreCompanies);
//         } catch (\Exception $e) {
//             Log::error('Fetch Companies Failed:', ['error' => $e->getMessage()]);
//             return response()->json(['error' => 'Failed to fetch companies: ' . $e->getMessage()], 500);
//         }
//     }

//     public function store(Request $request)
//     {
//         // Log request data for debugging
//         Log::info('Store Request:', $request->all());
//         Log::info('Files:', $request->allFiles());

//         // Validate request
//         $request->validate([
//             'company_name' => 'required|string|max:255',
//             'company_code' => 'nullable|string|max:100',
//             'registration_number' => 'nullable|string|max:100',
//             'gst_number' => 'nullable|string|max:100',
//             'directors' => 'required|array|min:1',
//             'directors.*.name' => 'required|string|max:255',
//             'directors.*.designation' => 'required|string|max:255',
//             'directors.*.din' => 'nullable|string|max:20',
//             'directors.*.pan' => 'nullable|string|max:20',
//             'directors.*.aadhaar' => 'nullable|string|max:20',
//             'directors.*.contact_number' => 'nullable|string|max:20',
//             'directors.*.email' => 'nullable|email|max:255',
//             'directors.*.appointment_date' => 'nullable|date',
//             'directors.*.resignation_date' => 'nullable|date',
//             'documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
//             'directors.*.documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
//         ]);

//         DB::beginTransaction();
//         try {
//             // Create company
//             $company = CompanyMasterDetail::create([
//                 'company_name' => $request->company_name,
//                 'company_code' => $request->company_code,
//                 'registration_number' => $request->registration_number,
//                 'gst_number' => $request->gst_number,
//                 'company_id' => 'com0',                                                                     
//             ]);

//             // Update company_id
//             $company->company_id = 'com' . $company->id;
//             $company->save();

//             // Store company documents
//             if ($request->hasFile('documents')) {
//                 foreach ($request->file('documents') as $key => $file) {
//                     if ($file && $file->isValid() && $key !== 'other_docs_name') {
//                         $path = $file->store('company_documents', 'public');
//                         CompanyDocument::create([
//                             'company_id' => $company->id,
//                             'director_id' => null, 
//                             'document_type' => $key,
//                             'file_path' => $path,
//                         ]);
//                         Log::info('Company Document Stored:', ['type' => $key, 'path' => $path]);
//                     }
//                 }
//                 // Handle other_docs separately
//                 if ($request->hasFile('documents.other_docs') && $request->filled('other_docs_name')) {
//                     $file = $request->file('documents.other_docs');
//                     if ($file->isValid()) {
//                         $path = $file->store('company_documents', 'public');
//                         CompanyDocument::create([
//                             'company_id' => $company->id,
//                             'director_id' => null,
//                             'document_type' => 'other_docs_' . Str::slug($request->other_docs_name),
//                             'file_path' => $path,
//                         ]);
//                         Log::info('Other Document Stored:', ['type' => 'other_docs_' . $request->other_docs_name, 'path' => $path]);
//                     }
//                 }
//             }

//             // Store directors and their documents
//             foreach ($request->directors as $index => $director) {
//                 $directorData = CompanyMasterPersonDetail::create([
//                     'company_id' => $company->company_id,
//                     'name' => $director['name'],
//                     'designation' => $director['designation'],
//                     'din' => $director['din'] ?? null,
//                     'pan' => $director['pan'] ?? null,
//                     'aadhaar' => $director['aadhaar'] ?? null,
//                     'contact_number' => $director['contact_number'] ?? null,
//                     'email' => $director['email'] ?? null,
//                     'appointment_date' => $director['appointment_date'] ?? null,
//                     'resignation_date' => $director['resignation_date'] ?? null,
//                 ]);

//                 // Store director documents
//                 if (isset($director['documents']) && is_array($director['documents'])) {
//                     foreach ($director['documents'] as $key => $file) {
//                         if ($file && $file->isValid()) {
//                             $path = $file->store('director_documents', 'public');
//                             CompanyDocument::create([
//                                 'company_id' => $company->id,
//                                 'director_id' => $directorData->id,
//                                 'document_type' => $key,
//                                 'file_path' => $path,
//                             ]);
//                             Log::info('Director Document Stored:', [
//                                 'director_id' => $directorData->id,
//                                 'type' => $key,
//                                 'path' => $path
//                             ]);
//                         }
//                     }
//                 }
//             }

//             DB::commit();
//             return redirect()->route('company')->with('success', 'Company added successfully.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Store Failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
//             return redirect()->back()->withErrors(['error' => 'Failed to add company: ' . $e->getMessage()]);
//         }
//     }

//     public function edit($id)
//     {
//         try {
//             $company = CompanyMasterDetail::with('documents', 'directors')->findOrFail($id);
//             $companyDocuments = $company->documents->whereNull('director_id')->map(function ($doc) {
//                 return [
//                     'id' => $doc->id,
//                     'document_type' => $doc->document_type,
//                     'file_path' => $doc->file_path,
//                 ];
//             })->values();

//             $directors = $company->directors->map(function ($director) {
//                 $documents = CompanyDocument::where('director_id', $director->id)->pluck('file_path', 'document_type')->toArray();
//                 return [
//                     'id' => $director->id,
//                     'name' => $director->name,
//                     'designation' => $director->designation,
//                     'din' => $director->din,
//                     'pan' => $director->pan,
//                     'aadhaar' => $director->aadhaar,
//                     'contact_number' => $director->contact_number,
//                     'email' => $director->email,
//                     'appointment_date' => $director->appointment_date,
//                     'resignation_date' => $director->resignation_date,
//                     'documents' => $documents,
//                 ];
//             });

//             return response()->json([
//                 'company' => $company,
//                 'company_documents' => $companyDocuments,
//                 'directors' => $directors,
//             ]);
//         } catch (\Exception $e) {
//             Log::error('Edit Failed:', ['error' => $e->getMessage()]);
//             return response()->json(['error' => 'Failed to fetch company: ' . $e->getMessage()], 500);
//         }
//     }

//     public function update(Request $request, $id)
//     {
//         // Log request data for debugging
//         Log::info('Update Request:', $request->all());
//         Log::info('Files:', $request->allFiles());

//         $request->validate([
//             'company_name' => 'required|string|max:255',
//             'company_code' => 'nullable|string|max:100',
//             'registration_number' => 'nullable|string|max:100',
//             'gst_number' => 'nullable|string|max:100',
//             'directors' => 'required|array|min:1',
//             'directors.*.name' => 'required|string|max:255',
//             'directors.*.designation' => 'required|string|max:255',
//             'directors.*.din' => 'nullable|string|max:20',
//             'directors.*.pan' => 'nullable|string|max:20',
//             'directors.*.aadhaar' => 'nullable|string|max:20',
//             'directors.*.contact_number' => 'nullable|string|max:20',
//             'directors.*.email' => 'nullable|email|max:255',
//             'directors.*.appointment_date' => 'nullable|date',
//             'directors.*.resignation_date' => 'nullable|date',
//             'documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
//             'directors.*.documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
//         ]);

//         DB::beginTransaction();
//         try {
//             $company = CompanyMasterDetail::findOrFail($id);
//             $company->update([
//                 'company_name' => $request->company_name,
//                 'company_code' => $request->company_code,
//                 'registration_number' => $request->registration_number,
//                 'gst_number' => $request->gst_number,
//             ]);

//             // Delete existing directors and their documents
//             CompanyMasterPersonDetail::where('company_id', $company->company_id)->delete();
//             CompanyDocument::where('company_id', $company->id)->whereNotNull('director_id')->delete();

//             // Store new directors and their documents
//             foreach ($request->directors as $index => $director) {
//                 $directorData = CompanyMasterPersonDetail::create([
//                     'company_id' => $company->company_id,
//                     'name' => $director['name'],
//                     'designation' => $director['designation'],
//                     'din' => $director['din'] ?? null,
//                     'pan' => $director['pan'] ?? null,
//                     'aadhaar' => $director['aadhaar'] ?? null,
//                     'contact_number' => $director['contact_number'] ?? null,
//                     'email' => $director['email'] ?? null,
//                     'appointment_date' => $director['appointment_date'] ?? null,
//                     'resignation_date' => $director['resignation_date'] ?? null,
//                 ]);

//                 // Store director documents
//                 if (isset($director['documents']) && is_array($director['documents'])) {
//                     foreach ($director['documents'] as $key => $file) {
//                         if ($file && $file->isValid()) {
//                             $path = $file->store('director_documents', 'public');
//                             CompanyDocument::create([
//                                 'company_id' => $company->id,
//                                 'director_id' => $directorData->id,
//                                 'document_type' => $key,
//                                 'file_path' => $path,
//                             ]);
//                             Log::info('Director Document Updated:', [
//                                 'director_id' => $directorData->id,
//                                 'type' => $key,
//                                 'path' => $path
//                             ]);
//                         }
//                     }
//                 }
//             }

//             // Update company documents
//             if ($request->hasFile('documents')) {
//                 foreach ($request->file('documents') as $key => $file) {
//                     if ($file && $file->isValid() && $key !== 'other_docs_name') {
//                         // Delete existing document of the same type
//                         CompanyDocument::where('company_id', $company->id)
//                             ->where('document_type', $key)
//                             ->whereNull('director_id')
//                             ->delete();
//                         $path = $file->store('company_documents', 'public');
//                         CompanyDocument::create([
//                             'company_id' => $company->id,
//                             'director_id' => null,
//                             'document_type' => $key,
//                             'file_path' => $path,
//                         ]);
//                         Log::info('Company Document Updated:', ['type' => $key, 'path' => $path]);
//                     }
//                 }
//                 // Handle other_docs separately
//                 if ($request->hasFile('documents.other_docs') && $request->filled('other_docs_name')) {
//                     $file = $request->file('documents.other_docs');
//                     if ($file->isValid()) {
//                         // Delete existing other_docs if any
//                         CompanyDocument::where('company_id', $company->id)
//                             ->where('document_type', 'like', 'other_docs_%')
//                             ->whereNull('director_id')
//                             ->delete();
//                         $path = $file->store('company_documents', 'public');
//                         CompanyDocument::create([
//                             'company_id' => $company->id,
//                             'director_id' => null,
//                             'document_type' => 'other_docs_' . Str::slug($request->other_docs_name),
//                             'file_path' => $path,
//                         ]);
//                         Log::info('Other Document Updated:', ['type' => 'other_docs_' . $request->other_docs_name, 'path' => $path]);
//                     }
//                 }
//             }

//             DB::commit();
//             return redirect()->route('company')->with('success', 'Company updated successfully.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Update Failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
//             return redirect()->back()->withErrors(['error' => 'Failed to update company: ' . $e->getMessage()]);
//         }
//     }

//     public function destroy($id)
//     {
//         Log::info('Delete Request:', ['company_id' => $id]);
//         DB::beginTransaction();
//         try {
//             $company = CompanyMasterDetail::findOrFail($id);
//             CompanyMasterPersonDetail::where('company_id', $company->company_id)->delete();
//             $documents = CompanyDocument::where('company_id', $company->id)->get();
//             foreach ($documents as $document) {
//                 Storage::disk('public')->delete($document->file_path);
//                 $document->delete();
//             }
//             $company->delete();

//             DB::commit();
//             Log::info('Company Deleted:', ['company_id' => $id]);
//             return redirect()->route('company')->with('success', 'Company deleted successfully.');
//         } catch (\Exception $e) {
//             DB::rollBack();
//             Log::error('Delete Failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
//             return redirect()->back()->withErrors(['error' => 'Failed to delete company: ' . $e->getMessage()]);
//         }
//     }

//     public function destroyDocument($id)
//     {
//         Log::info('Delete Document Request:', ['document_id' => $id]);
//         try {
//             $document = CompanyDocument::findOrFail($id);
//             Storage::disk('public')->delete($document->file_path);
//             $document->delete();
//             return response()->json(['success' => 'Document deleted successfully.']);
//         } catch (\Exception $e) {
//             Log::error('Delete Document Failed:', ['error' => $e->getMessage()]);
//             return response()->json(['error' => 'Failed to delete document: ' . $e->getMessage()], 500);
//         }
//     }

//     public function destroyDirectorDocument($directorId, $documentType)
//     {
//         Log::info('Delete Director Document Request:', ['director_id' => $directorId, 'document_type' => $documentType]);
//         try {
//             $document = CompanyDocument::where('director_id', $directorId)
//                 ->where('document_type', $documentType)
//                 ->firstOrFail();
//             Storage::disk('public')->delete($document->file_path);
//             $document->delete();
//             return response()->json(['success' => 'Director document deleted successfully.']);
//         } catch (\Exception $e) {
//             Log::error('Delete Director Document Failed:', ['error' => $e->getMessage()]);
//             return response()->json(['error' => 'Failed to delete director document: ' . $e->getMessage()], 500);
//         }
//     }
// }

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CoreCompany;
use App\Models\CompanyMasterDetail;
use App\Models\CompanyMasterPersonDetail;
use App\Models\CompanyDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = CompanyMasterDetail::with('documents', 'directors')->get();
        return view('license.company', compact('companies'));
    }

    public function fetchCompanies()
    {
        try {
            $coreCompanies = CoreCompany::select('company_name', 'company_code', 'registration_number', 'gst_number')->get();
            return response()->json($coreCompanies);
        } catch (\Exception $e) {
            Log::error('Fetch Companies Failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch companies: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        Log::info('Store Request:', $request->all());
        Log::info('Files:', $request->allFiles());

        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:100',
            'directors' => 'required|array|min:1',
            'directors.*.name' => 'required|string|max:255',
            'directors.*.designation' => 'required|string|max:255',
            'directors.*.din' => 'nullable|string|max:20',
            'directors.*.pan' => 'nullable|string|max:20',
            'directors.*.aadhaar' => 'nullable|string|max:20',
            'directors.*.contact_number' => 'nullable|string|max:20',
            'directors.*.email' => 'nullable|email|max:255',
            'directors.*.appointment_date' => 'nullable|date',
            'directors.*.resignation_date' => 'nullable|date',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'directors.*.documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $company = CompanyMasterDetail::create([
                'company_name' => $request->company_name,
                'company_code' => $request->company_code,
                'registration_number' => $request->registration_number,
                'gst_number' => $request->gst_number,
                'company_id' => 'com0',
            ]);

            $company->company_id = 'com' . $company->id;
            $company->save();

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $key => $file) {
                    if ($file && $file->isValid() && $key !== 'other_docs_name') {
                        $path = $file->store('company_documents', 'public');
                        CompanyDocument::create([
                            'company_id' => $company->id,
                            'document_type' => $key,
                            'file_path' => $path,
                        ]);
                        Log::info('Company Document Stored:', ['type' => $key, 'path' => $path]);
                    }
                }
                if ($request->hasFile('documents.other_docs') && $request->filled('other_docs_name')) {
                    $file = $request->file('documents.other_docs');
                    if ($file->isValid()) {
                        $path = $file->store('company_documents', 'public');
                        CompanyDocument::create([
                            'company_id' => $company->id,
                            'document_type' => 'other_docs_' . Str::slug($request->other_docs_name),
                            'file_path' => $path,
                        ]);
                        Log::info('Other Document Stored:', ['type' => 'other_docs_' . $request->other_docs_name, 'path' => $path]);
                    }
                }
            }

            foreach ($request->directors as $index => $director) {
                $directorData = [
                    'company_id' => $company->company_id,
                    'name' => $director['name'],
                    'designation' => $director['designation'],
                    'din' => $director['din'] ?? null,
                    'pan' => $director['pan'] ?? null,
                    'aadhaar' => $director['aadhaar'] ?? null,
                    'contact_number' => $director['contact_number'] ?? null,
                    'email' => $director['email'] ?? null,
                    'appointment_date' => $director['appointment_date'] ?? null,
                    'resignation_date' => $director['resignation_date'] ?? null,
                ];

                if (isset($director['documents']) && is_array($director['documents'])) {
                    foreach ($director['documents'] as $key => $file) {
                        if ($file && $file->isValid()) {
                            $path = $file->store('director_documents', 'public');
                            $directorData[$key] = $path;
                            Log::info('Director Document Stored:', [
                                'type' => $key,
                                'path' => $path
                            ]);
                        }
                    }
                }

                CompanyMasterPersonDetail::create($directorData);
            }

            DB::commit();
            return redirect()->route('company')->with('success', 'Company added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Failed to add company: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $company = CompanyMasterDetail::with('documents', 'directors')->findOrFail($id);
            $companyDocuments = $company->documents->whereNull('director_id')->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'document_type' => $doc->document_type,
                    'file_path' => $doc->file_path,
                ];
            })->values();

            $directors = $company->directors->map(function ($director) {
                return [
                    'id' => $director->id,
                    'name' => $director->name,
                    'designation' => $director->designation,
                    'din' => $director->din,
                    'pan' => $director->pan,
                    'aadhaar' => $director->aadhaar,
                    'contact_number' => $director->contact_number,
                    'email' => $director->email,
                    'appointment_date' => $director->appointment_date,
                    'resignation_date' => $director->resignation_date,
                    'documents' => [
                        'aadhar_doc' => $director->aadhar_doc,
                        'pan_doc' => $director->pan_doc,
                        'passport_doc' => $director->passport_doc,
                        'driving_license_doc' => $director->driving_license_doc,
                        'bank_passbook_doc' => $director->bank_passbook_doc,
                    ],
                ];
            });

            return response()->json([
                'company' => $company,
                'company_documents' => $companyDocuments,
                'directors' => $directors,
            ]);
        } catch (\Exception $e) {
            Log::error('Edit Failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch company: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Update Request:', $request->all());
        Log::info('Files:', $request->allFiles());

        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:100',
            'directors' => 'required|array|min:1',
            'directors.*.name' => 'required|string|max:255',
            'directors.*.designation' => 'required|string|max:255',
            'directors.*.din' => 'nullable|string|max:20',
            'directors.*.pan' => 'nullable|string|max:20',
            'directors.*.aadhaar' => 'nullable|string|max:20',
            'directors.*.contact_number' => 'nullable|string|max:20',
            'directors.*.email' => 'nullable|email|max:255',
            'directors.*.appointment_date' => 'nullable|date',
            'directors.*.resignation_date' => 'nullable|date',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'directors.*.documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $company = CompanyMasterDetail::findOrFail($id);
            $company->update([
                'company_name' => $request->company_name,
                'company_code' => $request->company_code,
                'registration_number' => $request->registration_number,
                'gst_number' => $request->gst_number,
            ]);

            $existingDirectors = CompanyMasterPersonDetail::where('company_id', $company->company_id)->get();
            foreach ($existingDirectors as $director) {
                foreach (['aadhar_doc', 'pan_doc', 'passport_doc', 'driving_license_doc', 'bank_passbook_doc'] as $doc) {
                    if ($director->$doc) {
                        Storage::disk('public')->delete($director->$doc);
                    }
                }
            }
            CompanyMasterPersonDetail::where('company_id', $company->company_id)->delete();

            foreach ($request->directors as $index => $director) {
                $directorData = [
                    'company_id' => $company->company_id,
                    'name' => $director['name'],
                    'designation' => $director['designation'],
                    'din' => $director['din'] ?? null,
                    'pan' => $director['pan'] ?? null,
                    'aadhaar' => $director['aadhaar'] ?? null,
                    'contact_number' => $director['contact_number'] ?? null,
                    'email' => $director['email'] ?? null,
                    'appointment_date' => $director['appointment_date'] ?? null,
                    'resignation_date' => $director['resignation_date'] ?? null,
                ];

                if (isset($director['documents']) && is_array($director['documents'])) {
                    foreach ($director['documents'] as $key => $file) {
                        if ($file && $file->isValid()) {
                            $path = $file->store('director_documents', 'public');
                            $directorData[$key] = $path;
                            Log::info('Director Document Updated:', [
                                'type' => $key,
                                'path' => $path
                            ]);
                        }
                    }
                }

                CompanyMasterPersonDetail::create($directorData);
            }

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $key => $file) {
                    if ($file && $file->isValid() && $key !== 'other_docs_name') {
                        CompanyDocument::where('company_id', $company->id)
                            ->where('document_type', $key)
                            ->whereNull('director_id')
                            ->delete();
                        $path = $file->store('company_documents', 'public');
                        CompanyDocument::create([
                            'company_id' => $company->id,
                            'director_id' => null,
                            'document_type' => $key,
                            'file_path' => $path,
                        ]);
                        Log::info('Company Document Updated:', ['type' => $key, 'path' => $path]);
                    }
                }
                if ($request->hasFile('documents.other_docs') && $request->filled('other_docs_name')) {
                    $file = $request->file('documents.other_docs');
                    if ($file->isValid()) {
                        CompanyDocument::where('company_id', $company->id)
                            ->where('document_type', 'like', 'other_docs_%')
                            ->whereNull('director_id')
                            ->delete();
                        $path = $file->store('company_documents', 'public');
                        CompanyDocument::create([
                            'company_id' => $company->id,
                            'director_id' => null,
                            'document_type' => 'other_docs_' . Str::slug($request->other_docs_name),
                            'file_path' => $path,
                        ]);
                        Log::info('Other Document Updated:', ['type' => 'other_docs_' . $request->other_docs_name, 'path' => $path]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('company')->with('success', 'Company updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Failed to update company: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        Log::info('Delete Request:', ['company_id' => $id]);
        DB::beginTransaction();
        try {
            $company = CompanyMasterDetail::findOrFail($id);
            $directors = CompanyMasterPersonDetail::where('company_id', $company->company_id)->get();
            foreach ($directors as $director) {
                foreach (['aadhar_doc', 'pan_doc', 'passport_doc', 'driving_license_doc', 'bank_passbook_doc'] as $doc) {
                    if ($director->$doc) {
                        Storage::disk('public')->delete($director->$doc);
                    }
                }
            }
            CompanyMasterPersonDetail::where('company_id', $company->company_id)->delete();
            $documents = CompanyDocument::where('company_id', $company->id)->get();
            foreach ($documents as $document) {
                Storage::disk('public')->delete($document->file_path);
                $document->delete();
            }
            $company->delete();

            DB::commit();
            Log::info('Company Deleted:', ['company_id' => $id]);
            return redirect()->route('company')->with('success', 'Company deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Failed to delete company: ' . $e->getMessage()]);
        }
    }

    public function destroyDocument($id)
    {
        Log::info('Delete Document Request:', ['document_id' => $id]);
        try {
            $document = CompanyDocument::findOrFail($id);
            Storage::disk('public')->delete($document->file_path);
            $document->delete();
            return response()->json(['success' => 'Document deleted successfully.']);
        } catch (\Exception $e) {
            Log::error('Delete Document Failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete document: ' . $e->getMessage()], 500);
        }
    }

    public function destroyDirectorDocument($directorId, $documentType)
    {
        Log::info('Delete Director Document Request:', ['director_id' => $directorId, 'document_type' => $documentType]);
        try {
            $director = CompanyMasterPersonDetail::findOrFail($directorId);
            if ($director->$documentType) {
                Storage::disk('public')->delete($director->$documentType);
                $director->update([$documentType => null]);
                return response()->json(['success' => 'Director document deleted successfully.']);
            }
            return response()->json(['error' => 'Document not found.'], 404);
        } catch (\Exception $e) {
            Log::error('Delete Director Document Failed:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete director document: ' . $e->getMessage()], 500);
        }
    }
}