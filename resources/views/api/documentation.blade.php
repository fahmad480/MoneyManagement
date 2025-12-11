@extends('layouts.app')

@section('title', 'API Documentation')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">API Documentation</h1>
                <p class="text-gray-600 mt-1">Panduan lengkap untuk menggunakan Money Controller API</p>
            </div>
            <a href="{{ route('api.management') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 transition-all transform hover:scale-105">
                <i class="fas fa-key"></i>
                <span class="font-medium">Manage API Keys</span>
            </a>
        </div>
    </div>

    <div class="space-y-8">
        <!-- Introduction -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-book text-blue-600"></i>
                Introduction
            </h2>
            <p class="text-gray-700 mb-4">Welcome to the Money Controller API documentation. This API allows you to programmatically access and manage your financial data including banks, cards, transactions, categories, and generate reports.</p>
            
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <p class="text-sm font-semibold text-blue-900 mb-1">Base URL</p>
                <code class="text-sm text-blue-800 bg-blue-100 px-2 py-1 rounded">{{ url('/api') }}</code>
            </div>
        </div>

        <!-- Authentication -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-lock text-green-600"></i>
                Authentication
            </h2>
            <p class="text-gray-700 mb-3">All API requests require authentication using an API key. Include your API key in the request header:</p>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto"><code>X-API-Key: your_api_key_here</code></pre>
            <p class="text-gray-600 mt-3 text-sm">You can generate API keys from the <a href="{{ route('api.management') }}" class="text-blue-600 hover:text-blue-800 font-medium">API Management</a> page.</p>
        </div>

        <!-- Response Format -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-code text-purple-600"></i>
                Response Format
            </h2>
            <p class="text-gray-700 mb-3">All responses are returned in JSON format with the following structure:</p>
            <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto"><code>{
  "success": true,
  "message": "Description of the result",
  "data": { ... },
  "meta": { ... } // For paginated responses
}</code></pre>
        </div>

        <!-- Banks API -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <i class="fas fa-university text-blue-600"></i>
                Banks API
            </h2>
            
            <!-- List Banks -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white">GET</span>
                        <code class="text-sm font-mono text-gray-800">/api/banks</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Retrieve a list of all banks</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Query Parameters:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1 ml-2">
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">per_page</code> (optional) - Number of results per page (default: 15)</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">is_active</code> (optional) - Filter by active status (true/false)</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">bank_type</code> (optional) - Filter by type (debit, credit, savings, current)</li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Example Response:</p>
                        <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{
  "success": true,
  "message": "Banks retrieved successfully",
  "data": [
    {
      "id": 1,
      "bank_name": "Bank BCA",
      "account_nickname": "Main Account",
      "account_number": "1234567890",
      "current_balance": "10000000.00",
      "bank_type": "savings",
      "is_active": true,
      "created_at": "2025-01-01 10:00:00"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}</code></pre>
                    </div>
                </div>
            </div>

            <!-- Create Bank -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-600 text-white">POST</span>
                        <code class="text-sm font-mono text-gray-800">/api/banks</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Create a new bank account</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Request Body:</p>
                        <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{
  "bank_name": "Bank BCA",
  "account_nickname": "Main Account",
  "account_number": "1234567890",
  "current_balance": 10000000,
  "bank_type": "savings",
  "branch": "Jakarta Pusat",
  "description": "Main savings account",
  "is_active": true
}</code></pre>
                    </div>
                </div>
            </div>

            <!-- Get Bank -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white">GET</span>
                        <code class="text-sm font-mono text-gray-800">/api/banks/{id}</code>
                    </div>
                </div>
                <div class="pl-4">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Retrieve a specific bank by ID</p>
                </div>
            </div>

            <!-- Update Bank -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border-l-4 border-yellow-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-600 text-white">PUT</span>
                        <code class="text-sm font-mono text-gray-800">/api/banks/{id}</code>
                    </div>
                </div>
                <div class="pl-4">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Update a bank account</p>
                    <p class="text-gray-600 text-sm mt-1"><strong>Request Body:</strong> Same as create, but all fields are optional</p>
                </div>
            </div>

            <!-- Delete Bank -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-600 text-white">DELETE</span>
                        <code class="text-sm font-mono text-gray-800">/api/banks/{id}</code>
                    </div>
                </div>
                <div class="pl-4">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Delete a bank account</p>
                </div>
            </div>
        </div>

        <!-- Cards API -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <i class="fas fa-credit-card text-cyan-600"></i>
                Cards API
            </h2>
            
            <div class="mb-6">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white">GET</span>
                        <code class="text-sm font-mono text-gray-800">/api/cards</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Retrieve a list of all cards</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Query Parameters:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1 ml-2">
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">per_page</code> (optional) - Number of results per page</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">is_active</code> (optional) - Filter by active status</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">card_type</code> (optional) - Filter by type (debit, credit)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-600 text-white">POST</span>
                        <code class="text-sm font-mono text-gray-800">/api/cards</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Create a new card</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Request Body:</p>
                        <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{
  "bank_id": 1,
  "card_name": "BCA Visa",
  "card_number": "4111111111111111",
  "transaction_limit": 5000000,
  "card_type": "credit",
  "card_form": "physical",
  "expiry_date": "2027-12-31",
  "description": "Main credit card",
  "is_active": true
}</code></pre>
                    </div>
                </div>
            </div>

            <p class="text-gray-500 text-sm italic">GET, PUT, DELETE endpoints follow the same pattern as Banks API</p>
        </div>

        <!-- Categories API -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <i class="fas fa-tags text-yellow-600"></i>
                Categories API
            </h2>
            
            <div class="mb-6">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white">GET</span>
                        <code class="text-sm font-mono text-gray-800">/api/categories</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Retrieve a list of all categories</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Query Parameters:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1 ml-2">
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">type</code> (optional) - Filter by type (income, expense)</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">is_active</code> (optional) - Filter by active status</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-600 text-white">POST</span>
                        <code class="text-sm font-mono text-gray-800">/api/categories</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Create a new category</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Request Body:</p>
                        <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{
  "name": "Food & Beverage",
  "icon": "fa-utensils",
  "color": "#ff6b6b",
  "type": "expense",
  "description": "Food and drink expenses",
  "is_active": true
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions API -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <i class="fas fa-exchange-alt text-green-600"></i>
                Transactions API
            </h2>
            
            <div class="mb-6">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white">GET</span>
                        <code class="text-sm font-mono text-gray-800">/api/transactions</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Retrieve a list of all transactions</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Query Parameters:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1 ml-2">
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">type</code> (optional) - Filter by type (income, expense, transfer)</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">bank_id</code> (optional) - Filter by bank</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">category_id</code> (optional) - Filter by category</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">start_date</code> (optional) - Filter from date (Y-m-d)</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">end_date</code> (optional) - Filter to date (Y-m-d)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-600 text-white">POST</span>
                        <code class="text-sm font-mono text-gray-800">/api/transactions</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Create a new transaction</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Request Body:</p>
                        <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{
  "bank_id": 1,
  "card_id": 1,
  "category_id": 1,
  "type": "expense",
  "amount": 150000,
  "payment_method": "credit",
  "source": "Restaurant ABC",
  "description": "Lunch with team",
  "notes": "Team building",
  "transaction_date": "2025-01-15 12:30:00"
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Charges API -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <i class="fas fa-receipt text-red-600"></i>
                Transaction Charges API
            </h2>
            
            <div class="mb-6">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white">GET</span>
                        <code class="text-sm font-mono text-gray-800">/api/transactions/{transactionId}/charges</code>
                    </div>
                </div>
                <div class="pl-4">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Get all charges for a transaction</p>
                </div>
            </div>

            <div class="mb-6">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-l-4 border-blue-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-600 text-white">POST</span>
                        <code class="text-sm font-mono text-gray-800">/api/transactions/{transactionId}/charges</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Add a charge to a transaction</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Request Body:</p>
                        <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{
  "charge_type": "admin_fee",
  "amount": 5000,
  "description": "Bank admin fee"
}</code></pre>
                    </div>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                        <p class="text-sm text-yellow-800"><strong>Charge Types:</strong> admin_fee, tax, service_charge, other</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports API -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                <i class="fas fa-chart-line text-indigo-600"></i>
                Reports API
            </h2>
            
            <!-- Summary Report -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white">GET</span>
                        <code class="text-sm font-mono text-gray-800">/api/reports/summary</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Get summary report of income, expense, and balance</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Query Parameters:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1 ml-2">
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">start_date</code> (optional) - Default: start of current month</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">end_date</code> (optional) - Default: end of current month</li>
                        </ul>
                    </div>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Example Response:</p>
                        <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{
  "success": true,
  "message": "Summary report retrieved successfully",
  "data": {
    "period": {
      "start_date": "2025-01-01",
      "end_date": "2025-01-31"
    },
    "total_income": "5000000.00",
    "total_expense": "3000000.00",
    "net_balance": "2000000.00",
    "total_balance": "10000000.00",
    "transaction_count": 25
  }
}</code></pre>
                    </div>
                </div>
            </div>

            <!-- By Category Report -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white">GET</span>
                        <code class="text-sm font-mono text-gray-800">/api/reports/by-category</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Get report grouped by category</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Query Parameters:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1 ml-2">
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">type</code> (optional) - income or expense (default: expense)</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">start_date</code> (optional)</li>
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">end_date</code> (optional)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Monthly Trend Report -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white">GET</span>
                        <code class="text-sm font-mono text-gray-800">/api/reports/monthly-trend</code>
                    </div>
                </div>
                <div class="pl-4 space-y-3">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Get monthly income/expense trend</p>
                    <div>
                        <p class="text-gray-900 font-semibold mb-2">Query Parameters:</p>
                        <ul class="list-disc list-inside text-gray-700 space-y-1 ml-2">
                            <li><code class="text-sm bg-gray-100 px-2 py-0.5 rounded">months</code> (optional) - Number of months to include (default: 6)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- By Bank Report -->
            <div class="mb-6">
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 p-4 rounded-lg mb-3">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white">GET</span>
                        <code class="text-sm font-mono text-gray-800">/api/reports/by-bank</code>
                    </div>
                </div>
                <div class="pl-4">
                    <p class="text-gray-700"><strong class="text-gray-900">Description:</strong> Get report of all banks with balances</p>
                </div>
            </div>
        </div>

        <!-- Error Responses -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
                Error Responses
            </h2>
            <p class="text-gray-700 mb-3">The API returns standard HTTP status codes:</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">200</span>
                    <span class="text-gray-700">Success</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">201</span>
                    <span class="text-gray-700">Created</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">401</span>
                    <span class="text-gray-700">Unauthorized (Invalid API Key)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">404</span>
                    <span class="text-gray-700">Not Found</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">422</span>
                    <span class="text-gray-700">Validation Error</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">500</span>
                    <span class="text-gray-700">Server Error</span>
                </div>
            </div>
            <div>
                <p class="text-gray-900 font-semibold mb-2">Error Response Format:</p>
                <pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto text-sm"><code>{
  "success": false,
  "message": "Validation error",
  "errors": {
    "bank_name": ["The bank name field is required."]
  }
}</code></pre>
            </div>
        </div>

        <!-- Rate Limiting -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-tachometer-alt text-yellow-600"></i>
                Rate Limiting
            </h2>
            <p class="text-gray-700">API requests are currently not rate limited, but please use the API responsibly. We may implement rate limiting in the future.</p>
        </div>

        <!-- Support -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-md p-6 border border-blue-200">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="fas fa-life-ring text-blue-600"></i>
                Support
            </h2>
            <p class="text-gray-700">If you have any questions or issues with the API, please contact support or create an issue in the project repository.</p>
            <div class="mt-4">
                <a href="{{ route('api.management') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition">
                    <i class="fas fa-arrow-left"></i>
                    Back to API Management
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
