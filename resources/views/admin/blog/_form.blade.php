@php
    /** @var \App\Models\BlogPost $post */
    /** @var \Illuminate\Support\Collection<int, \App\Models\BlogCategory> $categories */
    $categories = $categories ?? collect();
@endphp

<div class="grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-5">
        <div>
            <label class="psc-field__label">{{ __('Başlık') }}</label>
            <input name="title" type="text" required value="{{ old('title', $post->title) }}" class="psc-input mt-2">
            @error('title')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label class="psc-field__label">{{ __('Kategori') }}</label>
                <select name="blog_category_id" class="psc-select mt-2">
                    <option value="">{{ __('Seçin') }}</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected((string) old('blog_category_id', $post->blog_category_id) === (string) $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                @error('blog_category_id')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="psc-field__label">{{ __('Slug') }} <span class="font-normal normal-case text-slate-400">({{ __('opsiyonel') }})</span></label>
                <input name="slug" type="text" value="{{ old('slug', $post->slug) }}" pattern="[a-z0-9]+(?:-[a-z0-9]+)*"
                    placeholder="ornek-yazi" class="psc-input mt-2 font-mono text-sm">
                @error('slug')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="psc-field__label">{{ __('Özet (liste + meta)') }}</label>
            <textarea name="excerpt" rows="3" maxlength="500" class="psc-input mt-2 !h-auto py-3">{{ old('excerpt', $post->excerpt) }}</textarea>
            @error('excerpt')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="psc-field__label">{{ __('İçerik') }}</label>
            <div class="psc-editor-wrap mt-2">
                <div id="blog-body-editor"></div>
            </div>
            <textarea name="body" id="blog-body-input" class="hidden" required>{{ old('body', $post->body) }}</textarea>
            <p class="mt-1 text-xs text-slate-500">{{ __('Zengin metin editörü — başlık, liste, bağlantı ve görsel ekleyebilirsiniz.') }}</p>
            @error('body')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="space-y-5">
        <div class="psc-card">
            <div class="psc-card__body space-y-4">
                <p class="psc-card__title">{{ __('Yayın') }}</p>
                <label class="flex cursor-pointer items-center gap-3 text-sm font-semibold text-slate-800">
                    <input type="checkbox" name="is_published" value="1" class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500"
                        @checked(old('is_published', $post->is_published))>
                    {{ __('Yayında') }}
                </label>
                <div>
                    <label class="psc-field__label">{{ __('Yayın tarihi') }}</label>
                    <input name="published_at" type="datetime-local"
                        value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
                        class="psc-input mt-2">
                </div>
            </div>
        </div>

        <div class="psc-card">
            <div class="psc-card__body space-y-4">
                <p class="psc-card__title">{{ __('Kapak görseli') }}</p>
                <input name="hero_image_url" type="url" value="{{ old('hero_image_url', $post->hero_image_url) }}"
                    placeholder="https://…" class="psc-input">
                @error('hero_image_url')
                    <p class="text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="psc-card">
            <div class="psc-card__body space-y-4">
                <p class="psc-card__title">{{ __('SEO') }}</p>
                <div>
                    <label class="psc-field__label">{{ __('Meta başlık') }}</label>
                    <input name="meta_title" type="text" value="{{ old('meta_title', $post->meta_title) }}" class="psc-input mt-2">
                </div>
                <div>
                    <label class="psc-field__label">{{ __('Meta açıklama') }}</label>
                    <textarea name="meta_description" rows="3" maxlength="500" class="psc-input mt-2 !h-auto py-3">{{ old('meta_description', $post->meta_description) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hidden = document.getElementById('blog-body-input');
            const editor = new Quill('#blog-body-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [2, 3, false] }],
                        ['bold', 'italic', 'underline', 'link'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['blockquote', 'code-block'],
                        ['clean'],
                    ],
                },
            });
            if (hidden?.value?.trim()) {
                editor.clipboard.dangerouslyPasteHTML(hidden.value);
            }
            const form = hidden?.closest('form');
            form?.addEventListener('submit', function () {
                hidden.value = editor.root.innerHTML;
            });
        });
    </script>
@endpush
