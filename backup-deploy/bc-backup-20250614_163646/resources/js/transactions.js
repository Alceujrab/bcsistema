// Transactions Management JavaScript
class TransactionManager {
    constructor() {
        this.initEventListeners();
        this.initInlineEditing();
        this.initBulkActions();
        this.initFilters();
        this.initCategoryManagement();
        this.initTooltips();
        this.debounceTimer = null;
    }

    // Initialize all event listeners
    initEventListeners() {
        // Select all checkbox
        $(document).on('change', '#select-all', (e) => {
            $('.transaction-checkbox').prop('checked', e.target.checked);
            this.updateBulkActionsVisibility();
        });

        // Individual checkboxes
        $(document).on('change', '.transaction-checkbox', () => {
            this.updateBulkActionsVisibility();
            this.updateSelectAllState();
        });

        // Delete buttons
        $(document).on('click', '.delete-btn', (e) => {
            const id = $(e.target).closest('.delete-btn').data('id');
            this.deleteTransaction(id);
        });

        // Edit buttons
        $(document).on('click', '.edit-btn', (e) => {
            const id = $(e.target).closest('.edit-btn').data('id');
            this.enableInlineEdit(id);
        });

        // Filter changes
        $('.filter-input').on('input change', () => {
            this.applyFilters();
        });

        // Clear filters
        $('#clear-filters').on('click', () => {
            this.clearFilters();
        });

        // Export button
        $('#export-transactions').on('click', () => {
            this.exportTransactions();
        });

        // Quick category creation
        $('#quick-create-category').on('click', () => {
            this.showQuickCategoryModal();
        });
    }

    // Initialize inline editing functionality
    initInlineEditing() {
        $(document).on('click', '.editable', (e) => {
            const $cell = $(e.target).closest('.editable');
            if ($cell.hasClass('editing')) return;

            this.enableCellEdit($cell);
        });

        $(document).on('keydown', '.edit-input', (e) => {
            if (e.key === 'Enter') {
                this.saveCellEdit($(e.target));
            } else if (e.key === 'Escape') {
                this.cancelCellEdit($(e.target));
            }
        });

        $(document).on('blur', '.edit-input', (e) => {
            this.saveCellEdit($(e.target));
        });
    }

    // Enable inline editing for a specific cell
    enableCellEdit($cell) {
        const field = $cell.data('field');
        const type = $cell.data('type');
        const transactionId = $cell.closest('tr').data('id');
        const currentValue = this.getCurrentValue($cell, field, type);

        $cell.addClass('editing');
        const originalContent = $cell.html();
        $cell.data('original-content', originalContent);

        let input;
        if (type === 'select' && field === 'category_id') {
            input = this.createCategorySelect(currentValue);
        } else if (type === 'date') {
            input = `<input type="date" class="edit-input w-full px-2 py-1 border rounded" value="${this.formatDateForInput(currentValue)}">`;
        } else if (type === 'number') {
            const numValue = this.extractNumberValue(currentValue);
            input = `<input type="number" step="0.01" class="edit-input w-full px-2 py-1 border rounded" value="${numValue}">`;
        } else {
            input = `<input type="text" class="edit-input w-full px-2 py-1 border rounded" value="${this.escapeHtml(currentValue)}">`;
        }

        $cell.html(input);
        const $input = $cell.find('.edit-input');
        $input.focus();

        if (type === 'select') {
            $input.select2({
                dropdownParent: $cell,
                width: '100%'
            });
        }
    }

    // Get current value from cell
    getCurrentValue($cell, field, type) {
        if (field === 'category_id') {
            const $span = $cell.find('span');
            return $span.length ? $span.text().trim() : '';
        } else if (type === 'date') {
            return $cell.text().trim();
        } else if (type === 'number') {
            return this.extractNumberValue($cell.text());
        } else {
            return $cell.text().trim();
        }
    }

