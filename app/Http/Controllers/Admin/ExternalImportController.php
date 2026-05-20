<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\ExternalImportSourceType;
use App\Http\Controllers\Controller;
use App\Jobs\RunExternalImportSourceJob;
use App\Models\ExternalImportSource;
use App\Models\Institution;
use App\Services\ExternalImport\ExternalComplaintImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class ExternalImportController extends Controller
{
    public function index(): View
    {
        $sources = ExternalImportSource::query()
            ->with('institution:id,name')
            ->orderByDesc('updated_at')
            ->get();

        return view('admin.external-imports.index', [
            'sources' => $sources,
        ]);
    }

    public function create(): View
    {
        return view('admin.external-imports.form', [
            'source' => new ExternalImportSource([
                'enabled' => true,
                'auto_sync' => false,
                'max_pages' => 50,
                'fetch_media' => true,
                'default_moderation' => 'pending',
                'type' => ExternalImportSourceType::Sikayetvar,
            ]),
            'institutions' => $this->institutionOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $source = ExternalImportSource::query()->create($data);

        return redirect()
            ->route('admin.external-imports.index')
            ->with('status', __('Kaynak eklendi. “Şimdi çek” ile içe aktarabilirsiniz.'));
    }

    public function edit(ExternalImportSource $externalImport): View
    {
        return view('admin.external-imports.form', [
            'source' => $externalImport,
            'institutions' => $this->institutionOptions(),
        ]);
    }

    public function update(Request $request, ExternalImportSource $externalImport): RedirectResponse
    {
        $externalImport->update($this->validated($request));

        return redirect()
            ->route('admin.external-imports.index')
            ->with('status', __('Kaynak güncellendi.'));
    }

    public function destroy(ExternalImportSource $externalImport): RedirectResponse
    {
        $externalImport->delete();

        return redirect()
            ->route('admin.external-imports.index')
            ->with('status', __('Kaynak silindi. Daha önce içe aktarılan şikâyetler duruyor.'));
    }

    public function run(
        Request $request,
        ExternalImportSource $externalImport,
        ExternalComplaintImportService $service,
    ): RedirectResponse {
        if (! $externalImport->enabled) {
            return back()->withErrors(['run' => __('Kaynak kapalı. Önce etkinleştirin.')]);
        }

        if ($request->boolean('queue')) {
            RunExternalImportSourceJob::dispatch($externalImport->id);

            return back()->with('status', __('İçe aktarım kuyruğa alındı. Birkaç dakika içinde moderasyon / tüm şikâyetler listesinde görünür.'));
        }

        $result = $service->run($externalImport);

        $message = __(':imported yeni şikâyet içe aktarıldı, :skipped zaten vardı.', [
            'imported' => $result['imported'],
            'skipped' => $result['skipped'],
        ]);

        if ($result['errors'] !== []) {
            $message .= ' '.__(':count kayıt hata verdi.', ['count' => count($result['errors'])]);
        }

        return back()->with('status', $message);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::enum(ExternalImportSourceType::class)],
            'source_url' => ['required', 'url', 'max:500', 'regex:/^https?:\/\/(www\.)?sikayetvar\.com\//i'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'enabled' => ['sometimes', 'boolean'],
            'auto_sync' => ['sometimes', 'boolean'],
            'max_pages' => ['required', 'integer', 'min:1', 'max:200'],
            'fetch_media' => ['sometimes', 'boolean'],
            'default_moderation' => ['required', Rule::in(['pending', 'approved'])],
        ]);

        $type = ExternalImportSourceType::from($data['type']);
        if ($type === ExternalImportSourceType::SikayetvarInstitution && empty($data['institution_id'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'institution_id' => __('Kurum bağlantılı kaynak için bir kurum seçin.'),
            ]);
        }

        if ($type === ExternalImportSourceType::Sikayetvar) {
            $data['institution_id'] = null;
        }

        $data['enabled'] = $request->boolean('enabled');
        $data['auto_sync'] = $request->boolean('auto_sync');
        $data['fetch_media'] = $request->boolean('fetch_media');
        $data['source_slug'] = null;

        return $data;
    }

    /**
     * @return list<array{id: int, name: string}>
     */
    private function institutionOptions(): array
    {
        return Institution::query()
            ->orderBy('name')
            ->limit(500)
            ->get(['id', 'name'])
            ->map(fn (Institution $i) => ['id' => $i->id, 'name' => $i->name])
            ->all();
    }
}
