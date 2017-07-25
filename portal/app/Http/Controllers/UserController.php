<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use App\Sensor;
use DB;
use Hash;
use Image;
use Auth;

class UserController extends Controller
{

    // Helpers
    private function checkRoleAuthorization($request=null, $permission=null, $id=null)
    {
        if ($id && Auth::user()->id == $id) // edit self is allowed
            return true;
     
        if ($permission && Auth::user()->can($permission) == false) // check permissions
            return false;

        // Check for unauthorized role editing
        if ($request)
        {
            $superId = Role::where('name','=','superadmin')->pluck('id','id')->toArray();
            $reqIsSup= count(array_diff($request->input('roles'), $superId)) == 0 ? true : false; // check if super admin id role is requested
            $roleIds = $this->getMyPermittedRoles(true);
            $reqMatch= count(array_diff($request->input('roles'), $roleIds)) == 0 ? true : false; // check if all roles match

            if ($reqMatch == false || ($reqIsSup && Auth::user()->hasRole('superadmin') == false)){
                return false;
            }
        }
        return true;
    }

    
    private function getMyPermittedRoles($returnIdArray=false)
    {
        //die($user->roles->pluck('id'));
        if (Auth::user()->hasRole('superadmin'))
        {
            $roles = Role::all();
        }
        else if (Auth::user()->hasRole('admin'))
        {
            $roles = Role::where('name','!=','superadmin');
        }
        else 
        {
            $roles = Auth::user()->roles;
        }
        //die($roles);
        if ($returnIdArray)
        {
            return $roles->pluck('id','id')->toArray();
        } 
        else
        {
            return $roles->pluck('display_name','id');
        }
    }

    private function getMyPermittedSensors()
    {
        if (Auth::user()->hasRole('superadmin'))
        {
            $sensors = Sensor::orderBy('name', 'ASC')->get();
        }
        else 
        {
            $sensors = Auth::user()->sensors();
        }
        return $sensors;
    }

    private function checkIfUserMayEditUser($user)
    {
        //die($user->roles->pluck('id'));
        if (Auth::user()->id == $user->id)
        {
            return true; // you may edit yourself
        }
        else if (Auth::user()->hasRole('superadmin'))
        {
            return true;
        }
        else if (Auth::user()->hasRole('admin') && $user->hasRole('superadmin') == false && $user->hasRole('admin') == false) // only edit users of a 'lower' role
        {
            return true;
        }
        return false;
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = User::all()->sortBy('name', SORT_NATURAL|SORT_FLAG_CASE, false);
            return view('users.index',compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = $this->getMyPermittedRoles();
        $sensors = Sensor::all()->pluck('name','id');
        return view('users.create',compact('roles','sensors'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($this->checkRoleAuthorization($request, "user-create") == false)
            return redirect()->route('users.index')->with('error', 'You are not allowed to create this type of user');

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['api_token'] = str_random(60);

        // Handle the user upload of avatar
        if($request->hasFile('avatar')){
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->resize(300, 300)->save( public_path('uploads/avatars/' . $filename ) );
            $user->avatar = $filename;
        }

        $user = User::create($input);
        
        // Handle role assignment, only store permitted role
        $roleIds = $this->getMyPermittedRoles(true);
        foreach ($request->input('roles') as $key => $value)
        {
            if (in_array($value, $roleIds))
            {
                $user->attachRole($value);
            }
        }

        // Edit sensors
        $user->sensors()->sync($request->input('sensors'));


        return redirect()->route('users.index')
                        ->with('success','User created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        $sensors = $user->sensors()->orderBy('name','asc')->pluck('name','id');
        return view('users.show',compact('user','sensors'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user       = User::find($id);
        $editAllowed= $this->checkIfUserMayEditUser($user);
        
        if ($editAllowed)
        {
            $roles      = $this->getMyPermittedRoles();
            $userRole   = $user->roles->pluck('id','id')->toArray();
            $sensors    = $this->getMyPermittedSensors()->pluck('name','id');
            $userSensor = $user->sensors()->pluck('sensor_id','sensor_id')->toArray();
            return view('users.edit',compact('user','roles','userRole','sensors','userSensor'));
        }

        return redirect()->route('users.index')
                    ->with('error','You are not allowed to edit this user');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        if ($this->checkRoleAuthorization($request, "user-edit", $id) == false)
            return redirect()->route('users.index')->with('error', 'You are not allowed to edit users');
        
        $user = User::find($id);
        if ($this->checkIfUserMayEditUser($user) == false)
            return redirect()->route('users.index')->with('error', 'You are not allowed to edit this user');

        // Do normal validation
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required',
            'avatar' => 'mimes:jpeg,gif,png'
        ]);


        $input = $request->all();
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = array_except($input,array('password'));    
        }
        
        // Handle the user upload of avatar
        if($request->hasFile('avatar')){
            $avatar = $request->file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->fit(300, 300)->save( public_path('uploads/avatars/' . $filename ) );
            $user->avatar = $filename;
        }

        $user->update($input);

        // Edit role
        DB::table('role_user')->where('user_id',$id)->delete();
        foreach ($request->input('roles') as $key => $value) {
            $user->attachRole($value);
        }

        // Edit sensors
        $user->sensors()->sync($request->input('sensors'));

        return redirect()->route('users.index')
                        ->with("success", "User updated successfully");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($this->checkRoleAuthorization(null, "user-delete", $id) == false)
            return redirect()->route('users.index')->with('error','User not deleted, you have no permission');


        User::find($id)->delete();
        return redirect()->route('users.index')
                        ->with('success','User deleted successfully');

    }


}