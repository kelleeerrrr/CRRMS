<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAuditTrail;
use App\Models\AuditTrail;
use App\Models\User;
use App\Services\AuditTrailService;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    protected AuditTrailService $auditTrailService;

    public function __construct(AuditTrailService $auditTrailService)
    {
        $this->auditTrailService = $auditTrailService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', AuditTrail::class);

        $filters = $request->only(['user_id', 'model_type', 'action', 'start_date', 'end_date', 'search']);
        $auditTrails = $this->auditTrailService->getAuditTrails($filters);
        $statistics = $this->auditTrailService->getAuditStatistics();
        $users = User::all();

        return view('audit-trails.index', compact('auditTrails', 'statistics', 'users'));
    }

    public function show(AuditTrail $auditTrail)
    {
        $this->authorize('view', $auditTrail);

        $auditTrail->load('user');

        return view('audit-trails.show', compact('auditTrail'));
    }

    public function store(StoreAuditTrail $request)
    {
        try {
            $auditTrail = $this->auditTrailService->recordChange(
                $request->validated(),
                auth()->user()
            );

            return back()
                ->with('success', 'Audit trail recorded successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }

    public function cleanup()
    {
        $this->authorize('delete', AuditTrail::class);

        try {
            $count = $this->auditTrailService->cleanupOldTrails();

            return back()
                ->with('success', "Cleaned up {$count} old audit trails.");
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage());
        }
    }
}
