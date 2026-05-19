<footer class="safe-bottom mt-14 border-t border-neutral-200/90 bg-gradient-to-b from-[#faf9f7] to-[#f0eeeb] py-12 text-[15px] text-neutral-600">
    <div class="mx-auto grid max-w-[1200px] gap-10 px-5 sm:grid-cols-2 sm:gap-12 lg:grid-cols-4">
        <div class="sm:col-span-2 lg:col-span-1">
            <p class="font-heading text-xl font-extrabold text-neutral-950">{{ config('app.name') }}</p>
            <p class="mt-3 max-w-sm text-[15px] leading-relaxed text-neutral-600">
                {{ __('Kent ve mahalle düzeyinde sorun bildirimi: fotoğraf, konum ve moderasyon ile şeffaf süreç. Resmi kanallar yerine geçmez.') }}
            </p>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-neutral-500">{{ __('Keşfet') }}</p>
            <ul class="mt-4 space-y-2.5 text-[15px] font-semibold text-neutral-900">
                <li><a href="{{ route('cities.explore') }}" class="hover:text-primary hover:underline">{{ __('Şehrini keşfet') }}</a></li>
                <li><a href="{{ route('campaigns.index') }}" class="hover:text-primary hover:underline">{{ __('Sosyal kampanyalar') }}</a></li>
                <li><a href="{{ route('feed.index') }}" class="hover:text-primary hover:underline">{{ __('Akış') }}</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-primary hover:underline">{{ __('Blog') }}</a></li>
            </ul>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-neutral-500">{{ __('Üyelik') }}</p>
            <ul class="mt-4 space-y-2.5 text-[15px] font-semibold text-neutral-900">
                <li><a href="{{ route('login') }}" class="hover:text-primary hover:underline">{{ __('Vatandaş girişi') }}</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-primary hover:underline">{{ __('Üye ol') }}</a></li>
                <li><a href="{{ route('login.brand') }}" class="hover:text-primary hover:underline">{{ __('Belediye / kurum') }}</a></li>
            </ul>
        </div>
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-neutral-500">{{ __('İletişim ve yasal') }}</p>
            <ul class="mt-4 space-y-2.5 text-[15px]">
                <li><a href="{{ route('how-it-works') }}" class="font-medium text-neutral-800 underline-offset-4 hover:text-primary hover:underline">{{ __('Nasıl çalışır?') }}</a></li>
                <li><a href="{{ route('contact') }}" class="font-medium text-neutral-800 underline-offset-4 hover:text-primary hover:underline">{{ __('İletişim') }}</a></li>
                <li><a href="{{ route('legal.privacy') }}" class="font-medium text-neutral-800 underline-offset-4 hover:text-primary hover:underline">{{ __('Gizlilik') }}</a></li>
                <li><a href="{{ route('legal.kvkk') }}" class="font-medium text-neutral-800 underline-offset-4 hover:text-primary hover:underline">{{ __('KVKK') }}</a></li>
                <li><a href="{{ route('legal.terms') }}" class="font-medium text-neutral-800 underline-offset-4 hover:text-primary hover:underline">{{ __('Kullanım koşulları') }}</a></li>
            </ul>
            <p class="mt-6 text-[14px] leading-relaxed text-neutral-500">
                {{ __('Resmi başvuru yollarının yerini almaz; yasal haklarınızı etkileyebilecek hususlarda mevzuat ve ilgili kurumları yanınıza alınız.') }}
            </p>
        </div>
    </div>
    <p class="mx-auto mt-10 max-w-[1200px] border-t border-neutral-200/80 px-5 pt-6 text-center text-[13px] text-neutral-500">
        © {{ date('Y') }} {{ config('app.name') }}
    </p>
</footer>
