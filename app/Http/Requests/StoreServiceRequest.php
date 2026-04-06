<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Rules\UniqueSlugFromName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Service::class);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services', 'name'),
                new UniqueSlugFromName(Service::class),
            ],
            'industry_id' => ['required', 'integer', 'exists:industries,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
        ]);
    }
}
