@php
    /** @var \App\Models\BlogPost $post */
@endphp

<div>
    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Başlık') }}</label>
    <input name="title" type="text" required value="{{ old('title', $post->title) }}"
        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
    @error('title')
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Slug') }} <span class="font-normal normal-case text-slate-400">({{ __('boş bırakılırsa başlıktan üretilir') }})</span></label>
    <input name="slug" type="text" value="{{ old('slug', $post->slug) }}" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="ornek-yazi-basligi"
        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 font-mono text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
    @error('slug')
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Özet (liste + meta)') }}</label>
    <textarea name="excerpt" rows="2" maxlength="500"
        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm leading-relaxed text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">{{ old('excerpt', $post->excerpt) }}</textarea>
    @error('excerpt')
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('İçerik (Markdown)') }}</label>
    <textarea name="body" rows="18" required
        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 font-mono text-[13px] leading-relaxed text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">{{ old('body', $post->body) }}</textarea>
    <p class="mt-1 text-xs text-slate-500">{{ __('Başlıklar, listeler, bağlantılar ve kod blokları desteklenir.') }}</p>
    @error('body')
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Kapak görseli URL') }}</label>
    <input name="hero_image_url" type="url" value="{{ old('hero_image_url', $post->hero_image_url) }}" placeholder="https://…"
        class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
    @error('hero_image_url')
        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>

<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('SEO başlık') }}</label>
        <input name="meta_title" type="text" value="{{ old('meta_title', $post->meta_title) }}"
            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
        @error('meta_title')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('SEO açıklama') }}</label>
        <textarea name="meta_description" rows="2" maxlength="500"
            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">{{ old('meta_description', $post->meta_description) }}</textarea>
        @error('meta_description')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="flex flex-wrap items-center gap-6 rounded-xl border border-slate-200 bg-slate-50 px-5 py-4">
    <label class="flex cursor-pointer items-center gap-3 text-sm font-bold text-slate-800">
        <input type="checkbox" name="is_published" value="1" class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
            @checked(old('is_published', $post->is_published))>
        {{ __('Yayında (herkese açık talebi)') }}
    </label>
    <div class="min-w-[200px] flex-1">
        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ __('Yayın tarihi') }}</label>
        <input name="published_at" type="datetime-local"
            value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}"
            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
        @error('published_at')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>

@if (! auth()->user()?->isSuperAdmin())
    <p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs font-semibold text-amber-900">
        {{ __('Yayımla işaretlediğiniz yazılar süper yönetici onayından sonra sitede görünür.') }}
    </p>
@endif
