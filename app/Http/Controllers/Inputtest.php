<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Image;

class Inputtest extends Controller
{
    public function add(Request $req){
        $validatedData = $req->validate([
            'text' => ['required', 'string', 'max:255'],
            'number' => ['required', 'integer'],
            'image' => ['image', 'max:2048'],
        ]);

        $data = array();
        $image = request()->file('image');
        if($image){
            $name = hexdec(uniqid());
            $extension = $image->getClientOriginalExtension();
            $fullname = $name.'.'.$extension;
            $path = 'public/images/testinputs/images/';
            $url = $path.$fullname;
            $resize_image=Image::make($image->getRealPath());
            $resize_image->resize(300,300);
            $resize_image->save('public/images/testinputs/images/'.$fullname);
            $data['text'] = $req -> text;
            $data['number'] = $req -> number;
            $data['image'] = $url;
            DB::table('testinputs') -> insert($data);
            return redirect()->route('welcome')->with('message','Data successfully added');
        }
        else{
            $data['text'] = $req -> text;
            $data['number'] = $req -> number;
            DB::table('testinputs') -> insert($data);
            return redirect()->route('welcome')->with('message','Data successfully added');
        }
    }
    public function view(){
        $views = DB::table('testinputs') -> get();
        return view('view', ['views' => $views]);
    }
    public function update($id){
        $update = DB::table('testinputs') -> where('id', $id)->first();
        return view('update', ['update' => $update]);
    }
    public function update2(Request $req, $id){
        $validatedData = $req->validate([
            'text' => ['required', 'string', 'max:255'],
            'number' => ['required', 'integer'],
            'image' => ['image', 'max:2048'],
        ]);

        $data = array();
        $image = request()->file('image');
        if($image){
            $update = DB::table('testinputs') -> where('id', $id)->first();
            $old_image=$update->image;
            if(file_exists($old_image)){
                unlink($old_image);
            }
            $name = hexdec(uniqid());
            $extension = $image->getClientOriginalExtension();
            $fullname = $name.'.'.$extension;
            $path = 'public/images/testinputs/images/';
            $url = $path.$fullname;
            $resize_image=Image::make($image->getRealPath());
            $resize_image->resize(300,300);
            $resize_image->save('public/images/testinputs/images/'.$fullname);
            $data['text'] = $req -> text;
            $data['number'] = $req -> number;
            $data['image'] = $url;
            DB::table('testinputs') -> where('id', $id)->update($data);
            return redirect()->route('view')->with('message','Data successfully updated');
        }
        else{
            $data['text'] = $req -> text;
            $data['number'] = $req -> number;
            DB::table('testinputs') -> where('id', $id)->update($data);
            return redirect()->route('view')->with('message','Data successfully updated');
        }
    }
    public function delete($id){
        $delete = DB::table('testinputs') -> where('id', $id)->first();
        $image  = $delete->image;
        if(file_exists($image)){
            unlink($image);
        }
        DB::table('secondtestinputs') -> where('ti_id', $id)->delete();
        DB::table('testinputs') -> where('id', $id)->delete();
        return redirect()->route('view')->with('error','Data successfully deleted');
    }
    public function add2(){
        $first_tables=DB::table('testinputs')->get();
        return view('add', ['first_tables' => $first_tables]);
    }
    public function add3(Request $req){
        $validatedData = $req->validate([
            'text2' => ['required', 'string', 'max:255'],
            'ti_id' => ['required'],
        ]);

        $data = array();
        $data['text2'] = $req -> text2;
        $data['ti_id'] = $req -> ti_id;
        DB::table('secondtestinputs')->insert($data);
        return redirect()->route('add2')->with('message','Data successfully added');
    }
    public function view2(){
        $views = DB::table('secondtestinputs') -> join('testinputs', 'secondtestinputs.ti_id', '=', 'testinputs.id') -> select('secondtestinputs.*', 'testinputs.text') -> get();
        return view('view2', ['views' => $views]);
    }
    public function update3($id){
        $update = DB::table('secondtestinputs') -> join('testinputs', 'testinputs.id', '=', 'secondtestinputs.ti_id') -> where('secondtestinputs.id', $id) -> select('secondtestinputs.*', 'testinputs.text') -> first();
        $first_tables = DB::table('testinputs') -> get();
        return view('update2', ['update' => $update, 'first_tables' => $first_tables]);
    }
    public function update4(Request $req, $id){
        $validatedData = $req->validate([
            'text2' => ['required', 'string', 'max:255'],
            'ti_id' => ['required'],
        ]);

        $data = array();
        $data['text2'] = $req -> text2;
        $data['ti_id'] = $req -> ti_id;
        DB::table('secondtestinputs') -> where('id', $id)->update($data);
        return redirect()->route('view2')->with('message','Data successfully updated');
    }
    public function delete2($id){
        DB::table('secondtestinputs') -> where('id', $id)->delete();
        return redirect()->route('view2')->with('error','Data successfully deleted');
    }
}
