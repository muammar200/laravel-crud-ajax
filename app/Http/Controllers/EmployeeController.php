<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index() {
        return view('index');
    }
 
    // public function fetchAll() {
    //     $employee = Employee::all();
    //     $output = '';
    //     if ($employee->count() > 0) {
    //         $output .= '<table class="table table-striped align-middle">
    //         <thead>
    //           <tr>
    //             <th>ID</th>
    //             <th>Avatar</th>
    //             <th>Name</th>
    //             <th>E-mail</th>
    //             <th>Action</th>
    //           </tr>
    //         </thead>
    //         <tbody>';
    //         foreach ($employee as $rs) {
    //             $output .= '<tr>
    //             <td>' . $rs->id . '</td>
    //             <td><img src="storage/images/' . $rs->avatar . '" width="50" class="img-thumbnail rounded-circle"></td>
    //             <td>' . $rs->first_name . ' ' . $rs->last_name . '</td>
    //             <td>' . $rs->email . '</td>
    //             <td>
    //               <a href="#" id="' . $rs->id . '" class="text-success mx-1 editIcon" data-bs-toggle="modal" data-bs-target="#editEmployeeModal"><i class="bi-pencil-square h4"></i></a>
    //               <a href="#" id="' . $rs->id . '" class="text-danger mx-1 deleteIcon"><i class="bi-trash h4"></i></a>
    //             </td>
    //           </tr>';
    //         }
    //         $output .= '</tbody></table>';
    //         echo $output;
    //     } else {
    //         echo '<h1 class="text-center text-secondary my-5">No record in the database!</h1>';
    //     }
    // }

    public function fetchAll(){
    // $employees = Employee::all();
    // $employees = Employee::orderBy('created_at', 'desc')->get();
        
    $employees = DB::table('employees')
        ->select(['id', 'first_name', 'last_name', 'email', 'avatar'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($employee){
        $employee->full_name = $employee->first_name . ' ' . $employee->last_name;
        return $employee;
    });

    return Datatables::of($employees)->toJson();
    }
 
    public function store(Request $request) {
        // Data Validation
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:20',
            'last_name' => 'required|max:22',
            'email' => 'required|email|unique:employees',
            'avatar' => 'image|file|max:1024'
        ]);
        
        $messagesError = [
            'required' => ':attribute harus diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'email' => ':attribute harus berupa alamat email yang valid.',
            'unique' => ':attribute sudah ada di dalam database.',
            'image' => ':attribute harus berupa gambar.',
            'file' => ':attribute harus berupa file.',
            'avatar.max' => ':attribute maksimal :max KB.',
        ];

        // Mengaitkan atribut dengan message error
        $attributesError = [
            'first_name' => 'Nama Depan',
            'last_name' => 'Nama Belakang',
            'email' => 'Alamat Email',
            'avatar' => 'Foto Profil'
        ];

        $validator->setCustomMessages($messagesError);
        $validator->setAttributeNames($attributesError);
                
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        } 
        
        $file = $request->file('avatar');
        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/images', $fileName); //php artisan storage:link

        $now = now()->timezone('Asia/Makassar');
        $employeeData = [
            'first_name' => $request->first_name, 
            'last_name' => $request->last_name, 
            'email' => $request->email, 
            'avatar' => $fileName, 
            'created_at' => $now,   
            'updated_at' => $now,    
        ];

        // $empData['avatar'] = $request->file('avatar')->store('public/images'); //php artisan storage:link
        // Employee::create($empData);
        DB::table('employees')->insert($employeeData);
        return response()->json([
            'status' => 'success',
        ]);
    }
 
    // edit an employee ajax request
    public function edit(Request $request) {
        $id = $request->id;
        // $employee = Employee::find($id);
        // return response()->json($employee);
        $employee = DB::table('employees')
            ->where('id', $id)
            ->first();
        return response()->json($employee);
    }
 
    // update an employee ajax request
    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:20',
            'last_name' => 'required|max:22',
            'email' => 'required|email',
            'avatar' => 'image|file|max:1024'
        ]);    
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ]);
        }

        $fileName = '';
        // $emp = Employee::find($request->emp_id);
        $findEmployeeAvatar = DB::table('employees')
            ->select('avatar')
            ->where('id', $request->employee_id)
            ->first();
        // return response()->json($findEmployeeAvatar);
        if ($request->hasFile('avatar')) {
            // Condition if there are file send 
            $file = $request->file('avatar');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/images', $fileName);
            if ($findEmployeeAvatar->avatar) {
                Storage::delete('public/images/' . $findEmployeeAvatar->avatar);
            }
        } else {
            $fileName = $request->employee_avatar;
        }

        $now = now()->timezone('Asia/Makassar');
        $employeeData = [
            'first_name' => $request->first_name, 
            'last_name' => $request->last_name, 
            'email' => $request->email, 
            'avatar' => $fileName,
            'updated_at' => $now, 
        ];
 
        // $emp->update($empData);
        DB::table('employees')
        ->where('id', $request->employee_id)
        ->update($employeeData);
        return response()->json([
            'status' => 200,
        ]);
    }
 
    // delete an employee ajax request
    public function delete(Request $request) {
        $id = $request->id;
        // return response()->json($id);
        $findEmployeeAvatar = DB::table('employees')
            ->select('avatar')
            ->where('id', $request->id)
            ->first();
        // return response()->json($findEmployeeAvatar->avatar);

        if($findEmployeeAvatar){
            Storage::delete('public/images/' . $findEmployeeAvatar->avatar);

            DB::table('employees')->where('id', $id)->delete();
            return response()->json([
                'status' => 200,
            ]);
        } else {
            return response()->json([
                'status' => 400,
            ]);    
        }

        // $emp = Employee::find($id);
        // if($emp){
        //     Storage::delete('public/images/' . $emp->avatar);
        //     Employee::destroy($id);
        //     return response()->json([
        //         'status' => 200,
        //     ]);
        // } else{
        //     return response()->json([
        //         'status' => 400,
        //     ]);
        // } 
    }
}