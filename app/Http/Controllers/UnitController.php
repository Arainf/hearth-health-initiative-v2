<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;

class UnitController extends Controller
{
    public function index()
    {
        return UnitResource::collection(Unit::all());
    }

    public function store(UnitRequest $request)
    {
        return new UnitResource(Unit::create($request->validated()));
    }

    public function show(Unit $unit)
    {
        return new UnitResource($unit);
    }

    public function update(UnitRequest $request, Unit $unit)
    {
        $unit->update($request->validated());

        return new UnitResource($unit);
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return response()->json();
    }
}
