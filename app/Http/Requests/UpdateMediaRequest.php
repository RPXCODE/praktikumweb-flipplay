<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaRequest extends FormRequest
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
        $mediaId = $this->route('medium') ?? $this->route('media');
        if (is_object($mediaId)) {
            $mediaId = $mediaId->id;
        }

        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('media', 'title')->ignore($mediaId),
            ],
            'type' => ['sometimes', 'required', 'string', 'in:movie,music'],
            'genre' => ['sometimes', 'required', 'string', 'max:100'],
            'duration_minutes' => ['sometimes', 'required', 'integer', 'min:1'],
            'release_year' => ['sometimes', 'required', 'integer', 'min:1880', 'max:' . (date('Y') + 5)],
            'director' => ['nullable', 'string', 'max:255'],
            'age_rating' => ['nullable', 'string', 'max:20'],
            'artist' => ['nullable', 'string', 'max:255'],
            'album' => ['nullable', 'string', 'max:255'],
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
            'title.required' => 'Judul media tidak boleh kosong.',
            'title.unique' => 'Judul media sudah digunakan oleh media lain.',
            'type.in' => 'Jenis media harus bernilai movie atau music.',
            'duration_minutes.min' => 'Durasi media minimal 1 menit.',
        ];
    }
}
