<div class="fields-group">
    <div class="fields-row">
        <div class="fields-field-row">
            <div class="fields-field-col" style="--cols: 6">
                <div class="fields-field">
                    <label for="board_id">{{ __('Board', 'fmr') }}</label>
                    <select name="board_id" id="board_id" class="widefat" required>
                        <option value="">{{ __('Select Board', 'fmr') }}</option>
                        
                        @foreach($boards as $board)
                            <option value="{{ $board->ID }}" {{ $getFieldValue('board_id') == $board->ID ? 'selected' : '' }}>
                                {{ $board->post_title }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="fields-field-col" style="--cols: 6">
                <div class="fields-field">
                    <label for="type">{{ __('Type', 'fmr') }}</label>
                    <select name="type" id="type" class="widefat" required>
                        <option value="">{{ __('Select Type', 'fmr') }}</option>
                        
                        @foreach(['Nämnd', 'Styrelse', 'Utskott', 'Beredning', 'Råd'] as $type)
                            <option value="{{ $type }}" {{ $getFieldValue('type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="fields-row">
        <div class="fields-field-row">
            <div class="fields-field-col" style="--cols: 6">
                <div class="fields-field">
                    <label for="start_date">{{ __('Start Date', 'fmr') }}</label>
                    <input 
                        type="date" 
                        name="start_date" 
                        id="start_date" 
                        value="{{ $getFieldValue('start_date') ? date('Y-m-d', strtotime($getFieldValue('start_date'))) : '' }}" 
                        class="widefat"
                        required
                    >
                </div>
            </div>
            <div class="fields-field-col" style="--cols: 6">
                <div class="fields-field">
                    <label for="end_date">{{ __('End Date', 'fmr') }}</label>
                    <input 
                        type="date" 
                        name="end_date" 
                        id="end_date" 
                        value="{{ $getFieldValue('end_date') ? date('Y-m-d', strtotime($getFieldValue('end_date'))) : '' }}" 
                        class="widefat"
                        required
                    >
                </div>
            </div>
        </div>
    </div>
</div>