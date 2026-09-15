/**
 * Alpine multi-select for native-looking admin filters.
 *
 * @param {Object} config
 * @param {Array<{value: string, label: string}>} [config.options]
 * @param {Array<string|number>} [config.selected]
 * @param {string} [config.name]
 * @param {string} [config.placeholder]
 * @returns {Object}
 */
export function multiSelect({
    options = [],
    selected = [],
    name = '',
    placeholder = '',
} = {}) {
    return {
        open: false,
        options,
        selected: uniqueStrings(selected),
        name,
        placeholder,
        activeIndex: -1,

        get selectedLabels() {
            const selected = new Set(this.selected);

            return this.options
                .filter((option) => selected.has(String(option.value)))
                .map((option) => option.label);
        },

        get triggerText() {
            return this.selectedLabels.length
                ? this.selectedLabels.join(', ')
                : this.placeholder;
        },

        isSelected(value) {
            return this.selected.includes(String(value));
        },

        toggle(value) {
            const id = String(value);

            this.selected = this.isSelected(id)
                ? this.selected.filter((item) => item !== id)
                : [...this.selected, id];
        },

        toggleOpen() {
            this.open = !this.open;

            if (!this.open) {
                this.activeIndex = -1;
                return;
            }

            this.activeIndex = Math.max(
                this.options.findIndex((option) => this.isSelected(option.value)),
                0
            );

            this.$nextTick(() => this.scrollActiveIntoView());
        },

        close() {
            this.open = false;
            this.activeIndex = -1;
        },

        onFocusOut(event) {
            if (!this.$el.contains(event.relatedTarget)) {
                this.close();
            }
        },

        onKeydown(event) {
            if (event.key === 'Escape') {
                this.close();
                return;
            }

            if (!['Enter', ' ', 'ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) {
                return;
            }

            event.preventDefault();

            if (!this.open) {
                this.toggleOpen();
                return;
            }

            const lastIndex = this.options.length - 1;

            if (event.key === 'ArrowDown') {
                this.activeIndex = this.activeIndex >= lastIndex ? 0 : this.activeIndex + 1;
            } else if (event.key === 'ArrowUp') {
                this.activeIndex = this.activeIndex <= 0 ? lastIndex : this.activeIndex - 1;
            } else if (event.key === 'Home') {
                this.activeIndex = 0;
            } else if (event.key === 'End') {
                this.activeIndex = lastIndex;
            } else if (this.options[this.activeIndex]) {
                this.toggle(this.options[this.activeIndex].value);
            }

            this.$nextTick(() => this.scrollActiveIntoView());
        },

        scrollActiveIntoView() {
            this.$refs.panel
                ?.querySelector(`[data-index="${this.activeIndex}"]`)
                ?.scrollIntoView({ block: 'nearest' });
        },
    };
}

function uniqueStrings(values) {
    return [...new Set((values || []).map((value) => String(value)))];
}
