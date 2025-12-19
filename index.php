<?php
session_start();

// --- 1. Mock Data ---
$categories = [
    'all' => ['name' => 'All Departments', 'icon' => 'layout-grid'],
    'electronics' => ['name' => 'Electronics', 'icon' => 'smartphone'],
    'fashion' => ['name' => 'Fashion', 'icon' => 'shirt'],
    'home' => ['name' => 'Home & Living', 'icon' => 'home'],
    'sports' => ['name' => 'Sports', 'icon' => 'activity'],
    'deals' => ['name' => 'Deals', 'icon' => 'tag'],
];

$products = [
    1 => [
        "id" => 1,
        "name" => "Wireless Noise Cancelling Headphones",
        "price" => 249.99,
        "category" => "electronics",
        "rating" => 4.8,
        "reviews" => 1245,
        "image" => "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&q=80&w=800",
        "description" => "Experience pure sound with our top-rated noise cancelling technology. 30-hour battery life for all-day listening."
    ],
    2 => [
        "id" => 2,
        "name" => "Minimalist Analog Watch",
        "price" => 129.00,
        "category" => "fashion",
        "rating" => 4.7,
        "reviews" => 89,
        "image" => "https://images.unsplash.com/photo-1524805444758-089113d48a6d?auto=format&fit=crop&q=80&w=800",
        "description" => "A timeless classic featuring a genuine leather strap and a clean, easy-to-read dial. Water-resistant up to 50m."
    ],
    3 => [
        "id" => 3,
        "name" => "Smart Home Speaker",
        "price" => 89.99,
        "category" => "electronics",
        "rating" => 4.6,
        "reviews" => 560,
        "image" => "https://images.unsplash.com/photo-1589492477829-5e65395b66cc?auto=format&fit=crop&q=80&w=800",
        "description" => "Voice-controlled smart speaker with room-filling sound. Control your smart home devices with simple commands."
    ],
    4 => [
        "id" => 4,
        "name" => "Ceramic Pour-Over Coffee Set",
        "price" => 45.00,
        "category" => "home",
        "rating" => 4.9,
        "reviews" => 210,
        "image" => "https://images.unsplash.com/photo-1522204523234-8729aa6e3d5f?auto=format&fit=crop&q=80&w=800",
        "description" => "Brew the perfect cup of coffee at home. Includes a ceramic dripper, glass carafe, and reusable filter."
    ],
    5 => [
        "id" => 5,
        "name" => "Premium Yoga Mat",
        "price" => 68.00,
        "category" => "sports",
        "rating" => 4.8,
        "reviews" => 340,
        "image" => "https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?auto=format&fit=crop&q=80&w=800",
        "description" => "Non-slip texture and extra cushioning for joint support. Eco-friendly material free from harmful chemicals."
    ],
    6 => [
        "id" => 6,
        "name" => "Denim Jacket",
        "price" => 85.00,
        "category" => "fashion",
        "rating" => 4.5,
        "reviews" => 112,
        "image" => "https://images.unsplash.com/photo-1576995853123-5a10305d93c0?auto=format&fit=crop&q=80&w=800",
        "description" => "A wardrobe staple. Rugged denim construction with a comfortable fit that layers easily over hoodies or tees."
    ],
    7 => [
        "id" => 7,
        "name" => "Modern Desk Lamp",
        "price" => 59.00,
        "category" => "home",
        "rating" => 4.7,
        "reviews" => 76,
        "image" => "https://images.unsplash.com/photo-1507473888900-52e1adad8ce3?auto=format&fit=crop&q=80&w=800",
        "description" => "Adjustable LED desk lamp with touch controls and 3 color temperature modes. Includes a USB charging port."
    ],
    8 => [
        "id" => 8,
        "name" => "4K Action Camera",
        "price" => 199.00,
        "category" => "deals",
        "rating" => 4.6,
        "reviews" => 425,
        "image" => "https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?auto=format&fit=crop&q=80&w=800",
        "description" => "Capture your adventures in stunning 4K. Waterproof, shockproof, and ready for any environment. Limited time deal."
    ]
];

