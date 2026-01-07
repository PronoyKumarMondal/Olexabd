import ShopLayout from '@/Layouts/ShopLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Index({ products }) {
    const { post } = useForm({});

    const handleBuyNow = (productId, price) => {
        // Simple Buy Now flow (Mock Order)
        post(route('checkout.init', {
            amount: price,
            order_id: 'ORDER-' + Math.floor(Math.random() * 10000)
        }));
    };

    return (
        <ShopLayout>
            <Head title="Home" />

            {/* Hero Section */}
            <div className="bg-indigo-600 text-white py-16">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 className="text-4xl font-extrabold tracking-tight sm:text-5xl mb-4">
                        Premium Home Appliances
                    </h1>
                    <p className="text-xl text-indigo-100 mb-8">
                        Upgrade your home with the latest technology.
                    </p>
                    <a href="#products" className="bg-white text-indigo-600 px-8 py-3 rounded-full font-bold hover:bg-indigo-50 transition">
                        Shop Now
                    </a>
                </div>
            </div>

            {/* Products Grid */}
            <div id="products" className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h2 className="text-2xl font-bold text-gray-900 mb-6">Featured Products</h2>

                {products.length === 0 ? (
                    <div className="text-center py-12 text-gray-500">
                        No products available yet. Check back soon!
                    </div>
                ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        {products.map((product) => (
                            <div key={product.id} className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                                <div className="h-48 bg-gray-200 flex items-center justify-center text-gray-400">
                                    {/* Placeholder Image */}
                                    {product.image ? (
                                        <img src={product.image} alt={product.name} className="h-full w-full object-cover" />
                                    ) : (
                                        <svg className="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    )}
                                </div>
                                <div className="p-4">
                                    <div className="text-sm text-indigo-600 font-semibold mb-1">
                                        {product.category ? product.category.name : 'Appliance'}
                                    </div>
                                    <h3 className="text-lg font-bold text-gray-900 mb-2">{product.name}</h3>
                                    <p className="text-gray-600 text-sm mb-4 line-clamp-2">{product.description}</p>
                                    <div className="flex items-center justify-between">
                                        <span className="text-xl font-bold text-gray-900">${product.price}</span>
                                        <button
                                            onClick={() => handleBuyNow(product.id, product.price)}
                                            className="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition"
                                        >
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </ShopLayout>
    );
}
