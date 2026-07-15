<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasStringId
{
    /**
     * Boot the trait
     */
    protected static function bootHasStringId(): void
    {
        static::creating(function (Model $model) {
            // Only generate ULID if no ID is provided or if the provided ID is empty
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = static::generateUlid();
            } else {
                // Validate that the provided ID (any string) is unique
                $existingModel = static::where($model->getKeyName(), $model->{$model->getKeyName()})->first();
                if ($existingModel) {
                    throw new \InvalidArgumentException("The provided ID '{$model->{$model->getKeyName()}}' already exists.");
                }
                // Accept any string ID provided by the user - no format validation
            }
        });

        static::updating(function (Model $model) {
            // Prevent ID changes during updates
            if ($model->isDirty($model->getKeyName())) {
                throw new \InvalidArgumentException('The ID field cannot be modified during updates.');
            }
        });
    }

    /**
     * Generate a new ULID (only used when no ID is provided)
     */
    public static function generateUlid(): string
    {
        return Str::ulid()->toString();
    }

    /**
     * Initialize the model's attributes
     */
    public function initializeHasStringId(): void
    {
        $this->incrementing = false;
        $this->keyType = 'string';
    }
}
