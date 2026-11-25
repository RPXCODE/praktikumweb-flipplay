<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = [
            [
                'title' => 'Inception',
                'type' => 'movie',
                'genre' => 'Sci-Fi',
                'duration_minutes' => 148,
                'release_year' => 2010,
                'director' => 'Christopher Nolan',
                'age_rating' => 'PG-13',
            ],
            [
                'title' => 'Interstellar',
                'type' => 'movie',
                'genre' => 'Sci-Fi',
                'duration_minutes' => 169,
                'release_year' => 2014,
                'director' => 'Christopher Nolan',
                'age_rating' => 'PG-13',
            ],
            [
                'title' => 'Spirited Away',
                'type' => 'movie',
                'genre' => 'Animation',
                'duration_minutes' => 125,
                'release_year' => 2001,
                'director' => 'Hayao Miyazaki',
                'age_rating' => 'PG',
            ],
        ];

        $musics = [
            [
                'title' => 'Bohemian Rhapsody',
                'type' => 'music',
                'genre' => 'Rock',
                'duration_minutes' => 6,
                'release_year' => 1975,
                'artist' => 'Queen',
                'album' => 'A Night at the Opera',
            ],
            [
                'title' => 'Viva La Vida',
                'type' => 'music',
                'genre' => 'Alternative',
                'duration_minutes' => 4,
                'release_year' => 2008,
                'artist' => 'Coldplay',
                'album' => 'Viva la Vida',
            ],
            [
                'title' => 'Sialan',
                'type' => 'music',
                'genre' => 'Pop',
                'duration_minutes' => 4,
                'release_year' => 2023,
                'artist' => 'Juicy Luicy ft. Adrian Khalif',
                'album' => 'Non-Album Single',
            ],
        ];

        foreach (array_merge($movies, $musics) as $item) {
            Media::firstOrCreate(['title' => $item['title']], $item);
        }

        // Hubungkan media awal ke koleksi user IQODHUL HIMAM (seperti simulasi Modul 3)
        $user = User::where('email', 'himam@example.com')->first();
        if ($user) {
            $initialMediaIds = Media::whereIn('title', ['Inception', 'Bohemian Rhapsody'])->pluck('id');
            $user->media()->syncWithoutDetaching($initialMediaIds);
        }
    }
}
