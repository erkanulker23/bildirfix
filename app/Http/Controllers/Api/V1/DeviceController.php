<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PushDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'device_token' => ['required', 'string', 'max:512'],
            'platform' => ['required', 'string', Rule::in(['ios', 'android', 'web'])],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        $device = PushDevice::query()->updateOrCreate(
            ['device_token' => $data['device_token']],
            [
                'user_id' => $request->user()->id,
                'platform' => $data['platform'],
                'device_name' => $data['device_name'] ?? null,
                'last_used_at' => now(),
            ],
        );

        return response()->json(['data' => $device], 201);
    }
}
