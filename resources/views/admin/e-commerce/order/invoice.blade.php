<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->invoice }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --glass-white: rgba(255, 255, 255, 0.9);
            --glass-light: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.3);
            --glass-shadow: rgba(0, 0, 0, 0.1);
            --primary-color: #6366f1;
            --primary-light: #818cf8;
            --accent-color: #a855f7;
            --text-dark: #1e293b;
            --text-medium: #475569;
            --text-light: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        body {
            /* Glassmorphic gradient background */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-attachment: fixed;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 14px;
            color: var(--text-dark);
            line-height: 1.6;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            padding: 2rem 1rem;
            min-height: 100vh;
            position: relative;
        }

        /* Animated background pattern */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(168, 85, 247, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(99, 102, 241, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            /* Glassmorphic effect */
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 
                0 8px 32px 0 rgba(31, 38, 135, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .invoice-wrapper {
            padding: 1in;
            position: relative;
            background: transparent;
        }

        /* Watermark */
        .invoice-wrapper::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 500px;
            background-image: url("{{asset('uploads/setting/'.setting('logo'))}}");
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.03;
            z-index: 0;
            pointer-events: none;
        }

        .invoice-content {
            position: relative;
            z-index: 1;
        }

        /* Glassmorphic Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 2rem;
            margin-bottom: 2rem;
            /* Glassmorphic effect */
            background: linear-gradient(135deg, 
                rgba(99, 102, 241, 0.1) 0%, 
                rgba(168, 85, 247, 0.1) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            margin: -1in -1in 2rem -1in;
            padding: 2rem 1in 2rem 1in;
            position: relative;
            overflow: hidden;
        }

        /* Header glass overlay */
        .invoice-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.1) 0%, 
                rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }

        .invoice-header > * {
            position: relative;
            z-index: 1;
        }

        .invoice-header .logo {
            display: flex;
            align-items: center;
        }

        .invoice-header .logo img {
            max-height: 80px;
            max-width: 200px;
            width: auto;
            height: auto;
            object-fit: contain;
            /* Glassmorphic container for logo */
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 
                0 4px 16px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(255, 255, 255, 0.2) inset;
        }

        .invoice-header .invoice-title {
            text-align: right;
        }

        .invoice-header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0;
            letter-spacing: -0.02em;
            text-transform: uppercase;
            /* Glassmorphic gradient text */
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .invoice-header .invoice-number {
            font-size: 1rem;
            font-weight: 600;
            margin-top: 0.5rem;
            letter-spacing: 0.05em;
            color: var(--text-medium);
        }
            text-transform: uppercase;
        }

        .invoice-header .invoice-number {
            font-size: 1rem;
            font-weight: 600;
            margin-top: 0.5rem;
            opacity: 0.9;
            letter-spacing: 0.05em;
        }

        /* Info Cards Grid */
        .invoice-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .info-card {
            /* Glassmorphic card */
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 
                0 4px 16px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .info-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, 
                rgba(99, 102, 241, 0.03) 0%, 
                rgba(168, 85, 247, 0.03) 100%);
            pointer-events: none;
        }

        .info-card > * {
            position: relative;
            z-index: 1;
        }

        .info-card:hover {
            transform: translateY(-4px);
            box-shadow: 
                0 8px 24px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.2) inset;
            background: rgba(255, 255, 255, 0.8);
        }

        .info-card h2 {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--primary-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-card h2::before {
            content: "";
            width: 4px;
            height: 16px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .info-card p {
            margin: 0;
            font-size: 0.875rem;
            color: var(--text-medium);
            line-height: 1.8;
        }

        .info-card .highlight {
            font-weight: 600;
            color: var(--text-dark);
            display: block;
            margin-bottom: 0.25rem;
        }

        .info-card .status-badge {
            display: inline-block;
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
        }

        .status-paid {
            background: rgba(16, 185, 129, 0.15);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            color: #065F46;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.15);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            color: #92400E;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        /* Modern Table */
        .invoice-items {
            margin-bottom: 2.5rem;
        }

        .invoice-items h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--bg-light);
        }

        .invoice-items table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .invoice-items thead {
            /* Glassmorphic table header */
            background: linear-gradient(135deg, 
                rgba(99, 102, 241, 0.15) 0%, 
                rgba(168, 85, 247, 0.15) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .invoice-items th {
            text-align: left;
            padding: 1rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
            color: var(--text-dark);
        }

        .invoice-items th:first-child {
            border-top-left-radius: 8px;
        }

        .invoice-items th:last-child {
            border-top-right-radius: 8px;
        }

        .invoice-items tbody tr {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            transition: all 0.2s ease;
        }

        .invoice-items tbody tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.4);
        }

        .invoice-items tbody tr:hover {
            background: rgba(99, 102, 241, 0.08);
            transform: scale(1.005);
        }

        .invoice-items td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: top;
        }

        .invoice-items tbody tr:last-child td {
            border-bottom: none;
        }

        .invoice-items tbody tr:last-child td:first-child {
            border-bottom-left-radius: 8px;
        }

        .invoice-items tbody tr:last-child td:last-child {
            border-bottom-right-radius: 8px;
        }

        .item-name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9375rem;
        }

        .item-details {
            font-size: 0.8125rem;
            color: var(--text-light);
            margin-top: 0.25rem;
            line-height: 1.5;
        }

        .color-swatch {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 3px;
            margin-right: 4px;
            border: 1px solid rgba(0, 0, 0, 0.2);
            vertical-align: middle;
        }

        .text-right { 
            text-align: right; 
            font-weight: 600;
        }

        /* Summary Section */
        .invoice-summary {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .summary-card {
            width: 100%;
            max-width: 400px;
            /* Glassmorphic summary card */
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 
                0 4px 16px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
        }

        .summary-table {
            width: 100%;
        }

        .summary-table td {
            padding: 0.75rem 0;
            font-size: 0.9375rem;
        }

        .summary-label {
            color: var(--text-medium);
            font-weight: 500;
        }

        .summary-value {
            text-align: right;
            font-weight: 600;
            color: var(--text-dark);
        }

        .summary-table tr.divider td {
            border-top: 2px solid var(--border-color);
            padding-top: 1rem;
        }

        .summary-table tr.total td {
            padding-top: 1rem;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .summary-table tr.total .summary-label {
            color: var(--text-dark);
        }

        .summary-table tr.total .summary-value {
            color: var(--primary-color);
        }

        .summary-table tr.amount-due {
            /* Glassmorphic alert style */
            background: linear-gradient(135deg, 
                rgba(239, 68, 68, 0.1) 0%, 
                rgba(239, 68, 68, 0.15) 100%);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            margin-top: 0.5rem;
        }

        .summary-table tr.amount-due td {
            padding: 1rem;
            border-radius: 8px;
        }

        .summary-table tr.amount-due .summary-value {
            color: var(--danger);
            font-size: 1.5rem;
        }

        /* Glassmorphic Footer */
        .invoice-footer {
            margin-top: 3rem;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-align: center;
            box-shadow: 
                0 4px 16px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
        }

        .invoice-footer .thank-you {
            font-size: 1.125rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .invoice-footer .contact-info {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-top: 1rem;
        }

        /* Print Styles - Optimized for Glassmorphic Design */
        @media print {
            @page {
                size: A4;
                margin: 1in;
            }

            body {
                background: white !important;
                padding: 0;
            }

            body::before {
                display: none;
            }

            .invoice-container {
                max-width: 100%;
                box-shadow: none;
                border-radius: 0;
                background: white;
                border: none;
            }

            .invoice-wrapper {
                padding: 0;
            }

            .invoice-header {
                margin: 0 0 2rem 0;
                padding: 1.5rem;
                background: linear-gradient(135deg, 
                    rgba(99, 102, 241, 0.1) 0%, 
                    rgba(168, 85, 247, 0.1) 100%) !important;
                border: 1px solid rgba(0, 0, 0, 0.1);
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .info-card,
            .summary-card,
            .invoice-footer {
                background: rgba(249, 250, 251, 0.5) !important;
                border: 1px solid rgba(0, 0, 0, 0.1);
            }

            .invoice-items thead {
                background: rgba(99, 102, 241, 0.1) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-wrapper::before {
                opacity: 0.02;
            }

            .info-card:hover,
            .invoice-items tbody tr:hover {
                transform: none;
                box-shadow: none;
            }

            /* Ensure colored elements print correctly */
            .status-badge,
            .color-swatch {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Simplify backgrounds for print */
            .invoice-items tbody tr {
                background: white !important;
            }

            .invoice-items tbody tr:nth-child(even) {
                background: rgba(249, 250, 251, 0.5) !important;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 0;
            }

            .invoice-container {
                border-radius: 0;
            }

            .invoice-wrapper {
                padding: 1rem;
            }

            .invoice-header {
                flex-direction: column;
                gap: 1.5rem;
                margin: -1rem -1rem 2rem -1rem;
                padding: 1.5rem 1rem;
            }

            .invoice-header .invoice-title {
                text-align: left;
            }

            .invoice-info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .summary-card {
                max-width: 100%;
            }

            .invoice-items table {
                font-size: 0.8125rem;
            }

            .invoice-items th,
            .invoice-items td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-wrapper">
            <div class="invoice-content">
                <!-- Header -->
                <header class="invoice-header">
                    <div class="logo">
                        @if(setting('logo') && file_exists(public_path('uploads/setting/'.setting('logo'))))
                            <img src="{{asset('uploads/setting/'.setting('logo'))}}" alt="{{setting('site_name')}}" onerror="this.style.display='none'">
                        @else
                            <div style="background: white; padding: 15px 25px; border-radius: 8px; font-weight: 700; font-size: 1.2rem; color: var(--primary-color); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                {{setting('site_name', 'Your Company')}}
                            </div>
                        @endif
                    </div>
                    <div class="invoice-title">
                        <h1>Invoice</h1>
                        <div class="invoice-number">#{{ $order->invoice }}</div>
                    </div>
                </header>

                <!-- Info Grid -->
                <section class="invoice-info-grid">
                    <div class="info-card">
                        <h2>Bill To</h2>
                        <div>
                            <span class="highlight">{{$order->first_name}} {{$order->last_name}}</span>
                            <p>
                                {{$order->address}}<br>
                                {{$order->thana}}, {{$order->town}}<br>
                                {{$order->district}}<br>
                                <strong>Phone:</strong> {{$order->phone}}<br>
                                @if($order->email)
                                <strong>Email:</strong> {{$order->email}}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="info-card">
                        <h2>From</h2>
                        <div>
                            <span class="highlight">{{setting('site_name', 'Your Company')}}</span>
                            <p>
                                {{setting('SITE_INFO_ADDRESS')}}<br>
                                <strong>Email:</strong> {{setting('SITE_INFO_SUPPORT_MAIL')}}<br>
                                <strong>Phone:</strong> {{setting('SITE_INFO_PHONE')}}
                            </p>
                        </div>
                    </div>

                    <div class="info-card">
                        <h2>Invoice Details</h2>
                        <div>
                            <p>
                                <strong>Date:</strong><br>
                                <span class="highlight">{{date('F d, Y', strtotime($order->created_at))}}</span>
                            </p>
                            <p style="margin-top: 0.75rem;">
                                <strong>Payment Method:</strong><br>
                                <span class="highlight">{{$order->payment_method}}</span>
                            </p>
                            <span class="status-badge {{ $order->pay_staus == null ? 'status-pending' : 'status-paid' }}">
                                {{ $order->pay_staus == null ? 'Payment Pending' : 'Paid' }}
                            </span>
                        </div>
                    </div>
                </section>

                <!-- Items Table -->
                <section class="invoice-items">
                    <h2>Order Items</h2>
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 50%;">Product</th>
                                <th class="text-right" style="width: 15%;">Quantity</th>
                                <th class="text-right" style="width: 17.5%;">Unit Price</th>
                                <th class="text-right" style="width: 17.5%;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderDetails as $key => $item)
                            <tr>
                                <td>
                                    <div class="item-name">{{ $item->title }}</div>
                                    <div class="item-details">
                                        @php
                                            $variations = [];
                                            
                                            // Parse the size JSON data
                                            $sizeData = json_decode($item->size, true);
                                            
                                            // Handle Color
                                            if ($item->color && $item->color != 'blank') {
                                                // Check if color is numeric (ID) or name
                                                if (is_numeric($item->color)) {
                                                    $colorData = DB::table('colors')->where('id', $item->color)->first();
                                                    $colorName = $colorData ? $colorData->name : $item->color;
                                                    $colorCode = $colorData->code ?? null;
                                                } else {
                                                    $colorName = $item->color;
                                                    $colorCode = null;
                                                }
                                                
                                                // Add color with optional swatch
                                                $colorDisplay = 'Color: ' . htmlspecialchars($colorName);
                                                if ($colorCode) {
                                                    $colorDisplay = '<span class="color-swatch" style="background-color:' . htmlspecialchars($colorCode) . ';"></span>' . $colorDisplay;
                                                }
                                                $variations[] = $colorDisplay;
                                            }
                                            
                                            // Handle Size/Attributes from JSON
                                            if ($sizeData && !empty($sizeData)) {
                                                // Type 1: Size with/without Color ({"size_id": 123})
                                                if (isset($sizeData['size_id'])) {
                                                    $sizeId = $sizeData['size_id'];
                                                    $sizeInfo = DB::table('sizes')->where('id', $sizeId)->first();
                                                    
                                                    if ($sizeInfo) {
                                                        $variations[] = 'Size: ' . htmlspecialchars($sizeInfo->name);
                                                    }
                                                }
                                                // Type 2: Attributes (array of {"attribute_value_id": 456})
                                                elseif (is_array($sizeData) && isset($sizeData[0]['attribute_value_id'])) {
                                                    foreach ($sizeData as $attrData) {
                                                        if (isset($attrData['attribute_value_id'])) {
                                                            $attrValueId = $attrData['attribute_value_id'];
                                                            $attrValue = DB::table('attribute_values')
                                                                ->join('attributes', 'attribute_values.attribute_id', '=', 'attributes.id')
                                                                ->where('attribute_values.id', $attrValueId)
                                                                ->select('attribute_values.name as value_name', 'attributes.name as attr_name')
                                                                ->first();
                                                            
                                                            if ($attrValue) {
                                                                $variations[] = htmlspecialchars($attrValue->attr_name) . ': ' . htmlspecialchars($attrValue->value_name);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            
                                            // Display variations or "Simple Product" if none
                                            if (!empty($variations)) {
                                                echo implode(' • ', $variations);
                                            } else {
                                                echo '<em style="color: var(--success); font-weight: 500;">Simple Product</em>';
                                            }
                                        @endphp
                                    </div>
                                </td>
                                <td class="text-right">{{ $item->qty }}</td>
                                <td class="text-right">{{ setting('CURRENCY_CODE_MIN') ?? '৳' }}{{ number_format($item->price, 2) }}</td>
                                <td class="text-right">{{ setting('CURRENCY_CODE_MIN') ?? '৳' }}{{ number_format($item->total_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>

                <!-- Summary -->
                <section class="invoice-summary">
                    @php
                        $partialPayment = App\Models\PartialPayment::where('order_id', $order->id)->where('status', 1)->sum('amount');
                        $amountDue = $order->total - $partialPayment;
                    @endphp
                    <div class="summary-card">
                        <table class="summary-table">
                            <tbody>
                                <tr>
                                    <td class="summary-label">Subtotal</td>
                                    <td class="summary-value">{{ setting('CURRENCY_CODE_MIN') ?? '৳' }}{{number_format($order->subtotal, 2)}}</td>
                                </tr>
                                <tr>
                                    <td class="summary-label">Shipping Charge</td>
                                    <td class="summary-value">+ {{ setting('CURRENCY_CODE_MIN') ?? '৳' }}{{number_format($order->shipping_charge, 2)}}</td>
                                </tr>
                                @if($order->discount > 0)
                                <tr>
                                    <td class="summary-label">Discount @if($order->coupon_code)({{$order->coupon_code}})@endif</td>
                                    <td class="summary-value" style="color: var(--danger);">- {{ setting('CURRENCY_CODE_MIN') ?? '৳' }}{{number_format($order->discount, 2)}}</td>
                                </tr>
                                @endif
                                <tr class="divider total">
                                    <td class="summary-label">Grand Total</td>
                                    <td class="summary-value">{{ setting('CURRENCY_CODE_MIN') ?? '৳' }}{{number_format($order->total, 2)}}</td>
                                </tr>
                                @if($partialPayment > 0)
                                <tr>
                                    <td class="summary-label">Paid Amount</td>
                                    <td class="summary-value" style="color: var(--success);">- {{ setting('CURRENCY_CODE_MIN') ?? '৳' }}{{number_format($partialPayment, 2)}}</td>
                                </tr>
                                <tr class="amount-due">
                                    <td class="summary-label" style="font-weight: 700; color: var(--danger);">Amount Due</td>
                                    <td class="summary-value">{{ setting('CURRENCY_CODE_MIN') ?? '৳' }}{{number_format($amountDue, 2)}}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Footer -->
                <footer class="invoice-footer">
                    <div class="thank-you">Thank you for your business!</div>
                    <div class="contact-info">
                        For any questions about this invoice, please contact us at {{setting('SITE_INFO_SUPPORT_MAIL')}} or {{setting('SITE_INFO_PHONE')}}
                    </div>
                </footer>
            </div>
        </div>
    </div>
    
    <script>
        window.addEventListener("load", function() {
            // Small delay to ensure styles are loaded before printing
            setTimeout(function() {
                window.print();
            }, 250);
        });
    </script>
</body>
</html>