    // Create category select dropdown
    createCategorySelect(currentValue) {
        let options = '<option value="">Sem categoria</option>';
        
        // Get categories from global variable or make AJAX call
        if (window.categories) {
            window.categories.forEach(category => {
                const selected = category.name === currentValue ? 'selected' : '';
                options += `<option value="${category.id}" ${selected} data-color="${category.color || ''}">${category.name}</option>`;
            });
        }

        return `<select class="edit-input w-full px-2 py-1 border rounded">${options}</select>`;
    }

    // Save cell edit
    saveCellEdit($input) {
        const $cell = $input.closest('.editable');
        const field = $cell.data('field');
        const type = $cell.data('type');
        const transactionId = $cell.closest('tr').data('id');
        const newValue = $input.val();

        // Show loading state
        $cell.html('<div class="animate-pulse bg-gray-200 h-6 rounded"></div>');

        // Make AJAX request to update
        $.ajax({
            url: `/transactions/${transactionId}/quick-update`,
            method: 'PATCH',
            data: {
                field: field,
                value: newValue,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                this.handleSuccessfulEdit($cell, field, type, response.data);
                this.showToast('Transação atualizada com sucesso!', 'success');
            },
            error: (xhr) => {
                this.handleFailedEdit($cell);
                const message = xhr.responseJSON?.message || 'Erro ao atualizar transação';
                this.showToast(message, 'error');
            }
        });
    }

    // Handle successful edit
    handleSuccessfulEdit($cell, field, type, data) {
        $cell.removeClass('editing');
        
        if (field === 'category_id') {
            const category = data.category;
            if (category) {
                const color = category.color || '#e5e7eb';
                $cell.html(`
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                          style="background-color: ${color}20; color: ${color};">
                        ${category.name}
                    </span>
                `);
            } else {
                $cell.html(`
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Sem categoria
                    </span>
                `);
            }
        } else if (field === 'amount') {
            const amount = parseFloat(data.amount);
            const colorClass = amount >= 0 ? 'text-green-600' : 'text-red-600';
            const sign = amount >= 0 ? '+' : '';
            $cell.html(`
                <span class="${colorClass}">
                    ${sign}R$ ${Math.abs(amount).toLocaleString('pt-BR', {minimumFractionDigits: 2})}
                </span>
            `);
        } else if (field === 'date') {
            $cell.html(new Date(data.date).toLocaleDateString('pt-BR'));
        } else {
            $cell.html(this.escapeHtml(data[field]));
        }
    }

    // Handle failed edit
    handleFailedEdit($cell) {
        const originalContent = $cell.data('original-content');
        $cell.removeClass('editing').html(originalContent);
    }

    // Cancel cell edit
    cancelCellEdit($input) {
        const $cell = $input.closest('.editable');
        const originalContent = $cell.data('original-content');
        $cell.removeClass('editing').html(originalContent);
    }

    // Initialize bulk actions
    initBulkActions() {
        $('#bulk-delete').on('click', () => {
            this.bulkDelete();
        });

        $('#bulk-categorize').on('click', () => {
            this.showBulkCategorizeModal();
        });

        $('#bulk-status').on('click', () => {
            this.showBulkStatusModal();
        });
    }

    // Update bulk actions visibility
    updateBulkActionsVisibility() {
        const checkedCount = $('.transaction-checkbox:checked').length;
        const $bulkActions = $('#bulk-actions');
        
        if (checkedCount > 0) {
            $bulkActions.removeClass('hidden');
            $('#selected-count').text(checkedCount);
        } else {
            $bulkActions.addClass('hidden');
        }
    }

    // Update select all checkbox state
    updateSelectAllState() {
        const totalCheckboxes = $('.transaction-checkbox').length;
        const checkedCheckboxes = $('.transaction-checkbox:checked').length;
        const selectAllCheckbox = $('#select-all')[0];

        if (checkedCheckboxes === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedCheckboxes === totalCheckboxes) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    // Initialize filters
    initFilters() {
        // Date range picker
        if ($.fn.daterangepicker) {
            $('#date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Limpar',
                    format: 'DD/MM/YYYY'
                }
            });

            $('#date_range').on('apply.daterangepicker', (ev, picker) => {
                $(ev.target).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                this.applyFilters();
            });

            $('#date_range').on('cancel.daterangepicker', (ev, picker) => {
                $(ev.target).val('');
                this.applyFilters();
            });
        }

