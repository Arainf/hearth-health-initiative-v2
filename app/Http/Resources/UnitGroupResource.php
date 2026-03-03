<?php

namespace App\Http\Resources;

use App\Models\Unit_group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Unit_group */
class UnitGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'unit_group_code' => $this->unit_group_code,
            'unit_group_name' => $this->unit_group_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
