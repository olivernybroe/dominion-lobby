<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Trait HasUserstamps
 * @package App\Models
 * @mixin Model
 * @mixin SoftDeletes
 * @property User creator
 * @property User updator
 */
trait HasUserstamps
{
    public static function bootHasUserstamps()
    {
        static::creating(function (self $model) {
            $model->creator()->associate(auth()->user());
            $model->updator()->associate(auth()->user());
        });
        static::updating(function (self $model) {
            $model->updator()->associate(auth()->user());
        });
    }

    public function creator() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updator() : BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}