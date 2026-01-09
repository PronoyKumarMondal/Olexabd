<?php

namespace App\Observers;

class AuditObserver
{
    public function saving($model)
    {
        if (auth('admin')->check()) {
            $model->updated_by = auth('admin')->id();
        }
    }

    public function created($model)
    {
        if (auth('admin')->check()) {
            \Illuminate\Support\Facades\Log::info("Created " . class_basename($model) . " (ID: {$model->id}) by Admin: " . auth('admin')->user()->name);
        }
    }

    public function updated($model)
    {
        if (auth('admin')->check()) {
            // Only log if clean dirty changes exist
            if($model->wasChanged()){
                 \Illuminate\Support\Facades\Log::info("Updated " . class_basename($model) . " (ID: {$model->id}) by Admin: " . auth('admin')->user()->name . " | Changes: " . json_encode($model->getChanges()));
            }
        }
    }

    public function deleted($model)
    {
        if (auth('admin')->check()) {
            \Illuminate\Support\Facades\Log::info("Deleted " . class_basename($model) . " (ID: {$model->id}) by Admin: " . auth('admin')->user()->name);
        }
    }
}
