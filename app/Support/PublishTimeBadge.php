<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonInterface;

final class PublishTimeBadge
{
    /** @var array{text: string, class: string, title: string} */
    public static function for(CarbonInterface $moment): array
    {
        $locale = config('app.locale', 'tr');
        $now = now();

        if ($moment->isSameDay($now)) {
            return [
                'text' => __('Bugün').' '.$moment->format('H:i'),
                'class' => 'bg-gradient-to-r from-teal-500 to-emerald-600 text-white shadow shadow-teal-500/35 ring-1 ring-white/30',
                'title' => $moment->toIso8601String(),
            ];
        }

        if ($moment->greaterThan($now->copy()->subHours(12))) {
            return [
                'text' => $moment->locale($locale)->diffForHumans(),
                'class' => 'border border-indigo-200 bg-indigo-50 text-indigo-950 shadow-sm ring-1 ring-indigo-100 font-semibold',
                'title' => $moment->toIso8601String(),
            ];
        }

        if ($moment->isYesterday()) {
            return [
                'text' => __('Dün').' '.$moment->format('H:i'),
                'class' => 'border border-sky-200 bg-sky-100 text-sky-950 shadow-sm font-semibold',
                'title' => $moment->toIso8601String(),
            ];
        }

        return [
            'text' => $moment->locale($locale)->translatedFormat('d MMMM YYYY, HH:mm'),
            'class' => 'border border-slate-200 bg-slate-100 text-slate-800 font-semibold',
            'title' => $moment->toIso8601String(),
        ];
    }
}
