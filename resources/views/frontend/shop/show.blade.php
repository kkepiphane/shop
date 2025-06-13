@extends('app')

@section('content')
<!-- Start Hero Section -->
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-8">
                <div class="">
                    <h1>Détail / {{$product->name}}</h1>
                </div>
            </div>
            <div class="col-lg-4">

            </div>
        </div>
    </div>
</div>
<!-- End Hero Section -->



<div class="untree_co-section product-section before-footer-section">
    <div class="container">
        <div class="row">
            <!-- Start Column 1 -->
            <div class="col-md-3 col-lg-3 mb-5">
                <a class="product-item" href="#">
                    <img src="{{ asset('storage/' . ($product->image_path)) ?? asset('assets/default/default.png') }}" class="img-fluid product-thumbnail">

                </a>
            </div>
            <div class="col-md-9 col-lg-9 mb-5">
                <h3 class="text-black h4">{{$product->name}}</h3>
                <strong class="product-price">{{$product->price}} FCFA</strong>
                <p class="mb-4">{{$product->description}}</p>
                <div class="row">
                    <div class="col-md-4">
                        <a href="#" class="icon-cross add-to-cart btn btn-outline-black btn-sm btn-block" data-product-id="{{ $product->id }}"><img src="{{ asset('assets/images/cross.svg')}}" class="" style="margin-right:8px;"> Ajouter au panier</a>
                    </div>
                    <div class="col-md-4"><a href="{{ route('checkout') }}" class="btn btn-primary btn-sm btn-block">Passer la commande</a></div>
                    <div class="col-md-4"></div>
                </div>
            </div>
            <!-- End Column 4 -->

        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row mb-5">
                    <div class="col-md-6">
                        <a href="{{ route('shop') }}" class="btn btn-outline-black btn-sm btn-block">Continue Shopping</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 pl-5">
                <div class="row justify-content-end">
                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-md-12 text-right border-bottom mb-5">
                                <h3 class="text-black h4 text-uppercase">Totaux du panier</h3>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <span class="text-black">Subtotal</span>
                            </div>
                            <div class="col-md-6 text-right">
                                <strong class="text-black">{{ \Cart::getSubTotal() }} FCFA</strong>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <span class="text-black">Total</span>
                            </div>
                            <div class="col-md-6 text-right">
                                <strong class="text-black">{{ \Cart::getTotal() }} FCFA</strong>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')

@endpush