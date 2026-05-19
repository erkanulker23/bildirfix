<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Campaign;
use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

final class ContentViewRecorder
{
    /**
     * @param  class-string<Post|Campaign>  $modelClass
     */
    public static function record(Model $model, string $sessionKeyPrefix): void
    {
        if (! $model instanceof Post && ! $model instanceof Campaign) {
            return;
        }

        $key = $sessionKeyPrefix.':'.$model->getKey();
        if (Session::has($key)) {
            return;
        }

        Session::put($key, true);

        $model->newQuery()
            ->whereKey($model->getKey())
            ->increment('view_count');
    }
}