// --- 2. Logic / Actions ---

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Helper to calculate totals
function getCartDetails($cartSession, $productsDb) {
    $items = [];
    $subtotal = 0;
    $count = 0;
    
    foreach ($cartSession as $pid => $qty) {
        if (isset($productsDb[$pid])) {
            $product = $productsDb[$pid];
            $product['quantity'] = $qty;
            $items[] = $product;
            $subtotal += $product['price'] * $qty;
            $count += $qty;
        }
    }
    
    // Free shipping over $100
    $shipping = ($subtotal > 100) ? 0 : 15; 
    if ($count === 0) $shipping = 0;
    
    return [
        'items' => $items,
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'total' => $subtotal + $shipping,
        'count' => $count
    ];
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    switch ($action) {
        case 'add':
            if (!isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] = 0;
            }
            $_SESSION['cart'][$id]++;
            break;
            
        case 'update':
            $qty = intval($_POST['qty'] ?? 1);
            if ($qty > 0) {
                $_SESSION['cart'][$id] = $qty;
            } else {
                unset($_SESSION['cart'][$id]);
            }
            break;
            
        case 'remove':
            unset($_SESSION['cart'][$id]);
            break;
            
        case 'checkout':
            $_SESSION['cart'] = [];
            header("Location: ?view=success&order=" . rand(10000, 99999));
            exit;
            
        case 'contact':
             echo "<script>alert('Message sent successfully!'); window.location.href='?view=contact';</script>";
             exit;

        case 'subscribe':
            break;
    }
    
    $redirect_view = $_POST['redirect_view'] ?? 'home';
    header("Location: ?view=" . $redirect_view . (isset($_POST['category']) ? "&category=".$_POST['category'] : ""));
    exit;
}

