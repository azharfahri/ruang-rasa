<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->get();
        return view('admin.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('admin.branches.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'address'    => 'required|string',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'open_time'  => 'required',
            'close_time' => 'required',
        ]);

        Branch::create($data);

        return redirect()->route('branches.index')->with('success','Cabang berhasil ditambahkan');
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'address'    => 'required|string',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'open_time'  => 'required',
            'close_time' => 'required',
        ]);

        $branch->update($data);

        return redirect()->route('branches.index')->with('success','Cabang berhasil diperbarui');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')->with('success','Cabang berhasil dihapus');
    }
}
