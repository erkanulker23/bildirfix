<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PostModerationStatus;
use App\Enums\UserRole;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use App\Support\SuperAdmin;
use Database\Seeders\Support\ProjectBlogArticles;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Proje blog içerikleri — yalnızca bu seed ile yüklenir.
 *
 * Üretim: php artisan db:seed --class=ProjectBlogSeeder --force
 */
class ProjectBlogSeeder extends Seeder
{
    public function run(): void
    {
        $author = $this->resolveAuthor();

        if ($author === null) {
            $this->command?->warn('Yazar kullanıcı bulunamadı (süper admin veya admin). Blog yazıları atlandı.');

            return;
        }

        $categories = $this->ensureCategories();
        $articles = ProjectBlogArticles::all();
        $created = 0;
        $updated = 0;

        foreach ($articles as $article) {
            $categoryId = $categories[$article['category']] ?? null;
            $publishedAt = now()->subDays(max(0, (int) $article['days_ago']))->startOfHour();

            $payload = [
                'author_user_id' => $author->id,
                'blog_category_id' => $categoryId,
                'title' => $article['title'],
                'excerpt' => Str::limit($article['excerpt'], 500, ''),
                'body' => $article['body'],
                'meta_title' => Str::limit($article['title'], 120, ''),
                'meta_description' => Str::limit($article['meta_description'], 520, ''),
                'is_published' => true,
                'published_at' => $publishedAt,
                'moderation_status' => PostModerationStatus::Approved,
                'moderated_at' => now(),
                'moderated_by_user_id' => $author->id,
                'moderation_note' => null,
            ];

            $post = BlogPost::query()->where('slug', $article['slug'])->first();

            if ($post === null) {
                BlogPost::query()->create([
                    'slug' => $article['slug'],
                    ...$payload,
                ]);
                $created++;
            } else {
                $post->fill($payload);
                $post->slug = $article['slug'];
                $post->save();
                $updated++;
            }
        }

        $this->command?->info(sprintf(
            'Blog: %d yazı işlendi (%d yeni, %d güncellendi). Yazar: %s',
            count($articles),
            $created,
            $updated,
            $author->email ?? $author->phone,
        ));
    }

    private function resolveAuthor(): ?User
    {
        $superEmail = SuperAdmin::email();

        if ($superEmail !== '') {
            $byEmail = User::query()->whereRaw('LOWER(email) = ?', [$superEmail])->first();
            if ($byEmail !== null) {
                return $byEmail;
            }
        }

        return User::query()
            ->whereIn('role', [UserRole::SuperAdmin, UserRole::Admin])
            ->orderBy('id')
            ->first();
    }

    /**
     * @return array<string, int>
     */
    private function ensureCategories(): array
    {
        $definitions = [
            'kent-yasami' => ['name' => 'Kent yaşamı', 'sort_order' => 10],
            'rehber' => ['name' => 'Rehber', 'sort_order' => 20],
            'duyurular' => ['name' => 'Duyurular', 'sort_order' => 30],
            'haberler' => ['name' => 'Haberler', 'sort_order' => 40],
            'sosyal-kampanyalar' => ['name' => 'Sosyal kampanyalar', 'sort_order' => 15],
            'saglik-dayanisma' => ['name' => 'Sağlık ve dayanışma', 'sort_order' => 12],
        ];

        $map = [];

        foreach ($definitions as $slug => $data) {
            $category = BlogCategory::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'sort_order' => $data['sort_order'],
                ],
            );

            $map[$slug] = (int) $category->id;
        }

        return $map;
    }
}
