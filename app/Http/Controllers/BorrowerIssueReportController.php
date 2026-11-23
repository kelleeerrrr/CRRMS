<?php

namespace App\Http\Controllers;

use App\Models\BorrowerIssueReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BorrowerIssueReportController extends Controller
{
    public function index(Request $request): View
    {
        $reviewing = $request->routeIs('borrower.issues.review');
        $query = BorrowerIssueReport::query();

        if ($reviewing) {
            $this->authorize('review-borrower-issues');
            $query->with('user');
        } else {
            $query->where('user_id', $request->user()->id);
        }

        $issueTypeFilter = $request->query('type');
        if (!is_string($issueTypeFilter) || !array_key_exists($issueTypeFilter, BorrowerIssueReport::KINDS)) {
            $issueTypeFilter = null;
        }

        if ($issueTypeFilter !== null) {
            $query->where('kind', $issueTypeFilter);
        }

        $reports = $query->latest()->orderBy('id')->paginate(15)
            ->appends($issueTypeFilter === null ? [] : ['type' => $issueTypeFilter]);

        $view = $request->header('X-Report-Results') === '1' ? 'borrower.issue-results' : 'borrower.issues';

        return view($view, compact('reports', 'reviewing', 'issueTypeFilter'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kind' => ['required', Rule::in(array_keys(BorrowerIssueReport::KINDS))],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $paths = [];
        try {
            foreach ($request->file('photos', []) as $photo) {
                $path = $photo->store('borrower-issue-photos', 'local');
                if ($path === false) {
                    throw new RuntimeException('Could not store issue report photo.');
                }
                $paths[] = $path;
            }

            BorrowerIssueReport::create([
                'user_id' => $request->user()->id,
                'kind' => $data['kind'],
                'location' => $data['location'],
                'description' => $data['description'],
                'photos' => $paths,
            ]);
        } catch (Throwable $error) {
            Storage::disk('local')->delete($paths);
            report($error);

            return back()->withInput()->with('error', 'The report could not be submitted. Please try again.');
        }

        return redirect()->route('borrower.issues.index')->with('success', 'Issue report submitted.');
    }

    public function photo(Request $request, BorrowerIssueReport $issue, int $photo): Response
    {
        if ($issue->user_id !== $request->user()->id && !$request->user()->can('review-borrower-issues')) {
            return response('', 404);
        }

        $path = $issue->photos[$photo] ?? null;
        $disk = Storage::disk('local');
        if (!$path || !$disk->exists($path)) {
            return response('', 404);
        }

        return $disk->response($path, null, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
