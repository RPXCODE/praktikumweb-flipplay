<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaRequest;
use App\Http\Requests\UpdateMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    /**
     * Menampilkan daftar seluruh media yang tersedia di FlipPlay.
     * Mendukung pencarian berdasarkan judul (?title=... / ?search=...) dan filter genre/type.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Media::query();

        // Skenario alur: Pencarian media berdasarkan judul
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->query('title') . '%');
        } elseif ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->query('search') . '%');
        }

        // Filter tipe & genre
        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }
        if ($request->filled('genre')) {
            $query->where('genre', $request->query('genre'));
        }

        $media = $query->latest('id')->paginate($request->integer('per_page', 15));

        return MediaResource::collection($media);
    }

    /**
     * Menambahkan media baru ke dalam sistem (Status Code 201 Created).
     */
    public function store(StoreMediaRequest $request): JsonResponse
    {
        $media = Media::create($request->validated());

        return (new MediaResource($media))
            ->additional([
                'status' => 'success',
                'message' => 'Media baru berhasil ditambahkan ke katalog FlipPlay.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Menampilkan detail satu media beserta pengguna yang memasukkannya ke koleksi (Eager Loading 'users').
     */
    public function show(Media $medium): JsonResponse
    {
        // Eager load relasi pengguna kolektor sesuai ketentuan demo
        $medium->load('users');

        return (new MediaResource($medium))
            ->additional([
                'status' => 'success',
                'message' => 'Detail media berhasil dimuat.',
            ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Memperbarui data media yang ada (Status Code 200 OK).
     */
    public function update(UpdateMediaRequest $request, Media $medium): JsonResponse
    {
        $medium->update($request->validated());

        return (new MediaResource($medium->fresh()))
            ->additional([
                'status' => 'success',
                'message' => 'Data media berhasil diperbarui.',
            ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Menghapus media dari katalog FlipPlay (Status Code 200 OK).
     */
    public function destroy(Media $medium): JsonResponse
    {
        $medium->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Media berhasil dihapus dari katalog FlipPlay.',
        ], 200);
    }
}
