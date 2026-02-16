<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Domain\Setting\Models\Setting;
use App\Domain\Setting\Resources\SettingResource;
use App\Domain\Setting\Actions\UpdateSettingAction;
use App\Domain\Setting\DataTransferObjects\SettingData;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        protected UpdateSettingAction $updateSettingAction
    ) {
    }

    public function index(Request $request)
    {
        // Ensure only admin can access
        if (!$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return SettingResource::collection(Setting::all());
    }

    public function update(Request $request)
    {
        // Ensure only admin can access
        if (!$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|exists:settings,key',
            'settings.*.value' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $key = $request->input("settings.{$index}.key");

                    if (str_contains($key, 'limit')) {
                        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                            $fail("The {$key} value must be an integer.");
                        }
                    }
                }
            ],
        ]);

        $this->updateSettingAction->execute(SettingData::fromRequest($request));

        return response()->json(['message' => 'Settings updated successfully']);
    }
}
