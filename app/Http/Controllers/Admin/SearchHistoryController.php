<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SearchHistory;

class SearchHistoryController extends Controller
{
    public function index()
    {
        if (!auth('admin')->user()->hasPermission('view_search_history')) {
            abort(403, 'Unauthorized action.');
        }

        $query = SearchHistory::with('user');

        // Search Filter (Query String or Customer Name)
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('query', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Date Range Filter
        if ($startDate = request('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = request('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $searches = $query->latest()->paginate(10)->withQueryString();

        return view('admin.search_history.index', compact('searches'));
    }
}
