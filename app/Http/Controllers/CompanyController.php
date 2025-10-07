<?php
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
        $companies = CompanyMasterDetail::with('directors')->get();
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
            $companyData = [
                'company_name' => $request->company_name,
                'company_code' => $request->company_code,
                'registration_number' => $request->registration_number,
                'gst_number' => $request->gst_number,
                'company_id' => 'com0',
            ];

            // Handle company documents
            if ($request->hasFile('documents')) {
                Log::info('Documents found in request', array_keys($request->file('documents')));
                foreach ($request->file('documents') as $key => $file) {
                    if ($file && $file->isValid()) {
                        $path = $file->store('company_documents', 'public');
                        $companyData[$key] = $path; // Map file path to the corresponding column
                        Log::info('Company Document Stored:', ['type' => $key, 'path' => $path]);
                    }
                }
                if ($request->hasFile('documents.other_docs')) {
                    $file = $request->file('documents.other_docs');
                    if ($file->isValid()) {
                        $path = $file->store('company_documents', 'public');
                        $companyData['other_docs'] = $path;
                        $companyData['other_docs_name'] = $request->filled('documents.other_docs_name')
                            ? $request->input('documents.other_docs_name')
                            : null;
                        Log::info('Other Document Stored:', ['type' => 'other_docs', 'path' => $path, 'name' => $companyData['other_docs_name']]);
                    }
                }
            } else {
                Log::info('No documents found in request');
            }

            $company = CompanyMasterDetail::create($companyData);

            $company->company_id = 'com' . $company->id;
            $company->save();

            // Handle director documents
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
            $company = CompanyMasterDetail::with('directors')->findOrFail($id);
            $companyDocuments = [
                'certificate_incorporation' => $company->certificate_incorporation,
                'company_pan_card' => $company->company_pan_card,
                'aoa' => $company->aoa,
                'moa' => $company->moa,
                'gst_certificate' => $company->gst_certificate,
                'board_resolution' => $company->board_resolution,
                'signature_specimen' => $company->signature_specimen,
                'other_docs' => $company->other_docs,
                'other_docs_name' => $company->other_docs_name,
            ];

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

    // public function update(Request $request, $id)
    // {
       
    //     $request->validate([
    //         'company_name' => 'required|string|max:255',
    //         'company_code' => 'nullable|string|max:100',
    //         'registration_number' => 'nullable|string|max:100',
    //         'gst_number' => 'nullable|string|max:100',
    //         'directors' => 'required|array|min:1',
    //         'directors.*.name' => 'required|string|max:255',
    //         'directors.*.designation' => 'required|string|max:255',
    //         'directors.*.din' => 'nullable|string|max:20',
    //         'directors.*.pan' => 'nullable|string|max:20',
    //         'directors.*.aadhaar' => 'nullable|string|max:20',
    //         'directors.*.contact_number' => 'nullable|string|max:20',
    //         'directors.*.email' => 'nullable|email|max:255',
    //         'directors.*.appointment_date' => 'nullable|date',
    //         'directors.*.resignation_date' => 'nullable|date',
    //         'documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
    //         'directors.*.documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         $company = CompanyMasterDetail::findOrFail($id);
    //         $company->update([
    //             'company_name' => $request->company_name,
    //             'company_code' => $request->company_code,
    //             'registration_number' => $request->registration_number,
    //             'gst_number' => $request->gst_number,
    //         ]);

    //         // Handle existing company documents deletion
    //         $existingDocs = [
    //             'certificate_incorporation', 'company_pan_card', 'aoa', 'moa', 'gst_certificate',
    //             'board_resolution', 'signature_specimen', 'other_docs'
    //         ];
    //         foreach ($existingDocs as $doc) {
    //             if ($company->$doc) {
    //                 Storage::disk('public')->delete($company->$doc);
    //                 $company->$doc = null;
    //             }
    //         }
    //         if ($company->other_docs_name && $company->other_docs) {
    //             Storage::disk('public')->delete($company->other_docs);
    //             $company->other_docs_name = null;
    //         }

    //         // Handle new company documents
    //         if ($request->hasFile('documents')) {
    //             foreach ($request->file('documents') as $key => $file) {
    //                 if ($file && $file->isValid() && $key !== 'other_docs_name') {
    //                     $path = $file->store('company_documents', 'public');
    //                     $company->$key = $path;
    //                     Log::info('Company Document Updated:', ['type' => $key, 'path' => $path]);
    //                 }
    //             }
    //             if ($request->hasFile('documents.other_docs')) {
    //                 $file = $request->file('documents.other_docs');
    //                 if ($file->isValid()) {
    //                     $path = $file->store('company_documents', 'public');
    //                     $company->other_docs = $path;
    //                     $company->other_docs_name = $request->filled('documents.other_docs_name')
    //                         ? $request->input('documents.other_docs_name')
    //                         : null;
    //                     Log::info('Other Document Updated:', ['type' => 'other_docs', 'path' => $path, 'name' => $company->other_docs_name]);
    //                 }
    //             }
    //         }
    //         $company->save();

    //         // Handle existing directors
    //         $existingDirectors = CompanyMasterPersonDetail::where('company_id', $company->company_id)->get();
    //         foreach ($existingDirectors as $director) {
    //             foreach (['aadhar_doc', 'pan_doc', 'passport_doc', 'driving_license_doc', 'bank_passbook_doc'] as $doc) {
    //                 if ($director->$doc) {
    //                     Storage::disk('public')->delete($director->$doc);
    //                 }
    //             }
    //         }
    //         CompanyMasterPersonDetail::where('company_id', $company->company_id)->delete();

    //         // Handle new directors
    //         foreach ($request->directors as $index => $director) {
    //             $directorData = [
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
    //             ];

    //             if (isset($director['documents']) && is_array($director['documents'])) {
    //                 foreach ($director['documents'] as $key => $file) {
    //                     if ($file && $file->isValid()) {
    //                         $path = $file->store('director_documents', 'public');
    //                         $directorData[$key] = $path;
    //                         Log::info('Director Document Updated:', [
    //                             'type' => $key,
    //                             'path' => $path
    //                         ]);
    //                     }
    //                 }
    //             }

    //             CompanyMasterPersonDetail::create($directorData);
    //         }

    //         DB::commit();
    //         return redirect()->route('company')->with('success', 'Company updated successfully.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Update Failed:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    //         return redirect()->back()->withErrors(['error' => 'Failed to update company: ' . $e->getMessage()]);
    //     }
    // }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_code' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:100',
            'tin' => 'nullable|string|max:100',
            'date_of_incorporation' => 'nullable|date',
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
            // Separate validation for document fields
            'documents.certificate_incorporation' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'documents.company_pan_card' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'documents.aoa' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'documents.moa' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'documents.gst_certificate' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'documents.board_resolution' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'documents.signature_specimen' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'documents.other_docs' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'documents.other_docs_name' => 'nullable|string|max:255',
            'directors.*.documents.*' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $company = CompanyMasterDetail::findOrFail($id);

            // Update company details
            $companyData = [
                'company_name' => $request->company_name,
                'company_code' => $request->company_code,
                'registration_number' => $request->registration_number,
                'gst_number' => $request->gst_number,
                'tin' => $request->tin,
                'date_of_incorporation' => $request->date_of_incorporation,
            ];

            // Handle company documents (only update if new files are provided)
            $existingDocs = [
                'certificate_incorporation',
                'company_pan_card',
                'aoa',
                'moa',
                'gst_certificate',
                'board_resolution',
                'signature_specimen',
                'other_docs',
            ];

            if ($request->hasFile('documents')) {
                foreach ($existingDocs as $key) {
                    if ($request->hasFile("documents.$key")) {
                        $file = $request->file("documents.$key");
                        if ($file && $file->isValid()) {
                            // Delete old file if it exists
                            if ($company->$key) {
                                Storage::disk('public')->delete($company->$key);
                            }
                            $path = $file->store('company_documents', 'public');
                            $companyData[$key] = $path;
                            Log::info('Company Document Updated:', ['type' => $key, 'path' => $path]);
                        }
                    }
                }
                // Handle other_docs_name
                if ($request->filled('documents.other_docs_name')) {
                    $companyData['other_docs_name'] = $request->input('documents.other_docs_name');
                } elseif (!$request->hasFile('documents.other_docs')) {
                    // If no new file and no new name, preserve existing name
                    $companyData['other_docs_name'] = $company->other_docs_name;
                }
            }

            // Update company
            $company->update($companyData);

            // Handle directors
            // Get existing directors
            $existingDirectors = CompanyMasterPersonDetail::where('company_id', $company->company_id)->get();
            $existingDirectorIds = $existingDirectors->pluck('id')->toArray();
            $newDirectorIds = [];

            // Process new/updated directors
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

                // Handle director documents
                if (isset($director['documents']) && is_array($director['documents'])) {
                    foreach ($director['documents'] as $key => $file) {
                        if ($file && $file->isValid()) {
                            $path = $file->store('director_documents', 'public');
                            $directorData[$key] = $path;
                            Log::info('Director Document Updated:', [
                                'type' => $key,
                                'path' => $path,
                            ]);
                        }
                    }
                }

                // Update or create director
                $directorModel = isset($director['id']) && in_array($director['id'], $existingDirectorIds)
                    ? CompanyMasterPersonDetail::find($director['id'])
                    : new CompanyMasterPersonDetail();

                // Delete old documents if new ones are uploaded
                if ($directorModel->exists && isset($director['documents'])) {
                    foreach (['aadhar_doc', 'pan_doc', 'passport_doc', 'driving_license_doc', 'bank_passbook_doc'] as $doc) {
                        if ($directorModel->$doc && isset($director['documents'][$doc]) && $director['documents'][$doc]) {
                            Storage::disk('public')->delete($directorModel->$doc);
                        }
                    }
                }

                $directorModel->fill($directorData)->save();
                $newDirectorIds[] = $directorModel->id;
            }

            // Delete directors that were removed
            $directorsToDelete = array_diff($existingDirectorIds, $newDirectorIds);
            foreach ($directorsToDelete as $directorId) {
                $director = CompanyMasterPersonDetail::find($directorId);
                if ($director) {
                    foreach (['aadhar_doc', 'pan_doc', 'passport_doc', 'driving_license_doc', 'bank_passbook_doc'] as $doc) {
                        if ($director->$doc) {
                            Storage::disk('public')->delete($director->$doc);
                        }
                    }
                    $director->delete();
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

            $existingDocs = [
                'certificate_incorporation', 'company_pan_card', 'aoa', 'moa', 'gst_certificate',
                'board_resolution', 'signature_specimen', 'other_docs'
            ];
            foreach ($existingDocs as $doc) {
                if ($company->$doc) {
                    Storage::disk('public')->delete($company->$doc);
                }
            }
            if ($company->other_docs) {
                Storage::disk('public')->delete($company->other_docs);
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