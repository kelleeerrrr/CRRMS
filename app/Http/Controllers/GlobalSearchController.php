<?php

namespace App\Http\Controllers;

use App\Services\GlobalSearchService;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    protected GlobalSearchService $globalSearchService;

    public function __construct(GlobalSearchService $globalSearchService)
    {
        $this->globalSearchService = $globalSearchService;
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $types = $request->get('types', ['users', 'buildings', 'facilities', 'equipment', 'reservations']);

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = $this->globalSearchService->search($query, ['types' => $types]);

        // Save search for logged-in users
        if (auth()->check()) {
            $this->globalSearchService->saveSearch(auth()->user(), $query);
        }

        return response()->json(['results' => $results]);
    }

    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $types = $request->get('types', '*');

        if (empty($query)) {
            return view('global-search.index');
        }

        $filters = ['types' => $types === '*' ? ['users', 'buildings', 'facilities', 'equipment', 'reservations'] : explode(',', $types)];
        $results = $this->globalSearchService->search($query, $filters);

        // Save search for logged-in users
        if (auth()->check()) {
            $this->globalSearchService->saveSearch(auth()->user(), $query);
        }

        return view('global-search.index', compact('query', 'results', 'types'));
    }
}
