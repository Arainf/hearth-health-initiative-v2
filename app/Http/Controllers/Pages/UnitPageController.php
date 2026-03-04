<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use App\Models\Unit;
use App\Models\Unit_group;
use App\Models\VOffice;
use App\Services\DropdownService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UnitPageController extends Controller
{
    public object $trash;
    public string $token;
    public string $module;

    public function __construct()
    {
        $this->module = 'unit';
        $this->trash = new trashController;
        // This remains for initial page load and redirects if needed
        $this->token = $this->trash->encrypt($this->module);
    }

    public function index(Request $request, $token)
    {
        $user = auth()->user();
        if (!$user) return redirect('unauthorized');

        $MODULE_NAME = [
            'icon' => 'building-2',
            'label' => 'Units'
        ];

        $unitGroups = DropdownService::offices();

        // If AJAX request for modal content or data processing
        if ($request->query('id') || $request->query('mode') || $request->input('id') || $request->input('mode')) {
            return $this->menu($request);
        }

        return view('pages.unit', [
            'MODULE_NAME' => $MODULE_NAME,
            'TOKEN' => $token,
            'UNIT' => $unitGroups,
        ]);
    }

    public function menu(Request $request)
    {
        $rawMode = $request->query('mode') ?? $request->input('mode');
        $rawId   = $request->query('id') ?? $request->input('id');

        $mode = $rawMode ? $this->trash->decrypt($rawMode) : null;
        $id   = $rawId   ? $this->trash->decrypt($rawId)   : null;

        return match ($mode) {
            'store'  => $this->store($request),
            'update' => $this->update($request, $id),
            'delete' => $this->destroy($request, $id),
            default  => abort(404),
        };
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'unit_name'       => 'required|string|max:150',
            'unit_abbr'       => 'required|string|max:50',
            'unit_group_code' => 'required|exists:unit_group,unit_group_code',
        ]);

        try {
            $latestId = Unit::max('id') ?? 0;
            $nextId = $latestId + 1;

            $cleanAbbr  = Str::upper(str_replace(' ', '', $validated['unit_abbr']));
            $cleanGroup = Str::upper(str_replace(' ', '', $validated['unit_group_code']));

            // Loop logic: Repeat string then take first 4 (ALP -> ALPA)
            $partAbbr  = substr(str_repeat($cleanAbbr, 4), 0, 4);
            $partGroup = substr(str_repeat($cleanGroup, 4), 0, 4);

            $generatedCode = $partAbbr . $partGroup . $nextId;

            Unit::create([
                'unit_code'       => $generatedCode,
                'unit_name'       => $validated['unit_name'],
                'unit_abbr'       => $validated['unit_abbr'],
                'unit_group_code' => $validated['unit_group_code'],
            ]);

            return response()->json([
                'success' => true,
                'message' => "Unit $generatedCode created successfully!"
            ]);

        } catch (\Exception $e) {
            return response()->json(['errors' => ['main' => $e->getMessage()]], 422);
        }
    }

    public function update(Request $request, $id = null)
    {
        // Decrypt ID from input if not in URL
        $targetId = $id ?? $this->trash->decrypt($request->input('id'));

        $validated = $request->validate([
            'unit_name'       => 'required|string|max:100',
            'unit_abbr'       => 'required|string|max:50',
            'unit_group_code' => 'required|exists:unit_group,unit_group_code',
        ]);

        try {
            $unit = Unit::where('unit_code', $targetId)->firstOrFail();
            $unit->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Unit updated successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json(['errors' => ['main' => $e->getMessage()]], 422);
        }
    }

    public function table(Request $request)
    {
        if (!$request->ajax()) abort(404);
        return response()->json(VOffice::datatable($request));
    }

    public function destroy(Request $request, $id = null)
    {
        // Decrypt ID from input if not in URL
        $targetId = $id ?? $this->trash->decrypt($request->input('id'));

        try {
            $unit = Unit::where('unit_code', $targetId)->firstOrFail();
            $unit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unit deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Deletion failed'], 500);
        }
    }
}
