<?php

namespace App\Http\Requests;

use App\Models\Industry;
use App\Rules\UniqueSlugFromName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateIndustryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $industry = $this->route('industry');

        return $industry !== null && Gate::allows('update', $industry);
    }

    public function rules(): array
    {
        $industry = $this->route('industry');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('industries', 'name')->ignore($industry),
                new UniqueSlugFromName(Industry::class, $industry),
            ],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->input('name')) ? trim($this->input('name')) : $this->input('name'),
            'description' => is_string($this->input('description')) ? trim($this->input('description')) : $this->input('description'),
            'icon' => is_string($this->input('icon')) ? trim($this->input('icon')) : $this->input('icon'),
        ]);
    }
}
