<?php

namespace App\Http\Controllers;

use App\Models\LicenseLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class LicenseController extends Controller
{
    public function label()
    {
        $labels = DB::table('license_labels')->get();
        $subFields = DB::table('license_label_sub_fields')->get();
        return view('license.label', compact('labels', 'subFields'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'label_name' => 'required|string|max:255',
        ]);
        $labelId = DB::table('license_labels')->insertGetId([
            'label_name' => $request->label_name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tableName = 'label_' . $labelId;
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('licenses_id')->nullable();
            });
        }

        return redirect()->route('license_label')->with('success', 'Label and table created successfully!');
    }

    public function edit($id)
    {
        $label = DB::table('license_labels')->where('id', $id)->first();
        return response()->json($label);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'label_name' => 'required|string|max:255',
        ]);

        DB::table('license_labels')
            ->where('id', $id)
            ->update(['label_name' => $request->label_name]);

        return redirect()->route('license_label')->with('success', 'Label updated successfully!');
    }

    // License Label Sub Field
    public function labelSubField()
    {
        $subFields = DB::table('license_label_sub_fields')->get();
        return view('license.license_label_sub_field', compact('subFields'));
    }

    public function storeLabelSubField(Request $request)
    {
        $request->validate([
            'field_name' => 'required|string|max:255|unique:license_label_sub_fields,field_name',
            'input_type' => 'required|in:text,select,date,upload',
            'table_name' => 'nullable|required_if:input_type,select',
            'column_name' => 'nullable|required_if:input_type,select',
        ]);

        DB::table('license_label_sub_fields')->insert([
            'field_name' => $request->field_name,
            'input_type' => $request->input_type,
            'table_name' => $request->input_type === 'select' ? $request->table_name : null,
            'column_name' => $request->input_type === 'select' ? $request->column_name : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Field added successfully.');
    }

    public function updateLabelSubField(Request $request)
    {
        $request->validate([
            'edit_id' => 'required|integer',
            'edit_field_name' => 'required|string|max:255',
            'edit_input_type' => 'required|in:text,select,date,upload',
            'edit_table_name' => 'nullable|string|max:255',
            'edit_column_name' => 'nullable|string|max:255',
        ]);

        DB::table('license_label_sub_fields')
            ->where('id', $request->edit_id)
            ->update([
                'field_name' => $request->edit_field_name,
                'input_type' => $request->edit_input_type,
                'table_name' => $request->edit_input_type === 'select' ? $request->edit_table_name : null,
                'column_name' => $request->edit_input_type === 'select' ? $request->edit_column_name : null,
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Field updated successfully.');
    }

    // License Label + Sub Field Mapping
    public function mapSubFields(Request $request)
    {
        $labelId = $request->label_id;
        $subFieldIds = $request->sub_field_ids ?? [];

        DB::table('label_sub_field_map')->where('label_id', $labelId)->delete();

        foreach ($subFieldIds as $subFieldId) {
            DB::table('label_sub_field_map')->insert([
                'label_id' => $labelId,
                'sub_field_id' => $subFieldId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $tableName = 'label_' . $labelId;

        if (Schema::hasTable($tableName)) {
            foreach ($subFieldIds as $subFieldId) {
                $columnName = 'field_name_' . $subFieldId;
                if (!Schema::hasColumn($tableName, $columnName)) {
                    Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                        $table->text($columnName)->nullable();
                    });
                }
            }
        }

        return redirect()->back()->with('success', 'Sub fields mapped successfully.');
    }

    public function getMappedSubFields($id)
    {
        $mapped = DB::table('label_sub_field_map')
            ->where('label_id', $id)
            ->pluck('sub_field_id');

        return response()->json($mapped);
    }

    public function getTableColumns($table)
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($table);
        return response()->json($columns);
    }

    public function getLabelsBySubField($subFieldId)
    {
        $labels = DB::table('label_sub_field_map')
            ->join('license_labels', 'label_sub_field_map.label_id', '=', 'license_labels.id')
            ->where('label_sub_field_map.sub_field_id', $subFieldId)
            ->select('license_labels.label_name')
            ->get();

        return response()->json($labels);
    }
}
