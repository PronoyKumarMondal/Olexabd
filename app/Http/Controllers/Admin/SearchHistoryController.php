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

        $searches = SearchHistory::with('user')->latest()->paginate(10);

        return view('admin.search_history.index', compact('searches'));
    }
}
