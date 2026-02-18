<?php

namespace App\Http\Controllers;

use App\Models\ClassAnnouncement;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    public function store(Request $request, Classroom $classroom)
    {
        if ($classroom->teacher_id !== $request->user()->id) {
            abort(403, 'You can only ping your own classes.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $classroom->announcements()->create([
            'teacher_id' => Auth::id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => 'general',
        ]);

        return redirect()
            ->route('teacher.classes.show', $classroom->slug)
            ->with('success', 'Announcement sent to ' . $classroom->name . '!');
    }

    public function destroy(Classroom $classroom, ClassAnnouncement $announcement)
    {
        abort_unless($classroom->teacher_id === request()->user()->id, 403);
        abort_unless($announcement->classroom_id === $classroom->id, 404);

        $announcement->delete();

        return redirect()
            ->route('teacher.classes.show', $classroom->slug)
            ->with('success', 'Announcement deleted.');
    }
}
