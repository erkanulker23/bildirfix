<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Post;
use App\Support\PageHero;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class InstitutionPublicController extends Controller
{
    public function __invoke(Institution $institution): View
    {
        $posts = Post::query()
            ->publicApproved()
            ->where(function ($q) use ($institution): void {
                $q->where('institution_id', $institution->id)
                    ->orWhereHas(
                        'institutions',
                        static fn ($sq) => $sq->where('institutions.id', $institution->id)
                    );
            })
            ->with([
                'user:id,name',
                'category:id,name,slug',
                'city:id,name',
                'district:id,name',
                'institution:id,name,verified',
            ])
            ->orderByDesc('created_at')
            ->paginate(perPage: 15)
            ->withQueryString();

        return view('institutions.show', [
            'institution' => $institution->load('city:id,name,slug'),
            'pageHero' => PageHero::make(
                __('Kurum profili'),
                $institution->name,
                null,
                __('Bu kuruma yönlendirilen onaylı bildirimler.'),
            ),
            'posts' => $posts,
            'seo' => [
                'description' => Str::limit(
                    __('Onaylı ve yayında olan şikâyet kayıtları: :kurum.', ['kurum' => $institution->name]),
                    320
                ),
                'canonical' => route('institutions.show', $institution, absolute: true),
                'og_title' => $institution->name.' • '.config('app.name'),
                'og_type' => 'website',
            ],
            'structuredData' => [],
        ]);
    }
}
