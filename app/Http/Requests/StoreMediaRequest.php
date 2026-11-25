<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255', 'unique:media,title'],
            'type' => ['required', 'string', 'in:movie,music'],
            'genre' => ['required', 'string', 'max:100'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'release_year' => ['required', 'integer', 'min:1880', 'max:' . (date('Y') + 5)],
            // Field khusus Movie
            'director' => ['nullable', 'string', 'max:255', 'required_if:type,movie'],
            'age_rating' => ['nullable', 'string', 'max:20'],
            // Field khusus Music
            'artist' => ['nullable', 'string', 'max:255', 'required_if:type,music'],
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
            'title.required' => 'Judul media wajib diisi.',
            'title.unique' => 'Judul media sudah ada di dalam katalog (duplikat).',
            'type.required' => 'Jenis media wajib diisi.',
            'type.in' => 'Jenis media harus bernilai movie atau music.',
            'genre.required' => 'Genre media wajib diisi.',
            'duration_minutes.required' => 'Durasi media wajib diisi (dalam menit).',
            'duration_minutes.integer' => 'Durasi media harus berupa angka bilangan bulat.',
            'duration_minutes.min' => 'Durasi media minimal 1 menit.',
            'release_year.required' => 'Tahun rilis wajib diisi.',
            'director.required_if' => 'Sutradara wajib diisi jika jenis media adalah movie.',
            'artist.required_if' => 'Nama artis wajib diisi jika jenis media adalah music.',
        ];
    }
}
