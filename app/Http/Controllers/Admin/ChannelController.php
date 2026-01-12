<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    public function index()
    {
        // Permission check
        $this->authorizeAdmin();

        $channels = Channel::latest()->get();
        return view('admin.channels.index', compact('channels'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.channels.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:channels',
            'is_active' => 'boolean'
        ]);

        Channel::create($request->all());

        return redirect()->route('admin.channels.index')->with('success', 'Channel created successfully.');
    }

    public function edit(Channel $channel)
    {
        $this->authorizeAdmin();
        return view('admin.channels.edit', compact('channel'));
    }

    public function update(Request $request, Channel $channel)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('channels')->ignore($channel->id)],
            'is_active' => 'boolean'
        ]);

        $channel->update($request->all());

        return redirect()->route('admin.channels.index')->with('success', 'Channel updated successfully.');
    }

    public function destroy(Channel $channel)
    {
        $this->authorizeAdmin();
        
        $channel->delete();

        return redirect()->route('admin.channels.index')->with('success', 'Channel deleted successfully.');
    }

    private function authorizeAdmin()
    {
        $admin = auth('admin')->user();
        if (!$admin || (!$admin->isSuperAdmin() && !$admin->hasPermission('manage_channels'))) {
            abort(403, 'Unauthorized action.');
        }
    }
}