$view = $_GET['view'] ?? 'home';
$categoryFilter = $_GET['category'] ?? 'all';
$cartDetails = getCartDetails($_SESSION['cart'], $products);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXUS | Online Shopping</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-gray-50 text-slate-800 flex flex-col min-h-screen">

    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white shadow-sm z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="?view=home" class="flex-shrink-0 flex items-center cursor-pointer group">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-2 transform group-hover:rotate-12 transition-transform">
                        <i data-lucide="shopping-bag" class="text-white w-5 h-5"></i>
                    </div>
                    <span class="text-2xl font-bold text-slate-900 tracking-tight">NEXUS</span>
                </a>

                <!-- Desktop Nav -->
                <div class="hidden md:flex space-x-8">
                    <a href="?view=home" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Shop</a>
                    <a href="?view=home&category=deals" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Deals</a>
                    <a href="?view=about" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">About</a>
                    <a href="?view=track" class="text-slate-600 hover:text-blue-600 font-medium transition-colors">Track Order</a>
                </div>

                <!-- Icons -->
                <div class="flex items-center space-x-4">
                    <button class="text-slate-500 hover:text-blue-600 transition-colors">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                    <a href="?view=cart" class="text-slate-500 hover:text-blue-600 transition-colors relative">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        <?php if ($cartDetails['count'] > 0): ?>
                            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform bg-blue-600 rounded-full">
                                <?= $cartDetails['count'] ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <button class="md:hidden text-slate-500">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content Router -->
    <div class="flex-grow pt-16">
        
        <?php if ($view === 'home'): ?>
            <!-- Hero Section -->
            <div class="relative bg-slate-900 overflow-hidden">
                <div class="absolute inset-0">
                    <img class="w-full h-full object-cover opacity-30" src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Shopping mall background">
                </div>
                <div class="relative max-w-7xl mx-auto py-24 px-4 sm:py-32 sm:px-6 lg:px-8">
                    <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl mb-6">
                        Everything You Need.<br>
                        <span class="text-blue-400">Delivered Fast.</span>
                    </h1>
                    <p class="mt-6 text-xl text-gray-300 max-w-3xl">
                        Discover thousands of products from top brands in electronics, fashion, home, and more. Quality guaranteed.
                    </p>
                    <div class="mt-10">
                        <a href="#shop-section" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-slate-900 bg-white hover:bg-gray-100 md:py-4 md:text-lg md:px-10 transition-colors">
                            Start Shopping
                        </a>
                    </div>
                </div>
            </div>

            <!-- Shop Section -->
            <main id="shop-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <!-- Filters -->
                <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 space-y-4 md:space-y-0">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Featured Products</h2>
                        <p class="mt-1 text-slate-500">Handpicked items for you.</p>
                    </div>
                    
                    <div class="flex overflow-x-auto pb-2 md:pb-0 hide-scrollbar space-x-2">
                        <?php foreach ($categories as $key => $cat): ?>
                            <a href="?view=home&category=<?= $key ?>#shop-section" 
                               class="flex items-center px-4 py-2 text-sm font-medium whitespace-nowrap transition-all border rounded-full <?= ($categoryFilter === $key) ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-slate-600 border-gray-200 hover:border-blue-500 hover:text-blue-600' ?>">
                                <?php if (isset($cat['icon'])): ?>
                                    <i data-lucide="<?= $cat['icon'] ?>" class="w-4 h-4 mr-2"></i>
                                <?php endif; ?>
                                <?= $cat['name'] ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-10 gap-x-6">
                    <?php 
                    $hasProducts = false;
                    foreach ($products as $product): 
                        if ($categoryFilter !== 'all' && $product['category'] !== $categoryFilter) continue;
                        $hasProducts = true;
                    ?>
                        <div class="group relative bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition-shadow duration-300">
                            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-100 relative h-64">
                                <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-300">
                                <?php if($product['category'] === 'deals'): ?>
                                    <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-sm">SALE</div>
                                <?php endif; ?>
                            </div>
                            <div class="p-4 flex-1 flex flex-col">
                                <h3 class="text-sm font-medium text-slate-900 line-clamp-1 mb-1">
                                    <?= $product['name'] ?>
                                </h3>
                                <div class="flex items-center mb-2">
                                    <i data-lucide="star" class="w-4 h-4 text-yellow-400 fill-current"></i>
                                    <span class="ml-1 text-xs text-slate-500"><?= $product['rating'] ?> (<?= $product['reviews'] ?>)</span>
                                </div>
                                <p class="text-lg font-bold text-slate-900 mb-2">$<?= number_format($product['price'], 2) ?></p>
                                <p class="text-xs text-slate-500 line-clamp-2 mb-4"><?= $product['description'] ?></p>
                                
                                <form method="POST" class="mt-auto">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="redirect_view" value="home">
                                    <input type="hidden" name="category" value="<?= $categoryFilter ?>">
                                    <button type="submit" class="w-full bg-slate-900 text-white hover:bg-blue-600 py-2 px-4 text-sm font-medium rounded-lg transition-colors flex items-center justify-center">
                                        <i data-lucide="shopping-cart" class="w-4 h-4 mr-2"></i> Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!$hasProducts): ?>
                    <div class="text-center py-20 bg-white rounded-lg border border-gray-200">
                        <p class="text-slate-500">No products found in this category.</p>
                        <a href="?view=home" class="mt-4 inline-block text-blue-600 font-medium hover:underline">View All Products</a>
                    </div>
                <?php endif; ?>

                <!-- Newsletter -->
                <div class="mt-20 bg-blue-600 rounded-2xl p-8 md:p-12 text-center text-white">
                    <h2 class="text-2xl font-bold mb-4">Get 10% Off Your First Order</h2>
                    <p class="mb-8 opacity-90 max-w-lg mx-auto">Join our newsletter to receive exclusive deals, new product alerts, and more.</p>
                    <form method="POST" class="max-w-md mx-auto flex gap-2">
                        <input type="hidden" name="action" value="subscribe">
                        <input type="email" required placeholder="Enter your email" class="flex-1 px-4 py-3 rounded-lg text-slate-900 focus:outline-none focus:ring-2 focus:ring-white">
                        <button type="submit" class="px-6 py-3 bg-slate-900 text-white font-medium rounded-lg hover:bg-slate-800 transition-colors">Subscribe</button>
                    </form>
                </div>
            </main>

        <?php elseif ($view === 'cart'): ?>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <h1 class="text-3xl font-bold text-slate-900 mb-8">Shopping Cart</h1>

                <?php if (empty($cartDetails['items'])): ?>
                    <div class="text-center py-20 bg-white border border-gray-200 rounded-xl shadow-sm">
                        <div class="inline-flex p-4 bg-gray-50 rounded-full mb-4">
                            <i data-lucide="shopping-cart" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h2 class="text-xl font-bold text-slate-700">Your cart is empty</h2>
                        <p class="mt-2 text-slate-500">Looks like you haven't made your choice yet.</p>
                        <div class="mt-8">
                            <a href="?view=home" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start">
                        <section class="lg:col-span-7">
                            <ul class="divide-y divide-gray-200 bg-white border border-gray-200 rounded-xl overflow-hidden">
                                <?php foreach ($cartDetails['items'] as $item): ?>
                                    <li class="flex p-6">
                                        <div class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-md overflow-hidden bg-gray-50">
                                            <img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="w-full h-full object-center object-cover">
                                        </div>
                                        <div class="ml-6 flex-1 flex flex-col justify-between">
                                            <div>
                                                <div class="flex justify-between">
                                                    <h3 class="text-sm font-medium text-slate-900"><?= $item['name'] ?></h3>
                                                </div>
                                                <p class="mt-1 text-sm text-slate-500"><?= $categories[$item['category']]['name'] ?></p>
                                                <p class="mt-1 text-sm font-bold text-slate-900">$<?= number_format($item['price'], 2) ?></p>
                                            </div>
                                            <div class="flex items-center justify-between mt-4">
                                                <div class="flex items-center border border-gray-300 rounded-md">
                                                    <form method="POST" class="contents">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                        <input type="hidden" name="qty" value="<?= $item['quantity'] - 1 ?>">
                                                        <input type="hidden" name="redirect_view" value="cart">
                                                        <button type="submit" class="p-1 px-3 text-slate-600 hover:bg-gray-100">
                                                            <i data-lucide="minus" class="w-3 h-3"></i>
                                                        </button>
                                                    </form>
                                                    <span class="px-2 text-sm text-slate-900 font-medium"><?= $item['quantity'] ?></span>
                                                    <form method="POST" class="contents">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                        <input type="hidden" name="qty" value="<?= $item['quantity'] + 1 ?>">
                                                        <input type="hidden" name="redirect_view" value="cart">
                                                        <button type="submit" class="p-1 px-3 text-slate-600 hover:bg-gray-100">
                                                            <i data-lucide="plus" class="w-3 h-3"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                <form method="POST">
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                    <input type="hidden" name="redirect_view" value="cart">
                                                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium flex items-center">
                                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </section>

                        <section class="mt-8 lg:mt-0 lg:col-span-5 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900 mb-4">Order Summary</h2>
                            <dl class="space-y-3 text-sm">
                                <div class="flex items-center justify-between text-slate-600">
                                    <dt>Subtotal</dt>
                                    <dd class="font-medium text-slate-900">$<?= number_format($cartDetails['subtotal'], 2) ?></dd>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <dt>Shipping</dt>
                                    <dd class="font-medium text-slate-900">
                                        <?= ($cartDetails['shipping'] == 0) ? 'Free' : '$'.number_format($cartDetails['shipping'], 2) ?>
                                    </dd>
                                </div>
                                <div class="border-t border-gray-200 pt-3 flex items-center justify-between">
                                    <dt class="text-base font-bold text-slate-900">Total</dt>
                                    <dd class="text-xl font-bold text-blue-600">$<?= number_format($cartDetails['total'], 2) ?></dd>
                                </div>
                            </dl>
                            <div class="mt-6">
                                <a href="?view=checkout" class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors shadow-lg">
                                    Checkout
                                </a>
                            </div>
                            <div class="mt-4 flex items-center justify-center text-xs text-slate-500">
                                <i data-lucide="shield-check" class="w-4 h-4 mr-1 text-green-500"></i> Secure Checkout
                            </div>
                        </section>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($view === 'checkout'): ?>
            <div class="bg-gray-50 pt-16 pb-24">
                <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                    <a href="?view=cart" class="flex items-center text-slate-500 hover:text-slate-900 mb-8 font-medium">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Cart
                    </a>
                    
                    <h1 class="text-3xl font-bold text-slate-900 mb-8">Checkout</h1>
                    
                    <form method="POST" class="space-y-8">
                        <input type="hidden" name="action" value="checkout">
                        
                        <!-- Contact -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 text-sm">1</div>
                                Contact Information
                            </h2>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                                <input type="email" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5 border" placeholder="you@example.com">
                            </div>
                        </div>

                        <!-- Shipping -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 text-sm">2</div>
                                Shipping Address
                            </h2>
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">First Name</label>
                                    <input type="text" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5 border">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Last Name</label>
                                    <input type="text" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5 border">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                                    <input type="text" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5 border">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">City</label>
                                    <input type="text" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5 border">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Zip Code</label>
                                    <input type="text" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5 border">
                                </div>
                            </div>
                        </div>

                        <!-- Payment -->
                        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                            <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3 text-sm">3</div>
                                Payment
                            </h2>
                            <div class="grid grid-cols-1 gap-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Card Number</label>
                                    <div class="relative">
                                        <input type="text" required placeholder="0000 0000 0000 0000" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5 pl-10 border">
                                        <div class="absolute left-3 top-3 text-gray-400">
                                            <i data-lucide="credit-card" class="w-5 h-5"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Expiry Date</label>
                                        <input type="text" required placeholder="MM / YY" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5 border">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">CVC</label>
                                        <input type="text" required placeholder="123" class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5 border">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-slate-900 border border-transparent rounded-lg shadow-md py-4 px-4 text-lg font-bold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                            Place Order ($<?= number_format($cartDetails['total'], 2) ?>)
                        </button>
                    </form>
                </div>
            </div>

        <?php elseif ($view === 'success'): ?>
            <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-4">
                <div class="max-w-md w-full bg-white shadow-xl p-8 rounded-2xl border border-gray-200 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                        <i data-lucide="check" class="w-8 h-8 text-green-600"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-2">Order Confirmed!</h2>
                    <p class="text-slate-500 mb-8">Thank you for your purchase. We've sent a confirmation email to you.</p>
                    
                    <div class="bg-gray-50 p-4 rounded-lg mb-8 text-left border border-gray-100">
                        <p class="text-xs text-slate-500 uppercase font-bold mb-1">Order Reference</p>
                        <p class="text-lg font-mono font-bold text-slate-800">#NEX-<?= htmlspecialchars($_GET['order'] ?? '00000') ?></p>
                    </div>

                    <a href="?view=home" class="block w-full py-3 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                        Back to Home
                    </a>
                </div>
            </div>

        <?php elseif ($view === 'track'): ?>
            <div class="min-h-[80vh] flex flex-col items-center justify-center px-4 bg-gray-50">
                <div class="max-w-md w-full space-y-8 bg-white p-10 shadow-lg rounded-2xl border border-gray-200">
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="truck" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900">Track Your Order</h2>
                        <p class="mt-2 text-sm text-slate-500">Enter your order details below to check status.</p>
                    </div>
                    <form class="mt-8 space-y-4" onsubmit="event.preventDefault(); alert('Status: Shipped');">
                        <div>
                            <label class="sr-only">Order Number</label>
                            <input type="text" required class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Order No. (e.g. #NEX-1234)">
                        </div>
                        <div>
                            <label class="sr-only">Email Address</label>
                            <input type="email" required class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="Email Address">
                        </div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-slate-900 hover:bg-slate-800 focus:outline-none transition-colors">Track</button>
                    </form>
                </div>
            </div>
            
        <?php elseif ($view === 'about'): ?>
            <div class="bg-white py-20">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h1 class="text-4xl font-bold text-slate-900 mb-4">About NEXUS</h1>
                        <div class="w-20 h-1 bg-blue-600 mx-auto rounded-full"></div>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
                        <div>
                            <img src="https://images.unsplash.com/photo-1497215728101-856f4ea42174?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Office" class="rounded-xl shadow-lg">
                        </div>
                        <div class="prose prose-slate">
                            <h3 class="text-2xl font-bold text-slate-900 mb-4">Our Mission</h3>
                            <p class="text-slate-600 leading-relaxed mb-4">
                                At NEXUS, we believe that shopping should be simple, enjoyable, and accessible to everyone. 
                                Founded in 2024, our mission is to connect people with high-quality products that enhance their daily lives.
                            </p>
                            <p class="text-slate-600 leading-relaxed">
                                From cutting-edge electronics to sustainable fashion and home essentials, 
                                we curate our catalog with care to ensure you get the best value without compromising on quality.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($view === 'contact'): ?>
            <div class="min-h-screen bg-gray-50 py-20">
                <div class="max-w-2xl mx-auto px-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-8">
                            <h2 class="text-2xl font-bold text-slate-900 mb-2">Contact Us</h2>
                            <p class="text-slate-500 mb-8">Have questions? We're here to help.</p>
                            
                            <form method="POST" class="space-y-6">
                                <input type="hidden" name="action" value="contact">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
                                    <input type="text" name="name" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 border">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                    <input type="email" name="email" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 border">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Message</label>
                                    <textarea name="message" rows="4" required class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2.5 border"></textarea>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors">Send Message</button>
                            </form>
                        </div>
                        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                             <p class="text-sm text-slate-500 text-center">Or email us directly at <strong class="text-slate-700">support@nexus-store.com</strong></p>
                        </div>
                    </div>
                </div>
            </div>

        <?php elseif ($view === 'privacy' || $view === 'terms' || $view === 'refunds' || $view === 'shipping'): ?>
            <div class="min-h-screen bg-white py-20">
                <div class="max-w-3xl mx-auto px-4">
                    <h1 class="text-3xl font-bold text-slate-900 mb-8 capitalize"><?= str_replace('_', ' ', $view) ?> Policy</h1>
                    <div class="prose prose-slate max-w-none text-slate-600 space-y-6">
                        <?php if ($view === 'shipping'): ?>
                            <p>We offer worldwide shipping. Orders are typically processed within 24-48 hours.</p>
                            <p><strong>Standard Shipping:</strong> 5-7 business days.</p>
                            <p><strong>Express Shipping:</strong> 2-3 business days.</p>
                        <?php elseif ($view === 'refunds'): ?>
                            <p>We have a 30-day return policy. If you are not satisfied with your purchase, you can return it within 30 days for a full refund.</p>
                            <p>Items must be unused and in original packaging.</p>
                        <?php else: ?>
                            <p>This is a standard placeholder for the <?= $view ?> page. In a real application, this would contain the full legal text required for compliance.</p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 mt-auto">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-1">
                    <span class="text-2xl font-bold text-white tracking-tight">NEXUS</span>
                    <p class="mt-4 text-sm text-slate-400">Your one-stop shop for everything modern lifestyle.</p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Shop</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="?view=home&category=electronics" class="hover:text-white transition-colors">Electronics</a></li>
                        <li><a href="?view=home&category=fashion" class="hover:text-white transition-colors">Fashion</a></li>
                        <li><a href="?view=home&category=home" class="hover:text-white transition-colors">Home</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Support</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="?view=track" class="hover:text-white transition-colors">Order Status</a></li>
                        <li><a href="?view=shipping" class="hover:text-white transition-colors">Shipping</a></li>
                        <li><a href="?view=contact" class="hover:text-white transition-colors">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase mb-4">Legal</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="?view=privacy" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="?view=terms" class="hover:text-white transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center text-sm">
                <p>&copy; 2024 NEXUS Inc. All rights reserved.</p>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition-colors">Twitter</a>
                    <a href="#" class="hover:text-white transition-colors">Instagram</a>
                    <a href="#" class="hover:text-white transition-colors">Facebook</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
