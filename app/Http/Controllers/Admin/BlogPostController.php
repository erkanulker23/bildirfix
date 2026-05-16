<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
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

        $post = new BlogPost($data);
        $post->author_user_id = $request->user()?->id;
        $post->save();

        return redirect()
            ->route('admin.blog.edit', $post)
            ->with('status', __('Yazı oluşturuldu.'));
    }

    public function edit(BlogPost $blog): View
    {
        return view('admin.blog.edit', ['post' => $blog]);
    }

    public function update(Request $request, BlogPost $blog): RedirectResponse
    {
        $blog->fill($this->validated($request, $blog));
        $blog->save();

        return redirect()
            ->route('admin.blog.edit', $blog)
            ->with('status', __('Kaydedildi.'));
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
}
