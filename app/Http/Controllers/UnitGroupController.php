<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitGroupRequest;
use App\Http\Resources\UnitGroupResource;
use App\Models\Unit_group;

class UnitGroupController extends Controller
{
    public function index()
    {
        return UnitGroupResource::collection(Unit_group::all());
    }

    public function store(UnitGroupRequest $request)
    {
        return new UnitGroupResource(Unit_group::create($request->validated()));
    }

    public function show(Unit_group $unit_group)
    {
        return new UnitGroupResource($unit_group);
    }

    public function update(UnitGroupRequest $request, Unit_group $unit_group)
    {
        $unit_group->update($request->validated());

        return new UnitGroupResource($unit_group);
    }

    public function destroy(Unit_group $unit_group)
    {
        $unit_group->delete();

        return response()->json();
    }
}
