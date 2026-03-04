<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Generated_reports extends Model
{
    protected $fillable = [
        'generated_text',
        'staff_generated',
        'staff_updates',
    ];

    public function records(): HasOne
    {
        return $this->hasOne(Records::class, 'generated_id');
    }

    /**
     * Update a generated report for a record
     * @throws \Throwable
     */
    public static function updateGeneratedRecord($record, string $content, bool $approve = false): Records
    {
        if (!$record->generated_report) {
            throw new \Exception('Generated report not found.');
        }

        DB::transaction(function () use ($record, $content, $approve) {

            // 1️⃣ Update existing report
            $record->generated_report->update([
                'generated_text' => $content,
                'staff_updates'  => auth()->id() ?? 0,
            ]);

            if ($approve) {

                if ($record->status_id === 1) {
                    throw new \Exception(
                        'This record has already been approved.'
                    );
                }

                $record->update([
                    'status_id'   => 1,
                    'approved_by' => auth()->id(),
                ]);
            }
        });

        return $record->fresh()->load('status');

    }



}
