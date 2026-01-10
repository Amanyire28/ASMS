<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('modules.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('modules.announcements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,academic,event,urgent',
            'valid_until' => 'nullable|date|after:today',
        ]);

        Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'valid_until' => $request->valid_until,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function show(Announcement $announcement)
    {
        return view('modules.announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement)
    {
        return view('modules.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,academic,event,urgent',
            'valid_until' => 'nullable|date|after:today',
            'is_active' => 'boolean',
        ]);

        $announcement->update([
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'valid_until' => $request->valid_until,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);

        $status = $announcement->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Announcement {$status} successfully.");
    }
}
