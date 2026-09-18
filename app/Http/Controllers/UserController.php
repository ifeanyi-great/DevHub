<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class UserController extends Controller
{
   use AuthorizesRequests; 
    public function show(User $user)
    {
     return view('users.show', [
        'user'=> $user
     ]);
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

 

        $request->validate([
         'profile_picture' => 'image|max:2048',
        ]);

       $path = $request->file('profile_picture')->store('profile_pictures', 'public');

       $user->profile_picture = $path;

       $user->save();
    }

}
