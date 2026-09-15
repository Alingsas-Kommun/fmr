<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class MultiSelect extends Component
{
    public array $options;
    public array $selected;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        array $options = [],
        array $selected = [],
        public string $placeholder = '',
        public string $id = '',
    ) {
        $this->id = $id !== '' ? $id : trim($name, '[]');
        $this->options = $this->normalizeOptions($options);
        $this->selected = array_values(array_map('strval', $selected));
    }

    /**
     * Normalize options to [{value, label}, ...].
     *
     * @param array $options
     * @return array
     */
    private function normalizeOptions(array $options): array
    {
        $normalized = [];

        foreach ($options as $value => $label) {
            if (is_array($label) && array_key_exists('value', $label)) {
                $normalized[] = [
                    'value' => (string) $label['value'],
                    'label' => (string) ($label['label'] ?? $label['value']),
                ];
                continue;
            }

            $normalized[] = [
                'value' => (string) $value,
                'label' => (string) $label,
            ];
        }

        return $normalized;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('admin.components.multi-select');
    }
}
