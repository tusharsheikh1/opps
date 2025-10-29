@extends('layouts.admin.e-commerce.app')

@section('title', 'Settings')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6 offse">
                <h1>Setting - <small>Shop</small></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{routeHelper('dashboard')}}">Home</a></li>
                    <li class="breadcrumb-item active">My Profile</li>
                </ol>
            </div>
        </div>
    </div></section>

<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Application Layout</h3>
        </div>
        <div class="card-body">
            <div class="row">

                <div class="col-sm-8 offset-md-2">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title">Setting - Shop</h3>
                        </div>
                        <form id="shopSettingsForm" action="{{routeHelper('setting')}}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <input type="hidden" name="type" value="10">
                                {{-- <div class="form-group col-md-12"> --}}
                                    <ul class="form-row">
                                        <li class="bg-light p-2 mb-1 col-12"><b>Checkout</b></li>
                                        <li class="col-12 col-md-6 form-group">
                                            <label for="GUEST_CHECKOUT" class="text-capitalize">Gust Checkout</label>
                                            <select class="form-control" name="GUEST_CHECKOUT" id="GUEST_CHECKOUT">
                                                <option class="bg-info" value="{{ $GUEST_CHECKOUT->value }}">{{ ($GUEST_CHECKOUT->value == 1 ? "On"  : "Off" ) }}</option>
                                                <option value="1">On</option>
                                                <option value="0">Off</option>
                                            </select>
                                        </li>

                                        <li class="col-12 col-md-6 form-group">
                                            <label for="CHECKOUT_TYPE" class="text-capitalize">Checkout Type</label>
                                            <select class="form-control" name="CHECKOUT_TYPE" id="CHECKOUT_TYPE">
                                                <option class="bg-info" value="{{ setting('CHECKOUT_TYPE') ?? 0 }}">{{ (setting('CHECKOUT_TYPE')== 1 ? "Complex"  : "Minimal" ) }}</option>
                                                <option value="1">Complex</option>
                                                <option value="0">Minimal</option>
                                            </select>
                                        </li>

                                        <li class="col-12 col-md-6 form-group">
                                            <label for="phone_min_dgt" class="text-capitalize">Phone Number Minimum Digit</label>
                                            <input class="form-control" type="number" id="phone_min_dgt" name="phone_min_dgt" value="{{ setting('phone_min_dgt') ?? 11 }}">
                                        </li>

                                        <li class="col-12 col-md-6 form-group">
                                            <label for="phone_max_dgt" class="text-capitalize">Phone Number Maximum Digit</label>
                                            <input class="form-control" type="number" id="phone_max_dgt" name="phone_max_dgt" value="{{ setting('phone_max_dgt') ?? 11 }}">
                                        </li>
                                    </ul>



                                    <ul class="form-row">
                                        <li class="bg-light p-2 mb-1 col-12"><b>Shipping</b></li>
                                        <li class="col-12 col-md-6 form-group">
                                            <label for="COUNTRY_SERVE" class="text-capitalize">Country of Serve <span class="text-red">*</span></label>
                                            <input class="form-control" type="text" name="COUNTRY_SERVE" id="COUNTRY_SERVE" placeholder="Bangladesh" value="{{ setting('COUNTRY_SERVE') ?? 'Bangladesh' }}" required>
                                        </li>
                                        <li class="col-12 col-md-6 form-group">
                                            <label for="shipping_range_inside" class="text-capitalize">Text - Shipping in Range <span class="text-red">*</span></label>
                                            <input class="form-control" name="shipping_range_inside" id="shipping_range_inside" class="border border-info p-1" type="text" value="{{ setting('shipping_range_inside') ?? "Dhaka" }}" required>
                                        </li>
                                        <li class="col-12 col-md-6 form-group">
                                            <label for="CURRENCY_CODE" class="text-capitalize">Currency Code <span class="text-red">*</span></label>
                                            <input class="form-control" type="text" name="CURRENCY_CODE" id="CURRENCY_CODE" placeholder="Currency code" value="{{ setting('CURRENCY_CODE') ?? 'BDT' }}" required>
                                        </li>
                                        <li class="col-12 col-md-6 form-group">
                                            <label for="CURRENCY_CODE_MIN" class="text-capitalize">Currency Code Small <span class="text-red">*</span></label>
                                            <input class="form-control" type="text" name="CURRENCY_CODE_MIN" id="CURRENCY_CODE_MIN" placeholder="Currency code" value="{{ setting('CURRENCY_CODE_MIN') ?? 'Tk' }}" required>
                                        </li>
                                        <li class="col-12 col-md-6 form-group">
                                            <label for="CURRENCY_ICON" class="text-capitalize">Currency Icon <span class="text-red">*</span></label>
                                            <input class="form-control" type="text" name="CURRENCY_ICON" id="CURRENCY_ICON" placeholder="Currency icon" value="{{ setting('CURRENCY_ICON') ?? '৳' }}" required>
                                        </li>
                                    </ul>
                                {{-- </div> --}}
                                <hr>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-arrow-circle-up"></i>
                                    Update
                                </button>
                            </div>
                            </form>
                    </div>
                    </div>
            </div>
        </div>
    </div>
</section>
@endsection