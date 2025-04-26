<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DetailTransaction extends Model
{
    use HasFactory;

    protected $table = 'detail_transaction';
    protected $keyType = 'string'; // pakai UUID
    public $incrementing = false; // UUID bukan auto-increment

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
