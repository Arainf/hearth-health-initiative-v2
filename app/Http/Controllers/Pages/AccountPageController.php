<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use App\Models\User;
use Illuminate\Http\Request;

class AccountPageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->is_Admin()) return redirect('unauthorized');


        return view('pages.account',[
            'table' => trashController::encrypt('account'),
        ]);
    }

    public function table(Request $request)
    {

        if (!$request->ajax()) return;

        $draw   = (int) $request->get('draw', 1);
        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 20);

        $base = User::query();

        /* ===============================
           TOTAL COUNT (NO FILTER)
        =============================== */
        $recordsTotal = $base->count();

        /* ===============================
           SEARCH
        =============================== */
        if ($request->filled('search')) {
            $search = $request->search;

            $base->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");

            });
        }

        $recordsFiltered = $base->count();

        /* ===============================
           PAGINATION + ORDER
        =============================== */
        $rows = $base
            ->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        /* ===============================
           FORMAT FOR DATATABLE
        =============================== */
        $data = $rows->map(fn ($u) => [
            'id'         => $u->id,
            'name'       => $u->name,
            'username'   => $u->username ?? $u->email,
            'is_admin'   => (int) $u->is_admin,
            'ai_access'  => (int) $u->ai_access,
            'is_doctor' => (int) $u->is_doctor,
            'created_at' => $u->created_at,
        ]);

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }

}
