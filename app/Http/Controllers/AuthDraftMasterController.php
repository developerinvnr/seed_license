<?php

namespace App\Http\Controllers;

use App\Models\AuthDraftMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AuthDraftMasterController extends Controller
{
    public function index()
    {
        $drafts = AuthDraftMaster::all();
        $fields = $this->getDynamicFields();

        return view('license.auth_draft_master', compact('drafts', 'fields'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'input_fields' => 'nullable|array',
        ]);

        try {
            AuthDraftMaster::create([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'input_fields' => $validated['input_fields'],
            ]);

            return redirect()->route('auth-draft-master')->with('success', 'Draft created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $draft = AuthDraftMaster::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'input_fields' => 'nullable|array',
        ]);

        try {
            $draft->update([
                'title' => $validated['title'],
                'content' => $validated['content'],
                'input_fields' => $validated['input_fields'],
            ]);

            return redirect()->route('auth-draft-master')->with('success', 'Draft updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    protected function getDynamicFields()
    {
        $columns = Schema::getColumnListing('responsible_masters');
        $fields = [];
        $excludedColumns = ['id', 'created_at', 'updated_at'];

        foreach ($columns as $column) {
            if (!in_array($column, $excludedColumns)) {
                $label = Str::of($column)->replace('_', ' ')->title();
                $fields[$column] = $label;
            }
        }

        return $fields;
    }
}