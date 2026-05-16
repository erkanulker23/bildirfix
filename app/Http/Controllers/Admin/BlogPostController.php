<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\PostModerationStatus;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->with('author:id,name')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('admin.blog.index', ['posts' => $posts]);
    }

    public function create(): View
    {
        return view('admin.blog.create', ['post' => new BlogPost]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, null);
        $data['author_user_id'] = $request->user()?->id;
        $data = $this->applyBlogModerationRules($request->user(), $data);

        $post = BlogPost::query()->create($data);

        $message = __('Yazı oluşturuldu.');
        if (
            ! $request->user()?->isSuperAdmin()
            && ($data['is_published'] ?? false)
            && ($data['moderation_status'] ?? null) === PostModerationStatus::Pending
        ) {
            $message = __('Yazı kaydedildi; süper yönetici onayından sonra sitede yayınlanacak.');
        }

        return redirect()
            ->route('admin.blog.edit', $post)
            ->with('status', $message);
    }

    public function edit(BlogPost $blog): View
    {
        return view('admin.blog.edit', ['post' => $blog]);
    }

    public function update(Request $request, BlogPost $blog): RedirectResponse
    {
        $data = $this->validated($request, $blog);
        $data = $this->applyBlogModerationRules($request->user(), $data);
        $blog->fill($data);
        $blog->save();

        $message = __('Kaydedildi.');
        if (
            ! $request->user()?->isSuperAdmin()
            && ($data['is_published'] ?? false)
            && ($data['moderation_status'] ?? null) === PostModerationStatus::Pending
        ) {
            $message = __('Kaydedildi; süper yönetici onayı bekleniyor.');
        }

        return redirect()
            ->route('admin.blog.edit', $blog)
            ->with('status', $message);
    }

    public function destroy(BlogPost $blog): RedirectResponse
    {
        $blog->delete();

        return redirect()
            ->route('admin.blog.index')
            ->with('status', __('Yazı silindi.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?BlogPost $existing): array
    {
        $slugRule = Rule::unique('blog_posts', 'slug');
        if ($existing !== null) {
            $slugRule = $slugRule->ignore($existing->id);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slugRule],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:100000'],
            'hero_image_url' => ['nullable', 'string', 'max:2048'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_published' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        $slug = trim((string) ($validated['slug'] ?? ''));
        if ($slug === '') {
            if ($existing === null) {
                $slug = BlogPost::uniqueSlugFromTitle((string) $validated['title']);
            } else {
                $base = Str::slug((string) $validated['title']);
                if ($base === '') {
                    $base = 'yazi';
                }
                $candidate = $base;
                $i = 1;
                while (
                    BlogPost::query()->where('slug', $candidate)->where('id', '!=', $existing->id)->exists()
                ) {
                    $candidate = $base.'-'.$i++;
                }
                $slug = $candidate;
            }
        }

        $validated['slug'] = $slug;

        if (! $validated['is_published']) {
            $validated['published_at'] = null;
        } else {
            if (empty($validated['published_at'])) {
                $validated['published_at'] = now();
            }
        }

        if (($validated['hero_image_url'] ?? '') === '') {
            $validated['hero_image_url'] = null;
        }
        if (($validated['excerpt'] ?? '') === '') {
            $validated['excerpt'] = null;
        }
        if (($validated['meta_title'] ?? '') === '') {
            $validated['meta_title'] = null;
        }
        if (($validated['meta_description'] ?? '') === '') {
            $validated['meta_description'] = null;
        }

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function applyBlogModerationRules(?User $user, array $validated): array
    {
        if ($user === null) {
            $validated['moderation_status'] = PostModerationStatus::Approved;

            return $validated;
        }

        if ($user->isSuperAdmin()) {
            if ($validated['is_published']) {
                $validated['moderation_status'] = PostModerationStatus::Approved;
                $validated['moderated_at'] = now();
                $validated['moderated_by_user_id'] = $user->id;
                $validated['moderation_note'] = null;
            } else {
                $validated['moderation_status'] = PostModerationStatus::Approved;
                $validated['moderated_at'] = null;
                $validated['moderated_by_user_id'] = null;
                $validated['moderation_note'] = null;
            }

            return $validated;
        }

        if ($validated['is_published']) {
            $validated['moderation_status'] = PostModerationStatus::Pending;
            $validated['moderated_at'] = null;
            $validated['moderated_by_user_id'] = null;
            $validated['moderation_note'] = null;
        } else {
            $validated['moderation_status'] = PostModerationStatus::Approved;
        }

        return $validated;
    }
}
