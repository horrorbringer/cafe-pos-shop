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
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement(
                'INSERT INTO order_sequences (date, last_sequence, created_at, updated_at)
                 VALUES (?, 1, NOW(), NOW())
                 ON CONFLICT (date) DO UPDATE SET last_sequence = order_sequences.last_sequence + 1, updated_at = NOW()',
                [$date],
            );
        } else {
            DB::statement(
                'INSERT INTO order_sequences (date, last_sequence, created_at, updated_at)
                 VALUES (?, 1, NOW(), NOW())
                 ON DUPLICATE KEY UPDATE last_sequence = last_sequence + 1',
                [$date],
            );
        }

        return (int) self::where('date', $date)->value('last_sequence');
    }
}
