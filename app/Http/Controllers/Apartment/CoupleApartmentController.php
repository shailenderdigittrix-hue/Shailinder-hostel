<?php

namespace App\Http\Controllers\Apartment;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Hostel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\CoupleApartment;
use App\Models\ApartmentMember;
use Illuminate\Validation\Rule;
use App\Models\Mess;

class CoupleApartmentController extends Controller
{
   public function index() {
       $data = CoupleApartment::withCount('apartmentMember')->paginate(10);
      
        // Optionally rename the key if you want a custom one:
        $data->getCollection()->transform(function ($item) {
            $item->number_of_members = $item->apartment_member_count;
            unset($item->apartment_member_count);
            return $item;
        });
     return view('backend.Apartment.index', compact('data'));
   }

   public function create ($id = null) {
     $messList = Mess::orderBy('id', 'desc')->get();
     return view('backend.Apartment.add', compact('messList'));
   }

    public function store(Request $request)
    {
    	$checkApartment = CoupleApartment::where('id', $request->apartment_number)->first();
    	if(!empty($checkApartment)) {
          return redirect()->route('admin.couple-apartment.create')->with('error', 'This Apartment already exists!');
     	}
        $request->validate([
        	'apartment_number' => [
	              'required',
	              Rule::unique('couple_apartment', 'apartment_number'),
	        ],
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'floor_number' => 'required|integer|min:1',
            'total_floors' => 'required|integer|min:1',
            // 'bedrooms' => 'required|integer|min:0',
            // 'bathrooms' => 'required|integer|min:0',
            // 'balconies' => 'required|integer|min:0',
            'furnished_status' => 'required|string|in:Furnished,Semi-Furnished,Unfurnished',
            // 'mess_id' => 'required|exists:mess,id',

            // Member validation
            'members' => 'required|array|min:1',
            'members.*.name' => 'required|string|max:255',
            'members.*.relation' => 'required|string|max:255',
            'members.*.age' => 'required|integer|min:1',
        ]);

        // ✅ Save Apartment
        $apartment = CoupleApartment::create([
        	'apartment_number' => $request->apartment_number,
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'floor_number' => $request->floor_number,
            'total_floors' => $request->total_floors,
            // 'bedrooms' => $request->bedrooms,
            // 'bathrooms' => $request->bathrooms,
            // 'balconies' => $request->balconies,
            'furnished_status' => $request->furnished_status,
            // 'mess_id' => $request->mess_id,
        ]);

        // ✅ Save Members
        foreach ($request->members as $member) {
            ApartmentMember::create([
                'apartment_id' => $apartment->id, // ✅ missing comma fixed here
                'name' => $member['name'],        // ✅ use array keys, not object syntax
                'relation' => $member['relation'],
                'age' => $member['age'],
            ]);
        }

        return redirect()->route('admin.couple-apartment.create')->with('success', 'Apartment created successfully!');
    }


    public function destroy($id)
        {
            $apartment = CoupleApartment::findOrFail($id);
            $apartment->delete();

            return redirect()->route('admin.couple-apartment.index')->with('success', 'Apartment deleted successfully.');
        }

    public function edit($id)
		{
		    $apartment = CoupleApartment::with('apartmentMember')->findOrFail($id);
		    $messList = Mess::orderBy('id', 'desc')->get();
		    return view('backend.Apartment.edit', compact('apartment', 'messList', 'id'));
		}

	   public function update(Request $request, $id)
     	{
     		 $request->validate([
     		 	'apartment_number' => [
	              'required',
	              Rule::unique('couple_apartment', 'apartment_number')->ignore($id),
	            ],
                'name' => 'required|string|max:255',
	            'type' => 'required|string|max:255',
	            'description' => 'required|string',
	            'floor_number' => 'required|integer|min:1',
	            'total_floors' => 'required|integer|min:1',
	            'furnished_status' => 'required|string|in:Furnished,Semi-Furnished,Unfurnished',
     		 ]);
		     $apartment = CoupleApartment::findOrFail($id);
		     $apartment->update([
		       'apartment_number' => $request->apartment_number,
               'name' => $request->name,
               'type' => $request->type,
               'description' => $request->description,
               'floor_number' => $request->floor_number,
               'total_floors' => $request->total_floors,
               // 'bedrooms' => $request->bedrooms,
               // 'bathrooms' => $request->bathrooms,
               // 'balconies' => $request->balconies,
               'furnished_status' => $request->furnished_status,
		     ]);

           ApartmentMember::where('apartment_id', $id)->delete();
           foreach ($request->members as $key => $member) {
               ApartmentMember::create([
                'apartment_id' => $id, // ✅ missing comma fixed here
                'name' => $member['name'],        // ✅ use array keys, not object syntax
                'relation' => $member['relation'],
                'age' => $member['age'],
              ]);
           }

		     return redirect()->route('admin.couple-apartment.index')->with('success', 'Apartment updated successfully!');
	    }
}
