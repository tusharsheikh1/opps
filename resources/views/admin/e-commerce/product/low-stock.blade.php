@extends('layouts.admin.e-commerce.app')

@section('title', 'Low Stock Products')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Low Stock Products</h1>
                <p class="text-muted">Products with a stock quantity less than 6.</p>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Low Stock</li>
                </ol>
            </div>
        </div>
    </div></section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Low Stock Product List</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>SKU</th>
                                        <th>Categories</th>
                                        <th>Stock Quantity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $key => $product)
                                        <tr>
                                            <td>{{ $products->firstItem() + $key }}</td>
                                            <td>
                                                <img src="{{ asset('uploads/product/' . $product->image) }}" alt="{{ $product->title }}" width="60" height="60" style="object-fit: cover; border-radius: 5px;">
                                            </td>
                                            <td>{{ $product->title }}</td>
                                            <td>{{ $product->sku ?? 'N/A' }}</td>
                                            <td>
                                                @foreach($product->categories as $category)
                                                    <span class="badge badge-info">{{ $category->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                <span class="badge badge-danger">{{ $product->quantity }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No low stock products found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $products->links() }}
                    </div>
                </div>
                </div>
        </div>
    </div>
</section>
@endsection