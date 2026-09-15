<div
    class="multi-select"
    x-data="multiSelect(@js([
        'options' => $options,
        'selected' => $selected,
        'name' => $name,
        'placeholder' => $placeholder,
    ]))"
    @focusout="onFocusOut($event)"
    @keydown="onKeydown($event)"
    @click.outside="close()"
>
    <button
        type="button"
        id="{{ $id }}"
        class="multi-select__trigger"
        :class="{ 'is-open': open, 'is-placeholder': !selected.length }"
        :aria-expanded="open.toString()"
        aria-haspopup="listbox"
        aria-controls="{{ $id }}-list"
        @click="toggleOpen()"
        :title="triggerText"
    >
        <span class="multi-select__label" x-text="triggerText"></span>
    </button>

    <div
        x-cloak
        x-show="open"
        x-ref="panel"
        class="multi-select__panel"
    >
        <ul
            id="{{ $id }}-list"
            class="multi-select__list"
            role="listbox"
            aria-multiselectable="true"
            :aria-activedescendant="activeIndex >= 0 ? '{{ $id }}-option-' + activeIndex : ''"
        >
            <template x-for="(option, index) in options" :key="option.value">
                <li
                    :id="'{{ $id }}-option-' + index"
                    class="multi-select__option"
                    :class="{ 'is-active': activeIndex === index, 'is-selected': isSelected(option.value) }"
                    role="option"
                    :aria-selected="isSelected(option.value).toString()"
                    :data-index="index"
                    @mousedown.prevent="toggle(option.value); activeIndex = index"
                    @mouseenter="activeIndex = index"
                >
                    <input
                        type="checkbox"
                        tabindex="-1"
                        :checked="isSelected(option.value)"
                    >
                    <span x-text="option.label"></span>
                </li>
            </template>
        </ul>
    </div>

    <template x-for="value in selected" :key="value">
        <input type="hidden" :name="name" :value="value">
    </template>
</div>
