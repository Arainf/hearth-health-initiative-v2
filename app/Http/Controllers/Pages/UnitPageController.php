<?php
namespace App\Http\Controllers\Pages;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitPageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if(!$user) return redirect('unauthorized');

        // Dropdown options for Unit Group filter (from DB view)
        $unitGroups = DB::table('v_offices')
            ->select('unit_group_code', 'unit_group_name')
            ->whereNotNull('unit_group_code')
            ->whereNotNull('unit_group_name')
            ->distinct()
            ->orderBy('unit_group_name', 'asc')
            ->get();

        return view('pages.unit', [
            'table' => trashController::encrypt('unit'),
            'unitGroups' => $unitGroups,
        ]);
    }

    public function table(Request $request)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $draw   = (int) $request->get('draw', 1);
        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 20);

        // Base query on the DB view
        $base = DB::table('v_offices');

        // Total before filters
        $recordsTotal = (clone $base)->count();

        // Optional search (from d.search in DataTables ajax)
        if ($request->filled('search')) {
            $search = $request->get('search');

            $base->where(function ($q) use ($search) {
                $q->where('unit_name', 'like', "%{$search}%")
                ->orWhere('unit_abbr', 'like', "%{$search}%")
                ->orWhere('unit_group_code', 'like', "%{$search}%")
                ->orWhere('unit_group_name', 'like', "%{$search}%");
            });
        }

        // Unit Group filter
        if ($request->filled('unit_group') && $request->unit_group !== 'all') {
            $base->where('unit_group_name', $request->unit_group);
        }

        $recordsFiltered = (clone $base)->count();

        // Pagination + order
        $rows = $base
            ->orderBy('unit_name', 'asc')
            ->skip($start)
            ->take($length)
            ->get([
                'unit_code',
                'unit_name',
                'unit_abbr',
                'unit_group_code',
                'unit_group_name',
            ]);

        $data = $rows->map(fn ($row) => [
            'id'               => $row->unit_code,
            'unit_name'        => $row->unit_name,
            'unit_abbr'        => $row->unit_abbr,
            'unit_group_code'  => $row->unit_group_code,
            'unit_group_name'  => $row->unit_group_name,
        ])->values();

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

    public function destroy($id)
    {
        //TEMPORARY DELETE LOGIC USING QUERY BUILDER
        $delete = DB::table('unit')
            ->where('unit_code', $id)
            ->delete();

        return response()->json($delete);

        /*
        * THIS IS THE LOGIC ONCE THE MODEL 'UNIT' IS CREATED
        * Uncomment this once the model has been created
        */
        // $unit = Unit::where('unit_code', $id)->firstOrFail();
        // $unit->delete();

        // return response()->json($unit);

    }

}