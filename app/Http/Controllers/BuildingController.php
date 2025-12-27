<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BuildingController extends Controller
{
    // public function index()
    // {
    //     try {
    //         $data['buildings'] = Building::with('hostel')->get();
    //         return view('backend.HostelManagement.buildings.list', $data);
    //     } catch (\Exception $e) {
    //         Log::error('Building Index Error: ' . $e->getMessage());
    //         return redirect()->back()->with('error', 'Failed to load buildings.');
    //     }
    // }

    public function index() {
        try {
            $query = Building::with('hostel');

            if (auth()->user()->hasRole('Hostel Warden')) {
                $wardenId = auth()->id();

                // Get hostels assigned to the warden
                $wardenHostelIds = \App\Models\Hostel::where('warden', $wardenId)->pluck('id');

                // Filter buildings that belong to these hostels
                $query->whereIn('hostel_id', $wardenHostelIds);
            }

            $data['buildings'] = $query->get();

            return view('backend.HostelManagement.buildings.list', $data);
        } catch (\Exception $e) {
            Log::error('Building Index Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load buildings.');
        }
    }


    public function create()
    {
        try {
            $data['hostels'] = Hostel::all();
            return view('backend.HostelManagement.buildings.add', $data);
        } catch (\Exception $e) {
            Log::error('Building Create Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'hostel_id' => 'required|exists:hostels,id',
                'name' => 'required|string|max:255',
                'number_of_floors' => 'nullable|integer|min:0',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $validated['image'] = "";

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $name = time() . '' . str_replace(' ', '', $file->getClientOriginalName());
                $file->move(public_path('/uploads/'), $name);
                $validated['image'] = url('/public/uploads/' . $name);
            }

            Building::create($validated);

            return redirect()->route('buildings.index')->with('success', 'Building created successfully.');
        } catch (\Exception $e) {
            Log::error('Building Store Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create building.');
        }
    }

    public function show(Building $building)
    {
        try {
            return $building;
        } catch (\Exception $e) {
            Log::error('Building Show Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve building.'], 500);
        }
    }

    public function edit(Building $building)
    {
        try {
            $data['hostels'] = Hostel::all();
            $data['building'] = $building;
            return view('backend.HostelManagement.buildings.edit', $data);
        } catch (\Exception $e) {
            Log::error('Building Edit Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, Building $building)
    {
        try {
            $validated = $request->validate([
                'hostel_id' => 'required|exists:hostels,id',
                'name' => 'required|string|max:255',
                'number_of_floors' => 'nullable|integer|min:0',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            ]);

            $validated['image'] = $request->set_image;

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $name = time() . '' . str_replace(' ', '', $file->getClientOriginalName());
                $file->move(public_path('/uploads/'), $name);
                $validated['image'] = url('/public/uploads/' . $name);
            }

            $building->update($validated);

            return redirect()->route('buildings.index')->with('success', 'Building updated successfully.');
        } catch (\Exception $e) {
            Log::error('Building Update Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update building.');
        }
    }

    public function destroy(Building $building)
    {
        try {
            $building->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Building Delete Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete building.'], 500);
        }
    }
}
