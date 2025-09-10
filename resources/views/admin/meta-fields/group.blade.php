<div class="meta-box-group">
    <h3 class="meta-box-group-title">{{ $group['label'] }}</h3>

    @foreach($group['rows'] as $row)
        <div class="meta-box-row">
            <div class="meta-box-field-row">
                @foreach($row['fields'] as $field)
                    <div class="meta-box-field-col" style="--cols: {{ $field['cols'] ?? 12 }}">
                        @includeIf('admin.meta-fields.field', [
                            'field' => $field,
                            'value' => $field['value'] ?? ''
                        ])
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>