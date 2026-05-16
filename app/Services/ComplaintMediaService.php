<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ComplaintMediaService
{
    /**
     * Misafir taslakları için yükle → storage/app altında sakla (public değil).
     *
     * @return array<int, array{path: string, type: 'image'|'video', original: string}>
     */
    public function storeDraftUploads(Request $request): array
    {
        $sessionId = $request->session()->getId();
        $basePrefix = trim((string) config('complaint.draft_media_path', 'complaint_drafts'), '/');
        $baseRel = $basePrefix.'/'.$sessionId;

        /** @var list<UploadedFile> $images */
        $images = array_values($request->file('images', []) ?: []);

        /** @var list<UploadedFile> $videos */
        $videos = array_values($request->file('videos', []) ?: []);

        $out = [];

        foreach ($images as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }
            $path = $file->storeAs($baseRel, Str::uuid().'.'.$file->guessExtension(), ['disk' => 'local']);
            $out[] = [
                'path' => $path,
                'type' => 'image',
                'original' => $file->getClientOriginalName(),
            ];
        }

        foreach ($videos as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }
            $path = $file->storeAs($baseRel, Str::uuid().'.'.$file->guessExtension(), ['disk' => 'local']);
            $out[] = [
                'path' => $path,
                'type' => 'video',
                'original' => $file->getClientOriginalName(),
            ];
        }

        return $out;
    }

    /**
     * Taslak medyayı gönderiye taşır, public diskte yayınlar.
     *
     * @param  array<int, array{path: string, type: 'image'|'video', original?: string}>  $draft
     * @return list<array{type: string, url: string}>
     */
    public function promoteDraftToPost(Post $post, array $draft): array
    {
        $mediaItems = [];

        foreach ($draft as $row) {
            $rel = isset($row['path']) ? (string) $row['path'] : '';
            $type = ($row['type'] ?? '') === 'video' ? 'video' : 'image';

            if ($rel === '' || ! Storage::disk('local')->exists($rel)) {
                continue;
            }

            $ext = pathinfo($rel, PATHINFO_EXTENSION) ?: ($type === 'video' ? 'mp4' : 'jpg');
            $destRel = 'posts/'.$post->id.'/'.Str::uuid().'.'.$ext;

            Storage::disk('public')->put($destRel, Storage::disk('local')->get($rel));
            Storage::disk('local')->delete($rel);

            /** @phpstan-ignore-next-line */
            $mediaItems[] = [
                'type' => $type,
                'url' => Storage::disk('public')->url($destRel),
            ];
        }

        if ($draft !== [] && isset($draft[0]['path'])) {
            $dir = dirname((string) $draft[0]['path']);
            $prefix = trim((string) config('complaint.draft_media_path', 'complaint_drafts'), '/');
            if (str_starts_with($dir, $prefix)) {
                Storage::disk('local')->deleteDirectory($dir);
            }
        }

        return $mediaItems;
    }

    /**
     * @return list<array{type: string, url: string}>
     */
    public function storeAuthenticatedUploads(Request $request, Post $post): array
    {
        $items = [];

        foreach (array_values($request->file('images', []) ?: []) as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }
            $path = $file->store('posts/'.$post->id, 'public');
            $items[] = ['type' => 'image', 'url' => Storage::disk('public')->url($path)];
        }

        foreach (array_values($request->file('videos', []) ?: []) as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }
            $path = $file->store('posts/'.$post->id, 'public');
            $items[] = ['type' => 'video', 'url' => Storage::disk('public')->url($path)];
        }

        return $items;
    }
}
