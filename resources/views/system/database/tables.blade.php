@extends('layouts.system')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Database Tables</h1>
                <p class="mt-2 text-gray-600">Explore database tables, view structure, and manage data</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    <span class="font-medium">{{ count($tables) }}</span> tables found
                </div>
                <a href="{{ route('system.database.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Back to Database
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" id="tableSearch" placeholder="Search tables..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <select id="sizeFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All sizes</option>
                        <option value="small">Small (&lt; 1MB)</option>
                        <option value="medium">Medium (1-10MB)</option>
                        <option value="large">Large (&gt; 10MB)</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="refreshTables()" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="tablesGrid">
        @forelse($tables as $table)
            <div class="bg-white shadow rounded-lg table-card" data-table-name="{{ $table['name'] }}" data-table-size="{{ $table['size'] }}">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 truncate">{{ $table['name'] }}</h3>
                        </div>
                        <div class="flex items-center space-x-1">
                            @if($table['size'] > 10)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Large</span>
                            @elseif($table['size'] > 1)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Medium</span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Small</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Columns</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ count($table['columns']) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Rows</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ number_format($table['row_count']) }}</dd>
                        </div>
                    </div>

                    <div class="mb-4">
                        <dt class="text-sm font-medium text-gray-500">Size</dt>
                        <dd class="text-sm text-gray-900">{{ $table['size'] }} MB</dd>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            {{ count($table['columns']) }} columns
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="showTableStructure('{{ $table['name'] }}', {{ json_encode($table['columns']) }})" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Structure
                            </button>
                            <button onclick="showTableData('{{ $table['name'] }}')" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white shadow rounded-lg p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tables found</h3>
                    <p class="mt-1 text-sm text-gray-500">No database tables were found.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Table Structure Modal -->
<div id="structureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="structureModalTitle">Table Structure</h3>
                <button onclick="closeStructureModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="structureModalContent" class="max-h-96 overflow-y-auto">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Table Data Modal -->
<div id="dataModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-5/6 lg:w-4/5 xl:w-3/4 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="dataModalTitle">Table Data</h3>
                <button onclick="closeDataModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="dataModalContent" class="max-h-96 overflow-y-auto">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
class TableManager {
    constructor() {
        this.searchInput = document.getElementById('tableSearch');
        this.sizeFilter = document.getElementById('sizeFilter');
        this.tablesGrid = document.getElementById('tablesGrid');
        this.tableCards = document.querySelectorAll('.table-card');

        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Search functionality
        if (this.searchInput) {
            this.searchInput.addEventListener('input', () => this.filterTables());
        }

        // Size filter
        if (this.sizeFilter) {
            this.sizeFilter.addEventListener('change', () => this.filterTables());
        }
    }

    filterTables() {
        const searchTerm = this.searchInput.value.toLowerCase();
        const sizeFilter = this.sizeFilter.value;

        this.tableCards.forEach(card => {
            const tableName = card.dataset.tableName.toLowerCase();
            const tableSize = parseFloat(card.dataset.tableSize);

            let showCard = true;

            // Search filter
            if (searchTerm && !tableName.includes(searchTerm)) {
                showCard = false;
            }

            // Size filter
            if (sizeFilter) {
                switch (sizeFilter) {
                    case 'small':
                        if (tableSize >= 1) showCard = false;
                        break;
                    case 'medium':
                        if (tableSize < 1 || tableSize > 10) showCard = false;
                        break;
                    case 'large':
                        if (tableSize <= 10) showCard = false;
                        break;
                }
            }

            card.style.display = showCard ? 'block' : 'none';
        });
    }
}

