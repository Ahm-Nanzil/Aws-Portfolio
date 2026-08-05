@extends('layouts.admin')
@section('page-title')
    {{__('Inventory Report')}}
@endsection
@push('script-page')

@endpush
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">{{__('Dashboard')}}</a></li>
    <li class="breadcrumb-item">{{__('Report')}}</li>
@endsection
@section('action-btn')
    <div class="float-end">



    </div>
@endsection
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        line-height: 1.6;
    }
    .container {
        margin: 20px auto;
        max-width: 1200px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .header {
        text-align: center;
        margin-bottom: 30px;
    }
    .header h1 {
        margin: 0;
        font-size: 1.8rem;
    }
    .header p {
        margin: 5px 0;
        color: #555;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    table th, table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }
    table th {
        background-color: #f4f4f4;
        font-weight: bold;
    }
    .company-info {
        margin-bottom: 20px;
        font-size: 1rem;
        color: #333;
    }
</style>
@section('content')
    <div class="row">
        <div class="col-sm-12">

        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#stock-summary" role="tab">Stock Summary Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#inventory-analytics" role="tab">Inventory Analytics</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#valuation-report" role="tab">Inventory Valuation Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#warranty-report" role="tab">Warranty Period Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#stock-movement" role="tab">Stock Movement Report</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#stock-balance" role="tab">Stock Balance Report</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Stock Summary Report -->
                <div class="tab-pane fade show active" id="stock-summary" role="tabpanel">
                    <div class="card">
                        <div class="card-body table-border-style">
                            <!-- Your existing "Stock Summary Report" code here -->
                            <div class="container">
                                <div class="header">
                                    <h1>STOCK SUMMARY REPORT</h1>
                                    <p>From date: January 18, 2025</p>
                                    <p>To date: January 25, 2025</p>
                                </div>

                                <div class="company-info">
                                    <p><strong>Company name:</strong> {{ (isset(\Utility::settings()['company_name']) && !empty(\Utility::settings()['company_name'])) ? \Utility::settings()['company_name'] : env('APP_NAME') }}</p>
                                    <p><strong>Address:</strong></p>
                                </div>

                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>product Code</th>
                                            <th>product Name</th>
                                            <th>Unit Name</th>
                                            <th colspan="2">Opening Stock</th>
                                            <th colspan="2">Import In Period</th>
                                            <th colspan="2">Export In Period</th>
                                            <th colspan="2">Closing Stock</th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalOpeningQuantity = 0;
                                            $totalOpeningAmount = 0;
                                            $totalClosingQuantity = 0;
                                            $totalClosingAmount = 0;
                                        @endphp

                                        @foreach ($Products as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $product->sku }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->unit_id }}</td>
                                            <td>{{ $product->quantity }}</td>
                                            <td>{{ $product->quantity * $product->sale_price }}</td>
                                            <td>0</td>
                                            <td>0.00</td>
                                            <td>0</td>
                                            <td>0.00</td>
                                            <td>{{ $product->quantity }}</td>
                                            <td>{{ $product->quantity * $product->sale_price }}</td>

                                            @php
                                                $totalOpeningQuantity += $product->quantity;
                                                $totalOpeningAmount += $product->quantity * $product->sale_price;
                                                $totalClosingQuantity += $product->quantity;
                                                $totalClosingAmount += $product->quantity * $product->sale_price;
                                            @endphp
                                        </tr>
                                        @endforeach

                                        <tr>
                                            <td colspan="4"><strong>Total:</strong></td>
                                            <td><strong>{{ $totalOpeningQuantity }}</strong></td>
                                            <td><strong>{{ number_format($totalOpeningAmount, 2) }}</strong></td>
                                            <td><strong>0</strong></td>
                                            <td><strong>0.00</strong></td>
                                            <td><strong>0</strong></td>
                                            <td><strong>0.00</strong></td>
                                            <td><strong>{{ $totalClosingQuantity }}</strong></td>
                                            <td><strong>{{ number_format($totalClosingAmount, 2) }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Placeholder Tabs -->
                <div class="tab-pane fade" id="inventory-analytics" role="tabpanel">
                    <div class="card">
                        <div class="card-body">Content for Inventory Analytics</div>
                    </div>
                </div>
                <div class="tab-pane fade" id="valuation-report" role="tabpanel">
                    <div class="card">
                        <div class="card-body table-border-style">
                            <!-- Your existing "Stock Summary Report" code here -->
                            <div class="container">
                                <div class="header">
                                    <h1>INVENTORY VALUATION REPORT</h1>
                                    <p>From date: January 18, 2025</p>
                                    <p>To date: January 25, 2025</p>
                                </div>

                                <div class="company-info">
                                    <p><strong>Company name:</strong> {{ (isset(\Utility::settings()['company_name']) && !empty(\Utility::settings()['company_name'])) ? \Utility::settings()['company_name'] : env('APP_NAME') }}</p>

                                    <p><strong>Address:</strong></p>
                                </div>

                                <table>
                                    <thead>
                                        <tr>

                                            <th>Order</th>
                                            <th>product Code</th>
                                            <th>product Name</th>
                                            <th>Unit Name</th>
                                            <th>Inventory</th>
                                            <th>Sale price</th>
                                            <th>Purchase Price</th>
                                            <th>Amount Sold</th>
                                            <th>Amount Purchased</th>
                                            <th>Expected Profit</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        @php
                                            $totalOpeningQuantity = 0;
                                            $totalOpeningAmount = 0;
                                            $totalClosingQuantity = 0;
                                            $totalClosingAmount = 0;
                                        @endphp

                                        @foreach ($Products as $index => $product)
                                        <tr>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                            <td>0.00</td>
                                            <td>0.00</td>

                                            @php
                                                $totalOpeningQuantity += $product->quantity;
                                                $totalOpeningAmount += $product->quantity * $product->sale_price;
                                                $totalClosingQuantity += $product->quantity;
                                                $totalClosingAmount += $product->quantity * $product->sale_price;
                                            @endphp
                                        </tr>
                                        @endforeach

                                        <tr>
                                            <td colspan="4"><strong>Total:</strong></td>
                                            <td><strong>0.00</strong></td>
                                            <td><strong>0.00</strong></td>
                                            <td><strong>0</strong></td>
                                            <td><strong>0.00</strong></td>
                                            <td><strong>0</strong></td>
                                            <td><strong>0.00</strong></td>
                                            {{-- <td><strong>{{ $totalClosingQuantity }}</strong></td>
                                            <td><strong>{{ number_format($totalClosingAmount, 2) }}</strong></td> --}}
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="warranty-report" role="tabpanel">
                    <div class="card">
                        <div class="card-body">Content for Warranty Period Report</div>
                    </div>
                </div>
                <div class="tab-pane fade" id="stock-movement" role="tabpanel">
                    <div class="card">
                        <div class="card-body table-border-style">
                            <div class="container">
                                <div class="header">
                                    <h1>Stock Movement Summary (Batch & Serialized By Warehouse)</h1>
                                    <p>From date: January 18, 2025</p>
                                    <p>To date: January 25, 2025</p>
                                </div>

                                <div class="company-info">
                                    <p><strong>Company name:</strong> {{ (isset(\Utility::settings()['company_name']) && !empty(\Utility::settings()['company_name'])) ? \Utility::settings()['company_name'] : env('APP_NAME') }}</p>
                                    <p><strong>Address:</strong></p>
                                </div>

                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Item Code</th>
                                            <th>Description</th>
                                            <th>Item Type</th>
                                            <th>Group</th>
                                            <th>Sub Group</th>
                                            <th>UOM</th>
                                            <th>Batch No</th>
                                            <th>Serial#</th>
                                            <th>Expiry Date</th>
                                            <th>B/F</th>
                                            <th colspan="6">Purchase</th>
                                            <th colspan="7">Sales</th>
                                            <th colspan="7">Inventory</th>
                                            {{-- <th>Bal Qty</th> --}}
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th>GR</th>
                                            <th>PI</th>
                                            <th>CP</th>
                                            <th>PR</th>
                                            <th>GRT</th>
                                            <th>DO</th>
                                            <th>SI</th>
                                            <th>CS</th>
                                            <th>DRT</th>
                                            <th>SRT</th>
                                            <th>BR</th>
                                            <th>STF</th>
                                            <th>ADJ</th>
                                            <th>REC</th>
                                            <th>ISS</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Warehouse Group -->
                                        <tr style="background-color: #f0f0f0;">
                                            <td colspan="27"><strong>Warehouse: TESTWAREHOUSE</strong></td>
                                        </tr>
                                        <!-- Data Rows for Warehouse -->
                                        <tr>
                                            <td>1</td>
                                            <td>REF-123</td>
                                            <td>Sample Item A</td>
                                            <td>Hosting</td>
                                            <td>Group A</td>
                                            <td>Sub A</td>
                                            <td>Pieces</td>
                                            <td>LOT-001</td>
                                            <td>123456789</td>
                                            <td>2025-12-31</td>
                                            <td>50</td>
                                            <td>20</td>
                                            <td>10</td>
                                            <td>5</td>
                                            <td>5</td>
                                            <td>3</td>
                                            <td>2</td>
                                            <td>15</td>
                                            <td>5</td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>2</td>
                                            <td>1</td>
                                            <td>0</td>
                                            <td>1</td>
                                            <td>4</td>
                                            <td>60</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>555-001</td>
                                            <td>Sample Item B</td>
                                            <td>Maintenance</td>
                                            <td>Group B</td>
                                            <td>Sub B</td>
                                            <td>Kg</td>
                                            <td>LOT-002</td>
                                            <td>987654321</td>
                                            <td>2026-06-30</td>
                                            <td>100</td>
                                            <td>50</td>
                                            <td>30</td>
                                            <td>5</td>
                                            <td>10</td>
                                            <td>5</td>
                                            <td>3</td>
                                            <td>25</td>
                                            <td>10</td>
                                            <td>5</td>
                                            <td>3</td>
                                            <td>8</td>
                                            <td>2</td>
                                            <td>1</td>
                                            <td>2</td>
                                            <td>6</td>
                                            <td>120</td>
                                        </tr>
                                        <!-- Subtotals for Warehouse -->
                                        <tr style="background-color: #f9f9f9;">
                                            <td colspan="10"><strong>Subtotal for TESTWAREHOUSE:</strong></td>
                                            <td><strong>150</strong></td>
                                            <td><strong>70</strong></td>
                                            <td><strong>40</strong></td>
                                            <td><strong>10</strong></td>
                                            <td><strong>15</strong></td>
                                            <td><strong>8</strong></td>
                                            <td><strong>5</strong></td>
                                            <td><strong>40</strong></td>
                                            <td><strong>15</strong></td>
                                            <td><strong>5</strong></td>
                                            <td><strong>3</strong></td>
                                            <td><strong>10</strong></td>
                                            <td><strong>3</strong></td>
                                            <td><strong>1</strong></td>
                                            <td><strong>3</strong></td>
                                            <td><strong>10</strong></td>
                                            <td><strong>180</strong></td>
                                        </tr>
                                        <!-- Additional Warehouse (Static Example) -->
                                        <tr style="background-color: #f0f0f0;">
                                            <td colspan="27"><strong>Warehouse: SECONDWAREHOUSE</strong></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>789-002</td>
                                            <td>Sample Item C</td>
                                            <td>Maintenance</td>
                                            <td>Group C</td>
                                            <td>Sub C</td>
                                            <td>Meter</td>
                                            <td>LOT-003</td>
                                            <td>1122334455</td>
                                            <td>2027-03-15</td>
                                            <td>80</td>
                                            <td>30</td>
                                            <td>20</td>
                                            <td>10</td>
                                            <td>5</td>
                                            <td>8</td>
                                            <td>2</td>
                                            <td>20</td>
                                            <td>15</td>
                                            <td>5</td>
                                            <td>2</td>
                                            <td>10</td>
                                            <td>3</td>
                                            <td>0</td>
                                            <td>5</td>
                                            <td>7</td>
                                            <td>90</td>
                                        </tr>
                                        <!-- Grand Totals -->
                                        <tr style="background-color: #e0e0e0;">
                                            <td colspan="10"><strong>Grand Total:</strong></td>
                                            <td><strong>230</strong></td>
                                            <td><strong>100</strong></td>
                                            <td><strong>60</strong></td>
                                            <td><strong>20</strong></td>
                                            <td><strong>20</strong></td>
                                            <td><strong>16</strong></td>
                                            <td><strong>7</strong></td>
                                            <td><strong>60</strong></td>
                                            <td><strong>30</strong></td>
                                            <td><strong>10</strong></td>
                                            <td><strong>5</strong></td>
                                            <td><strong>20</strong></td>
                                            <td><strong>6</strong></td>
                                            <td><strong>2</strong></td>
                                            <td><strong>9</strong></td>
                                            <td><strong>23</strong></td>
                                            <td><strong>270</strong></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="stock-balance" role="tabpanel">
                    <div class="card">
                        <div class="card-body table-border-style">
                            <div class="container">
                                <div class="header">
                                    <h1>Stock Balance Detail (Batch & Serialized By Warehouse)</h1>
                                    <p>From date: January 18, 2025</p>
                                    <p>To date: January 25, 2025</p>
                                </div>

                                <div class="company-info">
                                    <p><strong>Company name:</strong> {{ (isset(\Utility::settings()['company_name']) && !empty(\Utility::settings()['company_name'])) ? \Utility::settings()['company_name'] : env('APP_NAME') }}</p>

                                    <p><strong>Address:</strong></p>
                                </div>

                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Item Code</th>
                                            <th>Description</th>
                                            <th>Item Type</th>
                                            <th>Group</th>
                                            <th>Sub Group</th>
                                            <th>Batch No</th>
                                            <th>Serial#</th>
                                            <th>Expiry Date</th>
                                            <th>UOM</th>
                                            <th>Bal Qty</th>
                                            <th>Unit Cost</th>
                                            <th>Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Warehouse Group -->
                                        <tr style="background-color: #f0f0f0;">
                                            <td colspan="13"><strong>Warehouse: TESTWAREHOUSE</strong></td>
                                        </tr>
                                        <!-- Data Rows for TESTWAREHOUSE -->
                                        <tr>
                                            <td>1</td>
                                            <td>ITEM-001</td>
                                            <td>Product A</td>
                                            <td>Hosting</td>
                                            <td>Group A</td>
                                            <td>Subgroup A</td>
                                            <td>LOT-001</td>
                                            <td>123456789</td>
                                            <td>2025-12-31</td>
                                            <td>Pieces</td>
                                            <td>50</td>
                                            <td>20</td>
                                            <td>1,000</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>ITEM-002</td>
                                            <td>Product B</td>
                                            <td>Maintenance</td>
                                            <td>Group B</td>
                                            <td>Subgroup B</td>
                                            <td>LOT-002</td>
                                            <td>987654321</td>
                                            <td>2026-06-30</td>
                                            <td>Kg</td>
                                            <td>100</td>
                                            <td>15</td>
                                            <td>1,500</td>
                                        </tr>
                                        <!-- Subtotals for TESTWAREHOUSE -->
                                        <tr style="background-color: #f9f9f9;">
                                            <td colspan="10"><strong>Subtotal for TESTWAREHOUSE:</strong></td>
                                            <td><strong>150</strong></td>
                                            <td><strong>---</strong></td>
                                            <td><strong>2,500</strong></td>
                                        </tr>
                                        <!-- Additional Warehouse -->
                                        <tr style="background-color: #f0f0f0;">
                                            <td colspan="13"><strong>Warehouse: SECONDWAREHOUSE</strong></td>
                                        </tr>
                                        <!-- Data Rows for SECONDWAREHOUSE -->
                                        <tr>
                                            <td>3</td>
                                            <td>ITEM-003</td>
                                            <td>Product C</td>
                                            <td>Hosting</td>
                                            <td>Group C</td>
                                            <td>Subgroup C</td>
                                            <td>LOT-003</td>
                                            <td>1122334455</td>
                                            <td>2027-03-15</td>
                                            <td>Meter</td>
                                            <td>80</td>
                                            <td>25</td>
                                            <td>2,000</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td>ITEM-004</td>
                                            <td>Product D</td>
                                            <td>Maintenance</td>
                                            <td>Group D</td>
                                            <td>Subgroup D</td>
                                            <td>LOT-004</td>
                                            <td>556677889</td>
                                            <td>2028-08-20</td>
                                            <td>Pieces</td>
                                            <td>60</td>
                                            <td>18</td>
                                            <td>1,080</td>
                                        </tr>
                                        <!-- Subtotals for SECONDWAREHOUSE -->
                                        <tr style="background-color: #f9f9f9;">
                                            <td colspan="10"><strong>Subtotal for SECONDWAREHOUSE:</strong></td>
                                            <td><strong>140</strong></td>
                                            <td><strong>---</strong></td>
                                            <td><strong>3,080</strong></td>
                                        </tr>
                                        <!-- Grand Totals -->
                                        <tr style="background-color: #e0e0e0;">
                                            <td colspan="10"><strong>Grand Total:</strong></td>
                                            <td><strong>290</strong></td>
                                            <td><strong>---</strong></td>
                                            <td><strong>5,580</strong></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

