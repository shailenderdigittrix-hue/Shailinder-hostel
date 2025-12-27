<?php

namespace App\Http\Controllers;
use App\Models\Room;
use App\Models\Hostel;
use App\Models\Building;
use Illuminate\Http\Request;



class RoomController extends Controller
{
    public function index(){
        $query = Room::with(['building.hostel', 'hostel'])->orderBy('id', 'desc');
        
        if (auth()->user()->hasRole('Hostel Warden')) {
            $wardenId = auth()->id();
            $hostelIds = Hostel::where('warden', $wardenId)->pluck('id');
            $buildingIds = Building::whereIn('hostel_id', $hostelIds)->pluck('id');
            // dd($buildingIds);
            $query->whereIn('building_id', $buildingIds);
        }

        $data['rooms'] = $query->get();

        return view('backend.HostelManagement.rooms.list', $data);
    }


    /**  Show the form for creating a new resource. */
    public function create()
    {
        $data['hostels'] = Hostel::all();
        return view('backend.HostelManagement.rooms.add', $data);
    }

    /** Store a newly created resource in storage. */
    public function store(Request $request) {
        $validated = $request->validate([
            'hostel_id'    => 'required|exists:hostels,id',
            'building_id'  => 'nullable|exists:buildings,id',
            'floor'        => 'nullable|string|max:255',
            'room_number'  => 'required|string|unique:rooms,room_number|max:255',
            'capacity'     => 'required|integer|min:1',
            'room_type'    => 'required|string|in:Single,Double,Triple',
            'is_active'    => 'nullable|boolean',
        ]);

        try {
            // Ensure checkbox sends default value if not checked
            $validated['is_active'] = $request->has('is_active') ? 1 : 0;

            Room::create($validated);

            return redirect()
                ->route('rooms.index')
                ->with('success', 'Room created successfully.');
        } catch (\Exception $e) {
            \Log::error('Room creation failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Something went wrong while creating the room. Please try again.']);
        }
    }


    /**  Display the specified resource. */
    public function show(string $id) {
        
    }

    /** Show the form for editing the specified resource. */
    public function edit(Room $room)
    {
        $data['hostels'] = Hostel::all();
        $data['room'] = $room;
        $data['buildings'] = Building::where('hostel_id', $room->hostel_id)->get();

        return view('backend.HostelManagement.rooms.edit', $data);
    }


    /** Update the specified resource in storage. */
    public function update(Request $request, Room $room) {
        $validated = $request->validate([
            'hostel_id'    => 'required|exists:hostels,id',
            'building_id'  => 'nullable|exists:buildings,id',
            'floor'        => 'nullable|string|max:255',
            'room_number'  => 'required|string|max:255|unique:rooms,room_number,' . $room->id,
            'capacity'     => 'required|integer|min:1',
            'room_type'    => 'required|string|in:Single,Double,Triple',
            'is_active'    => 'nullable|boolean',
        ]);

        try {
            // Ensure checkbox sends value
            $validated['is_active'] = $request->has('is_active') ? 1 : 0;

            $room->update($validated);

            return redirect()
                ->route('rooms.index')
                ->with('success', 'Room updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Room update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['error' => 'Something went wrong while updating the room. Please try again.']);
        }

        
    }







}
