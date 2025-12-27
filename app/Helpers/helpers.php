<?php

use App\Models\Room;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;


if (!function_exists('getAvailableRooms')) {
    function getAvailableRooms()
    {
        return Room::withCount(['allocations as current_occupancy' => function ($query) {
            $query->whereNull('deallocated_at');
        }])->get()->filter(function ($room) {
            return $room->current_occupancy < $room->capacity;
        });
    }
}

if (!function_exists('getNotifications')) {
    /**
     * Get notifications for the authenticated user (or all for admin)
     */
    function getNotifications($userId = null)
    {
        // If no user ID is provided, use the authenticated user
        if(!$userId) {
            $userId = Auth::id();
        }

        return Notification::where('user_id', $userId)
                           ->orderBy('created_at', 'desc')
                           ->get();
    }
}

if (!function_exists('getUnreadNotifications')) {
    function getUnreadNotifications()
    {
        return Notification::where('is_read', false)
                           ->orderBy('created_at', 'desc')
                           ->get();
    }
}

if (!function_exists('getUnreadNotificationsCount')) {
    function getUnreadNotificationsCount()
    {
        return Notification::where('is_read', false)->count();
    }
}

if (!function_exists('decryptEnv')) {
    function decryptEnv($key)
    {
        $value = env($key);
        if (!$value) return null;

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value; // fallback if not encrypted
        }
    }
}

// if (!function_exists('decryptIfNeeded')) {
//     function decryptIfNeeded($value)
//     {
//         // $dddd = Crypt::decryptString($value);
//         // dd($dddd);
//         // dd($value);
//         if (empty($value)) {
//             return $value;
//         }

//         try {
//             $dddd = Crypt::decryptString($value);
//             // dd(Crypt::decryptString($dddd));
//         } catch (\Exception $e) {
//             // Not an encrypted string — just return it as-is
//             return $value;
//         }
//     }
// }

