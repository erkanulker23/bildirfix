<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Şikâyet formu medya limitleri
    |--------------------------------------------------------------------------
    */

    'max_images' => 5,
    'max_videos' => 2,
    'image_max_kb' => 6144,
    'video_max_kb' => 35840,
    /** Sunucuda ffprobe yoksa bu süre için yalnızca dosya boyutu kontrol edilir. */
    'video_max_seconds' => 90,

    'draft_media_path' => 'complaint_drafts',
];
