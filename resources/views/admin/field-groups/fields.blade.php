@if(!empty($tabs))
    <div class="fields-tabs" 
        x-data="{ 
            storageKey: '{{ $id }}_activeTab',
            activeTab: sessionStorage.getItem('{{ $id }}_activeTab') || '{{ array_key_first($tabs) }}',
            setActiveTab(tabId) {
                this.activeTab = tabId;
                sessionStorage.setItem(this.storageKey, tabId);
            }
        }">

        <nav class="fields-tab-nav">
            @foreach($tabs as $tab_id => $tab)
                <button @click="setActiveTab('{{ $tab_id }}')" type="button" class="fields-tab-button" :class="{ 'active': activeTab === '{{ $tab_id }}' }" x-cloak>
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </nav>

        @foreach($tabs as $tab_id => $tab)
            <div class="fields-tab-content" x-show="activeTab === '{{ $tab_id }}'" x-cloak>
                @foreach($tab['groups'] as $group)
                    @includeIf('admin.field-groups.group', ['group' => $group])
                @endforeach
            </div>
        @endforeach
    </div>
@else
    @foreach($groups as $group)
        @includeIf('admin.field-groups.group', ['group' => $group])
    @endforeach
@endif