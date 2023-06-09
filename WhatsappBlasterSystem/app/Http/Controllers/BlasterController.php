<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blaster;
use App\Models\Customer;
use Auth;
use Session;
use Illuminate\Support\Arr;
class BlasterController extends Controller
{
    public function view()
    {
        $data['blasters'] = Blaster::where('user_id', Auth::id())->get();

        return view('user/blaster/index', $data);
    }
    public function add_view()
    {
        return view('user/blaster/add');
    }

    public function add(Request $request)
    {
        $blasting = Blaster::create([
            'user_id' => Auth::id(),
            'name' => $request->blaster_name,
        ]);
        return redirect()->route('blaster_view');
    }
    public function delete($id)
    {
        //find the delete_at data
        // $deleteBlasting = BlastingList::withTrashed()->findOrFail(1);
        //recovery the data
        // dd($deleteBlasting->restore());

        $blaster = Blaster::find($id);

        //validation
        if (Auth::id() == $blaster->user_id) {
            $blaster->delete(); //soft delete
        }

        return redirect()
            ->route('blaster_view')
            ->with('messages', 'delete successfully!');
    }
    public function viewCustomer($id)
    {
        $data['blaster'] = Blaster::where([['id', $id], ['user_id', Auth::id()]])->first();

        //validate
        if (isset($data['blaster'])) {
            return view('user/blaster/customerlist', $data);
        }

        return redirect()->route('blaster_view');
    }
    public function edit($id)
    {
        $data['blaster'] = Blaster::where([['id', $id], ['user_id', Auth::id()]])->first();
        if ($data['blaster'] == null) {
            return back();
        }
        return view('user/blaster/add', $data);
    }
    public function update(Request $request)
    {
        $blaster = Blaster::find($request->blaster_id);

        //validation
        if (Auth::id() != $blaster->user_id) {
            Session::flash('error', 'Edit Fail');
            return redirect()->route('blaster_view');
        }

        $blaster->name = $request->blaster_name;
        $blaster->save();

        return redirect()->route('blaster_view_customer', $blaster->id);
    }
    public function delete_view()
    {
        $data['blasters'] = Blaster::onlyTrashed()
            ->where('user_id', Auth::id())
            ->get();
        if ($data['blasters']->count() == null) {
            $data['blasters'] = 'blaster_history_null';
        }
        return view('user/blaster/index', $data);
    }
    public function restore($id){

        $blaster = Blaster::withTrashed()->find($id);
        if($blaster->user_id == Auth::id()){
           $blaster->restore();
        }
        return back();
    }
}
