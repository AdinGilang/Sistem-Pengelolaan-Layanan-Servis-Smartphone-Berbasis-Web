<?php

namespace App\Modules\Servis\Requests;

class UpdateServisRequest extends StoreServisRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('servis')) ?? false;
    }
}
