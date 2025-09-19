<div class="fields-group">
    @if(isset($group['label']))
        <h4 class="fields-group-title">{{ $group['label'] }}</h4>
    @endif

    <div class="fields-group-form">
        @foreach($group['rows'] as $row)
            @if(($row['_type'] ?? '') === 'field')
                @includeIf('admin.field-groups.field', [
                    'field' => $row,
                    'value' => $row['value'] ?? ''
                ])
            @else
                <div class="fields-field-row">
                    @foreach($row['fields'] as $field)
                        <div class="fields-field-col" style="--cols: {{ $field['cols'] ?? 12 }}">
                            @includeIf('admin.field-groups.field', [
                                'field' => $field,
                                'value' => $field['value'] ?? ''
                            ])
                        </div>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
</div>