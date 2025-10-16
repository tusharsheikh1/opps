<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->invoice }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3B82F6;
            --text-color-dark: #1F2937;
            --text-color-light: #6B7280;
            --border-color: #E5E7EB;
            --background-color: #F9FAFB;
        }

        body {
            background-color: var(--background-color);
            font-family: 'Be Vietnam Pro', sans-serif;
            font-size: 14px;
            color: var(--text-color-dark);
            margin: 0;
            -webkit-print-color-adjust: exact;
        }

        .invoice-wrapper {
            max-width: 800px;
            margin: 3rem auto;
            padding: 3rem;
            background-color: #FFFFFF;
            border-radius: 0.25rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            position: relative;
            z-index: 1;
        }

        .invoice-wrapper::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url("{{asset('uploads/setting/'.setting('logo'))}}");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 400px;
            opacity: 0.05;
            z-index: -1;
            pointer-events: none;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
        }
        .invoice-header .logo img {
            max-height: 45px;
            width: auto;
        }
        .invoice-header .invoice-title {
            text-align: right;
        }
        .invoice-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-color-dark);
            margin: 0;
            letter-spacing: -0.05em;
        }
        .invoice-header p {
            font-size: 0.875rem;
            color: var(--text-color-light);
            margin: 0.25rem 0 0;
        }

        .invoice-parties {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
        }
        .invoice-parties h2 {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: var(--text-color-light);
            margin: 0 0 0.75rem 0;
        }
        .invoice-parties p {
            margin: 0;
            line-height: 1.6;
            font-size: 0.875rem;
        }
        .invoice-parties .value {
            font-weight: 500;
            color: var(--text-color-dark);
        }

        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-items th {
            text-align: left;
            padding: 0.75rem 0.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            color: var(--text-color-light);
            border-bottom: 2px solid var(--border-color);
        }
        .invoice-items td {
            padding: 1rem 0.5rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: top;
        }
        .invoice-items .item-name {
            font-weight: 700;
        }
        .invoice-items .item-details {
            font-size: 0.8rem;
            color: var(--text-color-light);
        }
        .invoice-items .text-right { text-align: right; }

        .invoice-summary {
            margin-top: 2rem;
            display: flex;
            justify-content: flex-end;
        }
        .invoice-summary .summary-table {
            width: 50%;
            max-width: 300px;
        }
        .invoice-summary .summary-table td {
            padding: 0.5rem;
        }
        .invoice-summary .summary-label {
            color: var(--text-color-light);
        }
        .invoice-summary .summary-value {
            text-align: right;
            font-weight: 500;
        }
        .invoice-summary .summary-total {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .invoice-summary .summary-total-label {
            font-weight: 700;
        }
        .invoice-summary tr.total-due .summary-total {
            color: #D32F2F;
        }

        .invoice-footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
            text-align: center;
            font-size: 0.875rem;
            color: var(--text-color-light);
        }

        /* --- Responsive Screen --- */
        @media (max-width: 768px) {
            .invoice-wrapper {
                margin: 0;
                padding: 1.5rem;
                border-radius: 0;
                box-shadow: none;
            }
            .invoice-header, .invoice-parties {
                flex-direction: column;
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            .invoice-header .invoice-title {
                text-align: left;
                margin-top: 1.5rem;
            }
            .invoice-summary {
                justify-content: center;
            }
            .invoice-summary .summary-table {
                width: 100%;
                max-width: none;
            }
        }
        
        /* --- UPDATED PRINT STYLES --- */
        @media print {
            /* Use @page to control print page margins */
            @page {
                size: A4;
                margin: 1.5cm;
            }

            body {
                background-color: #FFFFFF;
                font-size: 12px; /* Reduce font size for print */
            }

            .invoice-wrapper {
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }
            
            .invoice-wrapper::before {
                opacity: 0.03; /* Make watermark even fainter for print */
                background-size: 350px;
            }

            /* Reduce spacing between sections for print */
            .invoice-header, .invoice-parties, .invoice-footer {
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
            }
            .invoice-summary {
                margin-top: 1.5rem;
            }
            .invoice-footer {
                padding-top: 1rem;
                margin-top: 1.5rem;
            }
            .invoice-parties {
                gap: 1.5rem;
            }

            /* Reduce table cell padding for print */
            .invoice-items td, .invoice-items th {
                padding: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <header class="invoice-header">
            <div class="logo">
                <img src="{{asset('uploads/setting/'.setting('logo'))}}" alt="Company Logo">
            </div>
            <div class="invoice-title">
                <h1>Invoice</h1>
                <p>#{{ $order->invoice }}</p>
            </div>
        </header>

        <section class="invoice-parties">
            <div>
                <h2>Billed To</h2>
                <p>
                    <span class="value">{{$order->first_name}}</span><br>
                    {{$order->address}}, {{$order->thana}}<br>
                    {{$order->town}}, {{$order->district}}<br>
                    {{$order->email}} | {{$order->phone}}
                </p>
            </div>
            <div>
                <h2>From</h2>
                 <p>
                    <span class="value">{{setting('site_name', 'Your Company')}}</span><br>
                    {{setting('SITE_INFO_ADDRESS')}}<br>
                    {{setting('SITE_INFO_SUPPORT_MAIL')}}<br>
                    {{setting('SITE_INFO_PHONE')}}
                </p>
            </div>
            <div>
                <h2>Details</h2>
                <p>
                    <strong>Date:</strong> <span class="value">{{date('F d, Y', strtotime($order->created_at))}}</span><br>
                    <strong>Payment:</strong> <span class="value">{{$order->payment_method}}</span><br>
                    <strong>Status:</strong> 
                    <span class="value" style="color: {{ $order->pay_staus == null ? '#F59E0B' : '#10B981' }}; font-weight: 700;">
                        {{ $order->pay_staus == null ? 'Pending' : 'Paid' }}
                    </span>
                </p>
            </div>
        </section>

        <section class="invoice-items">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-right">Qty</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->orderDetails as $key => $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->title }}</div>
                            <div class="item-details" style="text-transform: capitalize;">
                                @php
                                    $attributes = json_decode($item->size);
                                @endphp
                                @if($attributes && $attributes != '""' && $attributes != '[]' && $attributes != '"\"\""')
                                    @foreach($attributes as $attrKey => $attrValue)
                                        @php
                                            $value = DB::table('attribute_values')->where('id', $attrValue)->first();
                                            $name = DB::table('attributes')->where('slug', $attrKey)->first();
                                        @endphp
                                        {{ $name->name ?? '' }}: {{ $value->name ?? '' }} 
                                    @endforeach
                                @endif
                                @if($item->color != 'blank')
                                    &nbsp;• Color: {{ $item->color }}
                                @endif
                            </div>
                        </td>
                        <td class="text-right">{{ $item->qty }}</td>
                        <td class="text-right">{{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <section class="invoice-summary">
             @php
                $partialPayment = App\Models\PartialPayment::where('order_id', $order->id)->where('status', 1)->sum('amount');
                $amountDue = $order->total - $partialPayment;
            @endphp
            <table class="summary-table">
                <tbody>
                    <tr>
                        <td class="summary-label">Subtotal</td>
                        <td class="summary-value">{{number_format($order->subtotal, 2)}}</td>
                    </tr>
                     <tr>
                        <td class="summary-label">Shipping</td>
                        <td class="summary-value">+ {{number_format($order->shipping_charge, 2)}}</td>
                    </tr>
                    @if($order->discount > 0)
                    <tr>
                        <td class="summary-label">Discount ({{$order->coupon_code}})</td>
                        <td class="summary-value">- {{number_format($order->discount, 2)}}</td>
                    </tr>
                    @endif
                     <tr style="border-top: 1px solid var(--border-color);">
                        <td class="summary-label summary-total-label">Grand Total</td>
                        <td class="summary-value summary-total">{{number_format($order->total, 2)}}</td>
                    </tr>
                     @if($partialPayment > 0)
                    <tr>
                        <td class="summary-label">Paid</td>
                        <td class="summary-value">- {{number_format($partialPayment, 2)}}</td>
                    </tr>
                    <tr class="total-due">
                        <td class="summary-label summary-total-label">Amount Due</td>
                        <td class="summary-value summary-total">{{number_format($amountDue, 2)}}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </section>

        <footer class="invoice-footer">
            <p>Thank you for your order!</p>
        </footer>
    </div>
    
    <script>
        window.addEventListener("load", function() {
            window.print();
        });
    </script>
</body>
</html>