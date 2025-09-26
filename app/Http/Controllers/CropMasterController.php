<?php

namespace App\Http\Controllers;

use App\Models\CropMaster;
use App\Models\CoreVertical;
use App\Models\CoreCrop;
use App\Models\CoreVariety;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CropMasterController extends Controller
{
    public function index()
    {
        $cropMasters = CropMaster::with(['vertical', 'crop', 'variety'])->get();
        $verticals = CoreVertical::where('is_active', '1')->get();

        $crops = CoreCrop::select('core_crop.id', 'core_crop.crop_name', 'core_crop.vertical_id')
            ->join('core_vertical', 'core_vertical.id', '=', 'core_crop.vertical_id')
            ->where('core_vertical.is_active', '1')
            ->where('core_crop.is_active', '1')
            ->get()
            ->map(function ($crop) {
                return [
                    'id' => (int) $crop->id,
                    'crop_name' => $crop->crop_name ?? 'Unknown Crop',
                    'vertical_id' => (int) $crop->vertical_id,
                ];
            })->toArray();

        $varieties = CoreVariety::where('is_active', '1')->get()->map(function ($variety) {
            return [
                'id' => (int) $variety->id,
                'variety_name' => $variety->variety_name ?? 'Unknown Variety',
                'crop_id' => (int) $variety->crop_id,
            ];
        })->toArray();

        return view('license.crop-master', compact('cropMasters', 'verticals', 'crops', 'varieties'));
    }
    public function getCrops($vertical_id)
    {
        return response()->json(
            CoreCrop::where('is_active', 1)
                ->where('vertical_id', $vertical_id)
                ->select('id', 'crop_name')
                ->get()
        );
    }

    public function getVarieties($crop_id)
    {
        return response()->json(
            CoreVariety::where('is_active', 1)
                ->where('crop_id', $crop_id)
                ->select('id', 'variety_name')
                ->get()
        );
    }


    public function store(Request $request)
    {
        $request->validate([
            'crop_vertical_id' => 'required|exists:core_vertical,id',
            'crop_id' => 'required|exists:core_crop,id',
            'crop_variety_id' => 'required|exists:core_variety,id',
        ]);

        CropMaster::create([
            'crop_vertical_id' => (int) $request->crop_vertical_id,
            'crop_id' => (int) $request->crop_id,
            'crop_variety_id' => (int) $request->crop_variety_id,
        ]);

        return redirect()->route('crop-master')->with('success', 'Crop Master added successfully.');
    }

    public function destroy($id)
    {
        $cropMaster = CropMaster::findOrFail($id);
        $cropMaster->delete();
        return redirect()->route('crop-master')->with('success', 'Crop Master deleted successfully.');
    }
}
