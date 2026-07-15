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

        self::upsert(
            [['date' => $date, 'last_sequence' => 1]],
            ['date'],
            ['last_sequence' => DB::raw('order_sequences.last_sequence + 1')],
        );

        return (int) self::where('date', $date)->value('last_sequence');
    }
}
