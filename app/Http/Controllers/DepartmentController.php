<?php

namespace App\Http\Controllers;


use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{


    public function index()
    {
        $departments = Department::all();
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        Department::create($request->all());
        return redirect()->route('departments.index');
    }
}
