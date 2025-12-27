<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get unread notifications for the logged-in user
        $unreadNotifications = Notification::where('is_read', 0);

        // Mark them as read
        $unreadNotifications->update(['is_read' => 1]);

        $data['notifications'] = Notification::orderBy('created_at', 'desc')->paginate(10);
        return view('backend.notifications.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->get();
        // return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Room Booking',
            'message' => 'Your hostel room has been successfully booked!',
            'type' => 'booking',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