        // Amount range sliders
        if ($.fn.slider) {
            $("#amount_range").slider({
                range: true,
                min: -10000,
                max: 10000,
                values: [-10000, 10000],
                slide: (event, ui) => {
                    $("#amount_display").text(`R$ ${ui.values[0].toLocaleString('pt-BR')} - R$ ${ui.values[1].toLocaleString('pt-BR')}`);
                },
                stop: () => {
                    this.applyFilters();
                }
            });
        }
    }

    // Apply filters with debouncing
    applyFilters() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.performFilterSearch();
        }, 300);
    }

    // Perform actual filter search
    performFilterSearch() {
        const filters = this.getFilterValues();
        const $tableContainer = $('#transactions-table');
        
        // Show loading state
        $tableContainer.addClass('opacity-50 pointer-events-none');
        
        $.ajax({
            url: window.location.pathname,
            method: 'GET',
            data: { ...filters, ajax: true },
            success: (response) => {
                $tableContainer.html(response.html);
                this.updateResultsCount(response.total);
                this.updateBulkActionsVisibility();
            },
            error: () => {
                this.showToast('Erro ao aplicar filtros', 'error');
            },
            complete: () => {
                $tableContainer.removeClass('opacity-50 pointer-events-none');
            }
        });
    }

    // Get current filter values
    getFilterValues() {
        const filters = {};
        
        $('.filter-input').each(function() {
            const $input = $(this);
            const name = $input.attr('name');
            const value = $input.val();
            
            if (value && value.trim() !== '') {
                filters[name] = value;
            }
        });

        return filters;
    }

    // Clear all filters
    clearFilters() {
        $('.filter-input').val('');
        $('#amount_range').slider('values', [-10000, 10000]);
        $('#amount_display').text('R$ -10.000 - R$ 10.000');
        this.applyFilters();
    }

    // Delete transaction
    deleteTransaction(id) {
        if (!confirm('Tem certeza que deseja excluir esta transação?')) {
            return;
        }

        $.ajax({
            url: `/transactions/${id}`,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: () => {
                $(`.transaction-row[data-id="${id}"]`).fadeOut(() => {
                    $(this).remove();
                    this.updateBulkActionsVisibility();
                });
                this.showToast('Transação excluída com sucesso!', 'success');
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Erro ao excluir transação';
                this.showToast(message, 'error');
            }
        });
    }

    // Bulk delete
    bulkDelete() {
        const selectedIds = $('.transaction-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            this.showToast('Selecione pelo menos uma transação', 'warning');
            return;
        }

        if (!confirm(`Tem certeza que deseja excluir ${selectedIds.length} transação(ões)?`)) {
            return;
        }

        $.ajax({
            url: '/transactions/bulk-delete',
            method: 'DELETE',
            data: {
                ids: selectedIds,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: () => {
                selectedIds.forEach(id => {
                    $(`.transaction-row[data-id="${id}"]`).fadeOut(function() {
                        $(this).remove();
                    });
                });
                
                this.showToast(`${selectedIds.length} transação(ões) excluída(s) com sucesso!`, 'success');
                this.updateBulkActionsVisibility();
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Erro ao excluir transações';
                this.showToast(message, 'error');
            }
        });
    }

    // Show quick category creation modal
    showQuickCategoryModal() {
        const modal = `
            <div id="quick-category-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Criar Nova Categoria</h3>
                        <form id="quick-category-form">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                                <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cor</label>
                                <input type="color" name="color" class="w-full h-10 px-1 py-1 border border-gray-300 rounded-md" value="#3B82F6">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                            <div class="flex space-x-3">
                                <button type="submit" class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Criar
                                </button>
                                <button type="button" onclick="$('#quick-category-modal').remove()" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        $('body').append(modal);

        $('#quick-category-form').on('submit', (e) => {
            e.preventDefault();
            this.createQuickCategory();
        });
    }

    // Create quick category
    createQuickCategory() {
        const formData = new FormData($('#quick-category-form')[0]);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: '/categories',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                // Add to global categories if exists
                if (window.categories) {
                    window.categories.push(response.data);
                }

                // Update all category selects
                this.updateCategorySelects();

                $('#quick-category-modal').remove();
                this.showToast('Categoria criada com sucesso!', 'success');
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Erro ao criar categoria';
                this.showToast(message, 'error');
            }
        });
    }

    // Update category selects
    updateCategorySelects() {
        if (!window.categories) return;

        $('.category-select').each(function() {
            const $select = $(this);
            const currentValue = $select.val();
            
            $select.empty().append('<option value="">Selecione uma categoria</option>');
            
            window.categories.forEach(category => {
                const selected = category.id == currentValue ? 'selected' : '';
                $select.append(`<option value="${category.id}" ${selected}>${category.name}</option>`);
            });
        });
    }

    // Initialize category management
    initCategoryManagement() {
        // Auto-categorization
        $('#auto-categorize').on('click', () => {
            this.autoCategorizeTransactions();
        });
    }

    // Auto categorize transactions
    autoCategorizeTransactions() {
        const selectedIds = $('.transaction-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            this.showToast('Selecione pelo menos uma transação', 'warning');
            return;
        }

        $.ajax({
            url: '/transactions/auto-categorize',
            method: 'POST',
            data: {
                ids: selectedIds,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                this.performFilterSearch(); // Refresh table
                this.showToast(`${response.categorized} transação(ões) categorizadas automaticamente!`, 'success');
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Erro na categorização automática';
                this.showToast(message, 'error');
            }
        });
    }

    // Export transactions
    exportTransactions() {
        const filters = this.getFilterValues();
        const selectedIds = $('.transaction-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        const params = new URLSearchParams();
        
        // Add filters
        Object.keys(filters).forEach(key => {
            params.append(key, filters[key]);
        });

        // Add selected IDs if any
        if (selectedIds.length > 0) {
            selectedIds.forEach(id => params.append('ids[]', id));
        }

        window.open(`/transactions/export?${params.toString()}`, '_blank');
    }

    // Initialize tooltips
    initTooltips() {
        if ($.fn.tooltip) {
            $('[title]').tooltip({
                placement: 'top',
                trigger: 'hover'
            });
        }
    }

    // Utility functions
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    extractNumberValue(text) {
        return text.replace(/[^\d,-]/g, '').replace(',', '.');
    }

    formatDateForInput(dateString) {
        const parts = dateString.split('/');
        if (parts.length === 3) {
            return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
        }
        return dateString;
    }

    updateResultsCount(total) {
        $('#results-count').text(`${total} resultado(s) encontrado(s)`);
    }

    showToast(message, type = 'info') {
        const bgColor = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        }[type] || 'bg-blue-500';

        const toast = `
            <div class="toast fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full opacity-0">
                <div class="flex items-center">
                    <span>${message}</span>
                    <button class="ml-4 text-white hover:text-gray-200" onclick="$(this).closest('.toast').remove()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        const $toast = $(toast);
        $('body').append($toast);

        // Animate in
        setTimeout(() => {
            $toast.removeClass('translate-x-full opacity-0');
        }, 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            $toast.addClass('translate-x-full opacity-0');
            setTimeout(() => $toast.remove(), 300);
        }, 5000);
    }
}

// Initialize when document is ready
$(document).ready(() => {
    window.transactionManager = new TransactionManager();
});

// Global helper functions
window.showQuickCategoryModal = () => {
    if (window.transactionManager) {
        window.transactionManager.showQuickCategoryModal();
    }
};

window.refreshTransactionTable = () => {
    if (window.transactionManager) {
        window.transactionManager.performFilterSearch();
    }
};
