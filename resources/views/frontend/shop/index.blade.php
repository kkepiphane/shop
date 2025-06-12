@extends('app')

@section('content')
<!-- Start Hero Section -->
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1>Boutique</h1>
                </div>
            </div>
            <div class="col-lg-7">

            </div>
        </div>
    </div>
</div>
<!-- End Hero Section -->



<div class="untree_co-section product-section before-footer-section">
    <div class="container">
        <div class="row">

            <!-- Start Column 1 -->
            @foreach ($products as $product)
            <div class="col-12 col-md-4 col-lg-3 mb-5">
                <a class="product-item" href="#">
                    <img src="{{ asset('storage/' . ($product->image_path)) ?? asset('assets/default/default.png') }}" class="img-fluid product-thumbnail">
                    <h3 class="product-title">{{$product->name}}</h3>
                    <strong class="product-price">{{$product->price}} FCFA</strong>

                    <span class="icon-cross add-to-cart" data-product-id="{{ $product->id }}">
                        <img src="{{ asset('assets/images/cross.svg')}}" class="img-fluid">
                    </span>
                </a>
            </div>
            @endforeach
            <!-- End Column 4 -->

        </div>
    </div>
</div>

@endsection
@push('scripts')

@endpush