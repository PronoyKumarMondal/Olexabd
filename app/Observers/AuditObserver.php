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
}
