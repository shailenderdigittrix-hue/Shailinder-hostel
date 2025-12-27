<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelReader;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Student;
use App\Models\Hostel;
use App\Models\Course;
use App\Models\Room;
use App\Models\Building;
use App\Models\RoomAllocation;
use App\Models\RoomChangeRequest;
use App\Models\HostelDevices;
use Carbon\Carbon;
use DB;

class HostelController extends Controller
{
    
    public function index()
    {
        $data['hostels'] = Hostel::all();
        // dd($data['hostels']);
        return view('backend.HostelManagement.hostels.list', $data);
    }

    public function create(){
        $data['hostels'] = Hostel::all();
        $data['wardens'] = User::role('Hostel Warden')->get();

        // dd($data['wardens']);

        return view('backend.HostelManagement.hostels.add',$data);
    }


public function store(Request $request)
{
    // dd($request->all());
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50',
        'gender' => 'required|in:male,female,co-ed',
        // validate device_serial_no as an array of values
        'device_serial_no' => 'required|array|min:1',
        'device_serial_no.*' => 'required|string|max:255',
        'total_capacity' => 'required|integer',
        'warden' => 'nullable|string|max:255',
        'contact' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'address' => 'nullable|string',
        'facilities' => 'nullable|array',
        'facilities.*' => 'string',
        'is_active' => 'nullable',
    ]);

    \DB::beginTransaction();
    try {
        // If you want a single primary device_serial_no column on hostels table,
        // set it from the first device in the array; otherwise set null.
        $primaryDevice = $validated['device_serial_no'][0] ?? null;

        // 1️⃣ Create hostel
        $hostel = Hostel::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'gender' => $validated['gender'],
            'device_serial_no' => $primaryDevice, // keep or remove depending on schema
            'building' => $validated['building'] ?? null,
            'total_capacity' => $validated['total_capacity'],
            'warden' => $validated['warden'] ?? null,
            'contact' => $validated['contact'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'facilities' => $validated['facilities'] ?? null,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'created_by' => auth()->id(),
        ]);

        // 2️⃣ Assign multiple devices (if any)
        if (!empty($validated['device_serial_no'])) {
            foreach ($validated['device_serial_no'] as $device_serial_no) {
                $checkExisting = HostelDevices::where('device_serial_no', $device_serial_no)->first();
                if(!empty($checkExisting)){
                    \DB::rollBack();
                    return back()->withErrors(['device_serial_no' => 'Device Serial No ' . $device_serial_no . ' already assigned to another hostel.'])
                                 ->withInput();
                }
                HostelDevices::create([
                    'hostel_id' => $hostel->id,
                    'device_serial_no' => $device_serial_no,
                    'device_name' => 'Device ' . $device_serial_no, // optional label
                ]);
            }
        }

        \DB::commit();

        return redirect()
            ->route('hostels.index')
            ->with('success', 'Hostel added successfully.');

    } catch (\Exception $e) {
        \DB::rollBack();
        return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()])
                     ->withInput();
    }
}



    public function show(string $id) {
        $hostel = Hostel::findOrFail($id);
        return view('backend.HostelManagement.hostels.show', compact('hostel'));
    }

    
    public function edit(string $id) {
        $data['wardens'] = User::role('Hostel Warden')->get();
        $data['hostel'] = Hostel::with(['hostelDevices'])->findOrFail($id);
        // echo'<pre>';print_r($data['hostel']->hostelDevices);die;
        // dd($data['hostel']->hostelDevices);die;
    
        return view('backend.HostelManagement.hostels.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        $hostel = Hostel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hostels,name,' . $hostel->id,
            'code' => 'required|string|max:255|unique:hostels,code,' . $hostel->id,
            'gender' => 'required|in:male,female,co-ed',
            'device_serial_no' => 'required|array|min:1',
            'device_serial_no.*' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'total_capacity' => 'required|integer',
            'warden' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'facilities' => 'nullable|array',
            'facilities.*' => 'string',
            'is_active' => 'nullable',
        ]);

        // Prepare data for update
        $data = [
            'name' => $validated['name'],
            'code' => $validated['code'],
            'gender' => $validated['gender'],
            'building' => $validated['building'] ?? null,
            'total_capacity' => $validated['total_capacity'],
            'warden' => $validated['warden'] ?? null,
            'contact' => $validated['contact'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'facilities' => $validated['facilities'] ?? null,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'updated_by' => auth()->id(),
        ];

        // Get current and new device lists
        $existingDevices = $hostel->hostelDevices()->pluck('device_serial_no')->toArray();
        $newDevices = $validated['device_serial_no'];

        // Check if any new device is already assigned to another hostel
        $conflictingDevices = \App\Models\HostelDevices::whereIn('device_serial_no', $newDevices)
            ->where('hostel_id', '!=', $hostel->id)
            ->pluck('device_serial_no')
            ->toArray();

        if (!empty($conflictingDevices)) {
            return back()
                ->withErrors(['device_serial_no' => 'The following device(s) are already assigned to another hostel: ' . implode(', ', $conflictingDevices)])
                ->withInput();
        }

        // Delete removed devices
        $devicesToDelete = array_diff($existingDevices, $newDevices);
        if (!empty($devicesToDelete)) {
            \App\Models\HostelDevices::where('hostel_id', $hostel->id)
                ->whereIn('device_serial_no', $devicesToDelete)
                ->delete();
        }

        // Add new devices
        $devicesToAdd = array_diff($newDevices, $existingDevices);
        foreach ($devicesToAdd as $device_serial_no) {
            \App\Models\HostelDevices::create([
                'hostel_id' => $hostel->id,
                'device_serial_no' => $device_serial_no,
                'device_name' => 'Device ' . $device_serial_no,
            ]);
        }

        // Update hostel main record
        $hostel->update($data);

        return redirect()->route('hostels.index')->with('success', 'Hostel updated successfully.');
    }



    public function destroy(string $id)
    {
        $hostel = Hostel::findOrFail($id);
        $hostel->delete();

        return redirect()->route('hostels.index')->with('success', 'Hostel deleted successfully.');
    }
}
