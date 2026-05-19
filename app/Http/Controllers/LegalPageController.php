<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\LegalContent;
use Illuminate\View\View;

final class LegalPageController extends Controller
{
    public function privacy(): View
    {
        return $this->render(LegalContent::PRIVACY, __('Gizlilik'));
    }

    public function kvkk(): View
    {
        return $this->render(LegalContent::KVKK, __('KVKK'));
    }

    public function terms(): View
    {
        return $this->render(LegalContent::TERMS, __('Kullanım koşulları'));
    }

    private function render(string $page, string $label): View
    {
        return view('legal.show', [
            'pageLabel' => $label,
            'htmlContent' => LegalContent::html($page),
        ]);
    }
}
