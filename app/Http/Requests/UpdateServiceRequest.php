<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Rules\UniqueSlugFromName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $service = $this->route('service');

        return $service !== null && Gate::allows('update', $service);
    }

    public function rules(): array
    {
        $service = $this->route('service');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('services', 'name')->ignore($service),
                new UniqueSlugFromName(Service::class, $service),
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
