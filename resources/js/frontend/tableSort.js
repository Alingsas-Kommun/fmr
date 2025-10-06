export function tableSort() {
    return {
        data: [],
        renderedData: [],
        columns: [],
        sortBy: null,
        sortDirection: 'asc',
        sortable: true,
        sortedData: [],
        
        init() {
            this.sortedData = this.renderedData;
            if (this.sortBy) {
                this.sortByColumn(this.sortBy);
            }
        },
        
        sortByColumn(column) {
            if (!this.sortable) return;
            
            if (this.sortBy === column) {
                if (this.sortDirection === 'asc') {
                    this.sortDirection = 'desc';
                } else if (this.sortDirection === 'desc') {
                    // Reset to unsorted state
                    this.sortBy = null;
                    this.sortDirection = 'asc';
                }
            } else {
                this.sortDirection = 'asc';
                this.sortBy = column;
            }
            
            if (this.sortBy) {
                // Sort using original data, but return rendered data
                const sortedIndices = [...Array(this.data.length).keys()].sort((a, b) => {
                    const aVal = this.getNestedValue(this.data[a], column);
                    const bVal = this.getNestedValue(this.data[b], column);
                    
                    if (aVal < bVal) {
                        return this.sortDirection === 'asc' ? -1 : 1;
                    }
                    if (aVal > bVal) {
                        return this.sortDirection === 'asc' ? 1 : -1;
                    }
                    return 0;
                });
                
                this.sortedData = sortedIndices.map(index => this.renderedData[index]);
            } else {
                // Reset to original order when unsorted
                this.sortedData = [...this.renderedData];
            }
        },
        
        getNestedValue(obj, path) {
            return path.split('.').reduce((o, p) => o && o[p], obj);
        },
        
        renderColumn(item, column) {
            // The item is from renderedData, so it already contains the pre-rendered HTML
            return item[column.key] || '';
        }
    }
}
