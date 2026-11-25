<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttachMediaRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserCollectionController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna beserta jumlah koleksi media.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::withCount('media')->get();
        return UserResource::collection($users);
    }

    /**
     * Menampilkan profil pengguna tertentu.
     */
    public function show(User $user): JsonResponse
    {
        $user->loadCount('media');
        return (new UserResource($user))
            ->additional([
                'status' => 'success',
                'message' => 'Data pengguna berhasil dimuat.',
            ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Menampilkan koleksi media yang dimiliki seorang pengguna (Eager Loading 'media').
     */
    public function getCollection(User $user): JsonResponse
    {
        $user->load('media');

        return (new UserResource($user))
            ->additional([
                'status' => 'success',
                'message' => "Daftar koleksi media milik pengguna '{$user->name}' berhasil dimuat.",
            ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Custom Action: Menambahkan media ke koleksi pribadi pengguna (Attach).
     * Termasuk penanganan error 422 saat menambahkan media yang sama dua kali.
     */
    public function attachMedia(AttachMediaRequest $request, User $user): JsonResponse
    {
        $mediaId = (int) $request->validated()['media_id'];
        $media = Media::findOrFail($mediaId);

        // Validasi duplikasi koleksi (skenario failure path yang diuji)
        if ($user->media()->where('media.id', $mediaId)->exists()) {
            return response()->json([
                'message' => 'Validasi gagal: media ini sudah ada di dalam koleksi pribadi pengguna.',
                'errors' => [
                    'media_id' => ["Media '{$media->title}' sudah ada di dalam playlist/koleksi pengguna ini."],
                ],
            ], 422);
        }

        // Attach relasi
        $user->media()->attach($mediaId);

        return response()->json([
            'status' => 'success',
            'message' => "Media '{$media->title}' berhasil ditambahkan ke koleksi pengguna '{$user->name}'.",
            'data' => [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'media' => new MediaResource($media),
            ],
        ], 201);
    }

    /**
     * Custom Action: Menghapus media dari koleksi pengguna (Detach).
     */
    public function detachMedia(User $user, int $mediaId): JsonResponse
    {
        $exists = $user->media()->where('media.id', $mediaId)->exists();

        if (!$exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Media tidak ditemukan di dalam koleksi pengguna ini.',
            ], 404);
        }

        $user->media()->detach($mediaId);

        return response()->json([
            'status' => 'success',
            'message' => 'Media berhasil dihapus dari koleksi pengguna.',
        ], 200);
    }
}
