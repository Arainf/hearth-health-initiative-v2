<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Dump\trashController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountPageController extends Controller
{
    public object $trash;
    public string $token;
    public string $module;

    public function __construct() {
        $this->module = 'account';
        $this->trash = new trashController;
        $this->token = $this->trash->encrypt($this->module);
    }

    public function index(Request $request, $token) {
        $MODULE_NAME = ['icon' => 'shield-user', 'label' => 'Accounts'];
        $user = auth()->user();

        if (!$user->is_Admin()) return redirect('unauthorized');

        if ($request->filled('mode')) {
            return $this->menu($request);
        }

        return view('pages.account', [
            'TOKEN' => $token,
            'MODULE_NAME' => $MODULE_NAME,
        ]);
    }

    public function menu(Request $request) {
        $rawMode = $request->query('mode') ?? $request->input('mode');
        $mode = $this->trash->decrypt($rawMode);

        return match ($mode) {
            'create' => view('accounts.create'),
            'save'   => app(RegisteredUserController::class)->store($request),
            // Unified handlers for the AJAX calls from your action buttons
            'toggle-admin'  => $this->handleToggle($request, 'is_admin'),
            'toggle-ai'     => $this->handleToggle($request, 'ai_access'),
            'toggle-doctor' => $this->handleToggle($request, 'is_doctor'),
            'delete'        => $this->destroyAccount($request),
            default         => abort(404),
        };
    }

    /**
     * Unified Toggle Handler for Admin, AI, and Doctor status
     */
    private function handleToggle(Request $request, string $column) {
        $id = $this->trash->decrypt($request->input('id'));
        $value = (int) $request->input('value');

        $user = User::findOrFail($id);
        $user->update([$column => $value]);

        return response()->json([
            'success' => true,
            'message' => 'Account updated successfully.'
        ]);
    }

    /**
     * Handler for Delete Action
     */
    private function destroyAccount(Request $request) {
        $id = $this->trash->decrypt($request->input('id'));

        $user = User::findOrFail($id);

        // Prevent self-deletion if necessary
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Cannot delete your own account.'], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully.'
        ]);
    }

    public function table(Request $request) {
        if (!$request->ajax()) return abort(404);

        $draw   = (int) $request->get('draw', 1);
        $start  = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 20);

        $base = User::query();

        $recordsTotal = $base->count();

        if ($request->filled('search')) {
            $search = $request->search;
            $base->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $base->count();

        $rows = $base->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($length)
            ->get();

        $data = $rows->map(function ($u) {
            $encId     = $this->trash->encrypt($u->id);
            $modeAdmin = $this->trash->encrypt('toggle-admin');
            $modeAI    = $this->trash->encrypt('toggle-ai');
            $modeDoc   = $this->trash->encrypt('toggle-doctor');
            $modeDel   = $this->trash->encrypt('delete');

            $actions = '
                <div class="flex items-center justify-end gap-2 shrink-0 min-w-[160px]">
                    <button type="button" data-id="'.$encId.'" data-mode="'.$modeAdmin.'" data-value="'.($u->is_admin ? 0 : 1).'"
                        class="action-btn hhi-btn hhi-btn-secondary icon-only" title="'.($u->is_admin ? 'Remove Admin' : 'Make Admin').'">
                        <i data-lucide="user-key" class="w-4 h-4 '.($u->is_admin ? 'text-blue-600' : 'text-gray-400').'"></i>
                    </button>

                    <button type="button" data-id="'.$encId.'" data-mode="'.$modeAI.'" data-value="'.($u->ai_access ? 0 : 1).'"
                        class="action-btn hhi-btn hhi-btn-secondary icon-only" title="'.($u->ai_access ? 'Disable AI' : 'Enable AI').'">
                        <i data-lucide="brain" class="w-4 h-4 '.($u->ai_access ? 'text-green-600' : 'text-gray-400').'"></i>
                    </button>

                    <button type="button" data-id="'.$encId.'" data-mode="'.$modeDoc.'" data-value="'.($u->is_doctor ? 0 : 1).'"
                        class="action-btn hhi-btn hhi-btn-secondary icon-only" title="'.($u->is_doctor ? 'Remove Doctor' : 'Make Doctor').'">
                        <i data-lucide="stethoscope" class="w-4 h-4 '.($u->is_doctor ? 'text-purple-600' : 'text-gray-400').'"></i>
                    </button>

                    <div class="w-[2px] h-[31px] bg-gray-200 mx-1"></div>

                    <button type="button" data-id="'.$encId.'" data-mode="'.$modeDel.'"
                        class="action-btn hhi-btn hhi-btn-delete icon-only" title="Delete Account">
                        <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                    </button>
                </div>';

            return [
                'id'         => $encId,
                'name'       => $u->name,
                'username'   => $u->username,
                'is_admin'   => (int) $u->is_admin,
                'ai_access'  => (int) $u->ai_access,
                'is_doctor'  => (int) $u->is_doctor,
                'created_at' => $u->created_at->format('M d, Y'),
                'actions'    => $actions,
            ];
        });

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data,
        ]);
    }
}
