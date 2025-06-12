	<nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark" arial-label="Furni navigation bar">

		<div class="container">
			<a class="navbar-brand" href="{{ route('home') }}">Furni<span>.</span></a>

			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsFurni" aria-controls="navbarsFurni" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarsFurni">
				<ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
					<li class="nav-item active">
						<a class="nav-link" href="{{ route('home') }}">Accueil</a>
					</li>
					<li><a class="nav-link" href="{{ route('shop') }}">Boutique</a></li>
					<li><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
					@auth
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
							{{ Auth::user()->full_name }}
						</a>
						<ul class="dropdown-menu">
							<li>
								<form method="POST" action="{{ route('logout') }}">
									@csrf
									<button type="submit" class="dropdown-item">Déconnexion</button>
								</form>
							</li>
						</ul>
					</li>
					@else
					<li><a class="nav-link" href="{{ route('login') }}">Connexion</a></li>
					<li><a class="nav-link" href="{{ route('register') }}">Inscription</a></li>
					@endauth
				</ul>

				<ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">
					<li><a class="nav-link" href="#"><img src="{{ asset('assets/images/user.svg')}}"></a></li>
					<li>
						<a class="nav-link" href="{{ route('cart.view') }}">
							<img src="{{ asset('assets/images/cart.svg')}}">
							<span class="cart-count-badge">{{ Cart::getTotalQuantity() }}</span>
						</a>
					</li>
				</ul>
			</div>
		</div>

	</nav>