<?php

namespace App\Http\Controllers;

use App\Models\EquipmentBorrowRequest;
use App\Models\FacilityReservation;
use App\Models\ResourceBorrowRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BorrowerRecordController extends Controller
{
    public function index(Request $request): View
    {
        $loans = $request->routeIs('borrower.loans');
        $categories = [
            'facilities' => [
                'Facility Reservations', FacilityReservation::class, 'facility', 'title',
                'facilities.reservations.show',
            ],
            'equipment' => [
                'Equipment Borrow Requests', EquipmentBorrowRequest::class, 'items.equipment', 'purpose',
                'equipment.borrow-requests.show',
            ],
            'resources' => [
                'Resource Requests', ResourceBorrowRequest::class, 'resource', 'purpose',
                'resource-borrow-requests.show',
            ],
        ];

        if ($loans) {
            unset($categories['facilities']);
        }

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'in:'.implode(',', array_keys($categories))],
            'status' => ['nullable', 'in:pending,approved,reserved,checked_in,in_use,returned,completed,rejected,cancelled'],
        ]);
        $search = trim($filters['search'] ?? '');
        $categoryFilter = $filters['category'] ?? '';
        $status = $filters['status'] ?? '';
        $queries = [];

        foreach ($categories as $category => [$label, $model, $relation, $column]) {
            if ($categoryFilter !== '' && $categoryFilter !== $category) {
                continue;
            }

            $query = $model::query()
                ->select('id', 'updated_at')
                ->selectRaw('? as category', [$category])
                ->where('user_id', $request->user()->id);

            if ($loans) {
                $releaseColumn = $category === 'equipment' ? 'released_at' : 'checked_out_at';
                $query->where(function ($query) use ($releaseColumn) {
                    $query->whereNotNull($releaseColumn)
                        ->orWhereIn('status', ['in_use', 'returned', 'completed']);
                });
            }

            if ($status !== '') {
                $query->where('status', $status);
            }

            if ($search !== '') {
                $query->where(function ($query) use ($search, $column, $relation) {
                    $query->whereLike($column, "%{$search}%")
                        ->orWhereHas($relation, fn ($related) => $related->whereLike('name', "%{$search}%"));

                    if (Str::isUuid($search)) {
                        $query->orWhere('id', $search);
                    }
                });
            }

            $queries[] = $query->toBase();
        }

        $union = array_shift($queries);
        foreach ($queries as $query) {
            $union->unionAll($query);
        }

        $records = DB::query()->fromSub($union, 'borrower_records')
            ->orderByDesc('updated_at')
            ->orderBy('category')
            ->orderBy('id')
            ->paginate(15)
            ->appends($filters);

        // Fetch full models and loan items only for the current page.
        $models = [];
        foreach ($records->getCollection()->groupBy('category') as $category => $rows) {
            [, $model, $relation] = $categories[$category];
            $models[$category] = $model::with($relation)
                ->where('user_id', $request->user()->id)
                ->whereKey($rows->pluck('id')->all())
                ->get()->keyBy('id');
        }

        $records->setCollection($records->getCollection()->map(function ($row) use ($models, $categories) {
            return [
                'category' => $row->category,
                'label' => $categories[$row->category][0],
                'route' => $categories[$row->category][4],
                'record' => $models[$row->category]->get($row->id),
            ];
        })->filter(fn ($row) => $row['record'] !== null));

        return view('borrower.records', compact('records', 'loans', 'categories', 'search', 'categoryFilter', 'status'));
    }
}
