<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function index() {

        $users = User::all();



        return View::make('users.index', 
            [
                "users" => $users
            ]
        );
    }

    public function show($id){
        $user = User::find($id);

        return View::make('users.show', 
            [
                "user" => $user
            ]
        );
    }

    public function update($id, Request $request){

        $user = User::find($id);
        
        $user->name = $request->name ? $request->name : $user->name;
        $user->email = $request->email ? $request->email : $user->email;
        if($request->password){
            request()->validate([
                'password' => ['required', 'string', 'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/', 'confirmed']
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return Redirect::route('users.index');
    }

    public function delete($id){
        $user = User::find($id);
        $user->delete();

        return Redirect::route('users.index');
    }
}
