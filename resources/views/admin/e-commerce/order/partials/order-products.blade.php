<div class="card">
    <div class="card-header">
        <h2 class="card-title">Order Products</h2>
    </div>
    <div class="card-body">
        @php
            $vendors = DB::table('multi_order')
                ->where('order_id', $order->id)
                ->get();
        @endphp
        @foreach ($vendors as $vendorKey => $vendor)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Product</th>
                            <th>Title</th>
                            <th>Variation</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->orderDetails as $itemKey => $item)
                            @if (isset($item->product->user_id) && $item->product->user_id == $vendor->vendor_id)
                                <tr>
                                    <td>{{ $itemKey + 1 }}</td>
                                    <td>
                                        <img src="{{ asset('uploads/product/' . ($item->product->image ?? 'default.png')) }}"
                                            alt="Product Image" width="80px" height="80px">
                                    </td>
                                    <td>
                                        <a href="{{ isset($item->product) ? route('admin.product.show', $item->product->id) : '#' }}"
                                            target="_blank">{{ $item->title }}</a>
                                        @if(!isset($item->product)) <span class="badge badge-danger">Deleted</span> @endif
                                    </td>
                                    <td>
                                        @php
                                            $variations = [];
                                            
                                            // Parse the size JSON data (correctly as associative array)
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
                                                
                                                // Add color badge with swatch
                                                $colorBadge = '<span class="badge badge-info" style="padding: 6px 10px; font-size: 13px;">';
                                                if ($colorCode) {
                                                    $colorBadge .= '<span class="color-swatch" style="display:inline-block;width:14px;height:14px;background-color:' . htmlspecialchars($colorCode) . ';border-radius:3px;margin-right:6px;border:1px solid rgba(0,0,0,0.2);vertical-align:middle;"></span>';
                                                }
                                                $colorBadge .= 'Color: ' . htmlspecialchars($colorName) . '</span>';
                                                $variations[] = $colorBadge;
                                            }
                                            
                                            // Handle Size/Attributes from JSON
                                            if ($sizeData && !empty($sizeData)) {
                                                // Type 1: Size with/without Color ({"size_id": 123})
                                                if (isset($sizeData['size_id'])) {
                                                    $sizeId = $sizeData['size_id'];
                                                    $sizeInfo = DB::table('sizes')->where('id', $sizeId)->first();
                                                    
                                                    if ($sizeInfo) {
                                                        $variations[] = '<span class="badge badge-primary" style="padding: 6px 10px; font-size: 13px;">Size: ' . htmlspecialchars($sizeInfo->name) . '</span>';
                                                    } else {
                                                        $variations[] = '<span class="badge badge-warning" style="padding: 6px 10px; font-size: 13px;">Size: N/A</span>';
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
                                                                $variations[] = '<span class="badge badge-secondary" style="padding: 6px 10px; font-size: 13px;">' . 
                                                                    htmlspecialchars($attrValue->attr_name) . ': ' . 
                                                                    htmlspecialchars($attrValue->value_name) . '</span>';
                                                            } else {
                                                                $variations[] = '<span class="badge badge-danger" style="padding: 6px 10px; font-size: 13px;">Unknown Attribute</span>';
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            
                                            // Display variations or "Simple Product" if none
                                            if (!empty($variations)) {
                                                echo '<div style="display: flex; flex-wrap: wrap; gap: 6px;">' . implode('', $variations) . '</div>';
                                            } else {
                                                echo '<span class="badge badge-success" style="padding: 6px 10px; font-size: 13px;">Simple Product</span>';
                                            }
                                        @endphp
                                    </td>
                                    <td>{{ $item->qty }}</td>
                                    <td>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }} {{ number_format($item->price, 2) }}</td>
                                    <td>{{ setting('CURRENCY_CODE_MIN') ?? 'TK' }} {{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</div>