<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mess;
use App\Models\Hostel;

class MessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Mess::with('hostel')->paginate();
        return view('backend.MessManagement.menus.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $hostels = Hostel::get();
        // $data = Mess::where('id', $id)->first();
        // echo "<pre>";print_r($data);die;
        return view('backend.MessManagement.menus.add', compact('hostels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
           'mess_name' => 'required',
           'hostel_id' => 'required',
           'menu_document' => 'required'
         ]);

        $imagePath = null;
        if ($request->hasFile('menu_document')) {
             $file = $request->file('menu_document');
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/mess_menu'), $filename);
                $imagePath = 'uploads/mess_menu/'.$filename;
        } else {
            $imagePath = $request->old_menu_document;
        }
        // echo $imagePath;die;
        $res = Mess::create([
           'name' => $request->mess_name,
           'hostel_id' => $request->hostel_id,
           'menu_document_upload' => $imagePath
        ]);

        if($res) {
            return redirect()->route('mess.list')->with("success", "Successfully Added");
        } else {
            return redirect()->route('mess.list')->with("error", "Something went wrong");
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
        $hostels = Hostel::get();
        $data = Mess::where('id', $id)->first();
        // echo "<pre>";print_r($data);die;
        return view('backend.MessManagement.menus.edit', compact('hostels', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // echo'<pre>';print_r($request->all());die();
        $request->validate([
           'mess_name' => 'required',
           'hostel_id' => 'required',
         ]);

        $imagePath = null;
        if ($request->hasFile('menu_document')) {
             $file = $request->file('menu_document');
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/mess_menu'), $filename);
                $imagePath = 'uploads/mess_menu/'.$filename;
        } else {
            $imagePath = $request->old_menu_document;
        }
        // echo $imagePath;die;

        $res = Mess::where('id', $id)->update([
           'name' => $request->mess_name,
           'hostel_id' => $request->hostel_id,
           'menu_document_upload' => $imagePath
        ]);

        if($res) {
            return redirect()->route('mess.list')->with("success", "Successfully updated");
        } else {
            return redirect()->route('mess.list')->with("error", "Something went wrong");
        }
 
        // echo'<pre>';print_r($request->all());die;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Mess::where('id', $id)->delete();
        return redirect()->route('mess.list')->with("success", "Successfully deleted");
    }
}
