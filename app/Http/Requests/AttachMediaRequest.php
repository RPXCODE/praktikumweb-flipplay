<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'media_id' => ['required', 'integer', 'exists:media,id'],
        ];
    }

    /**
     * Pesan validasi kustom.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'media_id.required' => 'Parameter media_id wajib diisi.',
            'media_id.integer' => 'Parameter media_id harus berupa angka integer ID media.',
            'media_id.exists' => 'Media yang dipilih tidak ditemukan di dalam sistem.',
        ];
    }
}
