<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

#[Fillable(['date', 'last_sequence'])]
class OrderSequence extends Model
{
    public $timestamps = true;

    protected $casts = [
        'date' => 'date',
        'last_sequence' => 'integer',
    ];

    public static function getNextSequence(): int
    {
        $date = now()->toDateString();

        return DB::transaction(function () use ($date) {
            $sequence = self::lockForUpdate()->firstOrCreate(
                ['date' => $date],
                ['last_sequence' => 0],
            );

            $sequence->increment('last_sequence');

            return $sequence->last_sequence;
        });
    }
}