function showTableStructure(tableName, columns) {
    const modal = document.getElementById('structureModal');
    const title = document.getElementById('structureModalTitle');
    const content = document.getElementById('structureModalContent');

    title.textContent = `Table Structure: ${tableName}`;

    // Show loading state
    content.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <span class="ml-2 text-gray-600">Loading table structure...</span>
        </div>
    `;

    modal.classList.remove('hidden');

    // Fetch detailed structure
    fetch(`/system/database/tables/${tableName}/structure`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                content.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">${data.error}</p>
                            </div>
                        </div>
                    </div>
                `;
                return;
            }

            let structureHtml = '<div class="space-y-4">';

            // Column details
            structureHtml += '<div>';
            structureHtml += '<h4 class="text-sm font-medium text-gray-900 mb-3">Columns</h4>';
            structureHtml += '<div class="space-y-2">';

            data.columns.forEach(column => {
                const isPrimary = column.Key === 'PRI';
                const isNullable = column.Null === 'YES';
                const hasDefault = column.Default !== null;

                structureHtml += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center space-x-2">
                                ${isPrimary ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">PK</span>' : ''}
                                ${!isNullable ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">NOT NULL</span>' : ''}
                                ${hasDefault ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">DEFAULT</span>' : ''}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">${column.Field}</span>
                                <span class="text-sm text-gray-500 ml-2">${column.Type}</span>
                                ${hasDefault ? `<span class="text-xs text-gray-400 ml-2">Default: ${column.Default}</span>` : ''}
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">${column.Extra || 'Column'}</span>
                    </div>
                `;
            });

            structureHtml += '</div></div>';

            // Indexes
            if (data.indexes && data.indexes.length > 0) {
                structureHtml += '<div>';
                structureHtml += '<h4 class="text-sm font-medium text-gray-900 mb-3">Indexes</h4>';
                structureHtml += '<div class="space-y-2">';

                const uniqueIndexes = [...new Set(data.indexes.map(idx => idx.Key_name))];
                uniqueIndexes.forEach(indexName => {
                    const indexColumns = data.indexes.filter(idx => idx.Key_name === indexName);
                    const isUnique = indexColumns[0].Non_unique === 0;

                    structureHtml += `
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border">
                            <div class="flex items-center space-x-2">
                                ${isUnique ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">UNIQUE</span>' : ''}
                                <span class="font-medium text-gray-900">${indexName}</span>
                                <span class="text-sm text-gray-500">(${indexColumns.map(col => col.Column_name).join(', ')})</span>
                            </div>
                            <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">Index</span>
                        </div>
                    `;
                });

                structureHtml += '</div></div>';
            }

            structureHtml += '</div>';
            content.innerHTML = structureHtml;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">Failed to load table structure: ${error.message}</p>
                        </div>
                    </div>
                </div>
            `;
        });
}

function showTableData(tableName) {
    const modal = document.getElementById('dataModal');
    const title = document.getElementById('dataModalTitle');

    title.textContent = `Table Data: ${tableName}`;
    modal.classList.remove('hidden');

    // Reset state
    currentTableName = tableName;
    currentPage = 1;
    currentPerPage = 10;
    currentSort = '';
    currentDirection = 'asc';

    // Load table data
    loadTableData(tableName);
}

function closeStructureModal() {
    document.getElementById('structureModal').classList.add('hidden');
}

function closeDataModal() {
    document.getElementById('dataModal').classList.add('hidden');
}

function refreshTables() {
    window.location.reload();
}

// Global variables for current table data state
let currentTableName = '';
let currentPage = 1;
let currentPerPage = 10;
let currentSort = '';
let currentDirection = 'asc';

function changePage(tableName, page) {
    currentTableName = tableName;
    currentPage = page;
    loadTableData(tableName, page, currentPerPage, currentSort, currentDirection);
}

function changePerPage(tableName) {
    const perPage = document.getElementById('perPageSelect').value;
    currentTableName = tableName;
    currentPerPage = parseInt(perPage);
    currentPage = 1;
    loadTableData(tableName, 1, currentPerPage, currentSort, currentDirection);
}

function sortTable(tableName, column) {
    currentTableName = tableName;
    if (currentSort === column) {
        currentDirection = currentDirection === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort = column;
        currentDirection = 'asc';
    }
    loadTableData(tableName, currentPage, currentPerPage, currentSort, currentDirection);
}

function loadTableData(tableName, page = 1, perPage = 10, sort = '', direction = 'asc') {
    const modal = document.getElementById('dataModal');
    const content = document.getElementById('dataModalContent');

    // Show loading state
    content.innerHTML = `
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <span class="ml-2 text-gray-600">Loading table data...</span>
        </div>
    `;

    // Build query parameters
    const params = new URLSearchParams({
        page: page,
        per_page: perPage
    });

    if (sort) {
        params.append('sort', sort);
        params.append('direction', direction);
    }

    // Fetch table data
    fetch(`/system/database/tables/${tableName}/data?${params}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                content.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">${data.error}</p>
                            </div>
                        </div>
                    </div>
                `;
                return;
            }

            let dataHtml = `
                <div class="mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="text-sm text-gray-500">
                                Showing ${data.pagination.from || 0} to ${data.pagination.to || 0} of ${data.pagination.total} entries
                            </div>
                            <div class="text-sm text-gray-500">
                                Total rows: ${data.total_rows}
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <select id="perPageSelect" class="border border-gray-300 rounded-md px-2 py-1 text-sm" onchange="changePerPage('${tableName}')">
                                <option value="10" ${data.pagination.per_page == 10 ? 'selected' : ''}>10 per page</option>
                                <option value="25" ${data.pagination.per_page == 25 ? 'selected' : ''}>25 per page</option>
                                <option value="50" ${data.pagination.per_page == 50 ? 'selected' : ''}>50 per page</option>
                                <option value="100" ${data.pagination.per_page == 100 ? 'selected' : ''}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;

            if (data.data.length === 0) {
                dataHtml += `
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No data found</h3>
                        <p class="mt-1 text-sm text-gray-500">This table contains no data.</p>
                    </div>
                `;
            } else {
                // Create table
                dataHtml += `
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    ${data.columns.map(column => {
                                        const isCurrentSort = currentSort === column;
                                        const sortIcon = isCurrentSort ?
                                            (currentDirection === 'asc' ? '↑' : '↓') :
                                            '↕';
                                        return `
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 ${isCurrentSort ? 'bg-gray-100' : ''}" onclick="sortTable('${tableName}', '${column}')">
                                                ${column} <span class="ml-1">${sortIcon}</span>
                                            </th>
                                        `;
                                    }).join('')}
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${data.data.map(row => `
                                    <tr class="hover:bg-gray-50">
                                        ${data.columns.map(column => `
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${row[column] !== null ? (typeof row[column] === 'string' && row[column].length > 50 ?
                                                    `<span title="${row[column]}">${row[column].substring(0, 50)}...</span>` :
                                                    row[column]) : '<span class="text-gray-400 italic">NULL</span>'}
                                            </td>
                                        `).join('')}
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;

                // Pagination
                if (data.pagination.last_page > 1) {
                    dataHtml += `
                        <div class="mt-4 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <button onclick="changePage('${tableName}', 1)" class="px-3 py-1 border border-gray-300 rounded-md text-sm ${data.pagination.current_page == 1 ? 'bg-gray-100' : 'hover:bg-gray-50'}" ${data.pagination.current_page == 1 ? 'disabled' : ''}>
                                    First
                                </button>
                                <button onclick="changePage('${tableName}', ${data.pagination.current_page - 1})" class="px-3 py-1 border border-gray-300 rounded-md text-sm ${data.pagination.current_page == 1 ? 'bg-gray-100' : 'hover:bg-gray-50'}" ${data.pagination.current_page == 1 ? 'disabled' : ''}>
                                    Previous
                                </button>
                                <span class="px-3 py-1 text-sm text-gray-700">
                                    Page ${data.pagination.current_page} of ${data.pagination.last_page}
                                </span>
                                <button onclick="changePage('${tableName}', ${data.pagination.current_page + 1})" class="px-3 py-1 border border-gray-300 rounded-md text-sm ${data.pagination.current_page == data.pagination.last_page ? 'bg-gray-100' : 'hover:bg-gray-50'}" ${data.pagination.current_page == data.pagination.last_page ? 'disabled' : ''}>
                                    Next
                                </button>
                                <button onclick="changePage('${tableName}', ${data.pagination.last_page})" class="px-3 py-1 border border-gray-300 rounded-md text-sm ${data.pagination.current_page == data.pagination.last_page ? 'bg-gray-100' : 'hover:bg-gray-50'}" ${data.pagination.current_page == data.pagination.last_page ? 'disabled' : ''}>
                                    Last
                                </button>
                            </div>
                        </div>
                    `;
                }
            }

            content.innerHTML = dataHtml;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">Failed to load table data: ${error.message}</p>
                        </div>
                    </div>
                </div>
            `;
        });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new TableManager();
});
</script>
@endsection
