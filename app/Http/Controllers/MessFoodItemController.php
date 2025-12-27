<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MessFoodItem;

class MessFoodItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $data = MessFoodItem::paginate();
       // echo'<pre>';print_r($data);die;
       return view('backend.MessManagement.food_items.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */



    public function create(Request $request)
    {
        return view('backend.MessManagement.food_items.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
           'name' => 'required',
           'category' => 'required',
           'description' => 'required',
           'calories' => 'required',
           'price' => 'required',
           'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // max 2MB
         ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
             $file = $request->file('image');
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/food_items'), $filename);
                $imagePath = 'uploads/food_items/'.$filename;
        }


        $res = MessFoodItem::create([
          'name' => $request->name,
          'category' => $request->category,
          'description' => $request->description,
          'calories' => $request->calories,
          'price' => $request->price,
          'image' => $imagePath,
          'status' => $request->status == 'on' ? 'Active' : 'Inactive'
        ]);

        if($res) {
            return redirect()->route('fooditems.list')->with("success", "Successfully Food Item Created");
        } else {
            return redirect()->route('fooditems.list')->with("error", "Something went wrong");
        }
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
       $data = MessFoodItem::where('id', $id)->first();
       // echo'<pre>';print_r($data);die;
       return view('backend.MessManagement.food_items.add', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // echo'<pre>';print_r($request->all());die;
         $request->validate([
           'name' => 'required',
           'category' => 'required',
           'description' => 'required',
           'calories' => 'required',
           'price' => 'required',
           'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // max 2MB
         ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
             $file = $request->file('image');
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/food_items'), $filename);
                $imagePath = 'uploads/food_items/'.$filename;
        } else {
            $imagePath = $request->old_image;
        }


        $res = MessFoodItem::where('id', $id)->update([
          'name' => $request->name,
          'category' => $request->category,
          'description' => $request->description,
          'calories' => $request->calories,
          'price' => $request->price,
          'image' => $imagePath,
          'status' => $request->status == 'on' ? 'Active' : 'Inactive'
        ]);

        if($res) {
            return redirect()->route('fooditems.list')->with("success", "Successfully Food Item Updated");
        } else {
            return redirect()->route('fooditems.list')->with("error", "Something went wrong");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $res = MessFoodItem::where('id', $id)->delete();
        if($res) {
            return redirect()->route('fooditems.list')->with("success", "Successfully Deleted");
        } else {
            return redirect()->route('fooditems.list')->with("error", "Something went wrong");
        }
    }
}
