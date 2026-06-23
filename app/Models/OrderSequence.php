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

        return DB::transaction(function () use ($date, $driver) {
            if ($driver === 'mysql') {
                DB::statement(
                    'INSERT INTO order_sequences (date, last_sequence, created_at, updated_at)
                     VALUES (?, 1, NOW(), NOW())
                     ON DUPLICATE KEY UPDATE last_sequence = last_sequence + 1',
                    [$date],
                );
            } else {
                DB::statement(
                    'INSERT INTO order_sequences (date, last_sequence, created_at, updated_at)
                     VALUES (?, 1, datetime(\'now\'), datetime(\'now\'))
                     ON CONFLICT(date) DO UPDATE SET last_sequence = last_sequence + 1',
                    [$date],
                );
            }

            return (int) DB::selectOne(
                'SELECT last_sequence FROM order_sequences WHERE date = ?',
                [$date],
            )->last_sequence;
        });
    }
}
