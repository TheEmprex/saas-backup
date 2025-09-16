<?php

declare(strict_types=1);

namespace Wave\Traits;

use Illuminate\Support\Str;

trait HasDynamicFields
{
    /**
     * @return list<mixed>
     */
    private function dynamicFields($fields): array
    {
        $dynamicFields = [];

        foreach ($fields as $field) {
            $key = Str::slug($field['label']);

            $fieldType = class_exists($field['type']) ? $field['type'] : '\Filament\Forms\Components\\'.$field['type'];

            $newField = $fieldType::make($key);

            if (isset($field['label'])) {
                $newField->label($field['label']);
            }

            if (isset($field['options'])) {
                $newField->options($field['options']);
            }

            if (isset($field['suggestions'])) {
                $newField->suggestions($field['suggestions']);
            }

            if (isset($field['rules'])) {
                $rules = explode('|', $field['rules']);
                $newField->rules($rules);

                if (in_array('required', $rules)) {
                    $newField->required();
                }
            }

            $keyValue = auth()->user()->profileKeyValues->where('key', $key)->first();

            $value = $keyValue->value ?? '';

            if (! empty($value) && json_decode($value, true) !== null) {
                $value = json_decode($value, true);
            }

            $newField->default($value);
            // add validation

            $dynamicFields[] = $newField;
        }

        return $dynamicFields;
    }

    private function saveDynamicFields($fields): void
    {
        $state = $this->form->getState();

        foreach ($fields as $field) {
            $key = Str::slug($field['label']);

            if (isset($state[$key])) {
                $value = $state[$key];

                if (is_array($state[$key])) {
                    $value = json_encode($state[$key]);
                }

                auth()->user()->setProfileKeyValue($key, $value, $field['type']);
            }
        }
    }
}
