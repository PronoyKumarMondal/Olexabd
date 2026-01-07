import { Head, useForm } from '@inertiajs/react';

export default function BkashMock({ amount, order_id }) {
    const { post, processing } = useForm({
        amount: amount,
        order_id_db: '', // If we had a real ID
        trxID: 'TRX-' + Math.floor(Math.random() * 1000000)
    });

    const handleSuccess = (e) => {
        e.preventDefault();
        post(route('bkash.success'));
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-[#E2136E]">
            <Head title="bKash Payment" />

            <div className="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md text-center animate-fade-in-up">
                <div className="mb-6">
                    <img
                        src="https://logos-download.com/wp-content/uploads/2022/01/BKash_Logo_icon.png"
                        alt="bKash"
                        className="h-16 mx-auto"
                    />
                </div>

                <h2 className="text-2xl font-bold text-gray-800 mb-2">Merchant Payment</h2>
                <p className="text-gray-500 mb-6">Completing payment for Order <strong>#{order_id}</strong></p>

                <div className="bg-gray-100 p-4 rounded-md mb-6">
                    <div className="flex justify-between mb-2">
                        <span className="text-gray-600">Merchant</span>
                        <span className="font-semibold">Appliance Store</span>
                    </div>
                    <div className="flex justify-between">
                        <span className="text-gray-600">Amount</span>
                        <span className="font-bold text-xl">৳ {amount}</span>
                    </div>
                </div>

                <form onSubmit={handleSuccess} className="space-y-4">
                    <div>
                        <input
                            type="text"
                            placeholder="Enter bKash Account Number"
                            className="w-full px-4 py-3 rounded border border-gray-300 focus:border-[#E2136E] focus:ring-[#E2136E]"
                            required
                        />
                    </div>

                    <button
                        type="submit"
                        disabled={processing}
                        className="w-full bg-[#E2136E] text-white font-bold py-3 rounded hover:bg-[#c1105e] transition duration-200"
                    >
                        Confirm Payment
                    </button>
                </form>

                <div className="mt-4 text-xs text-gray-400">
                    This is a sandbox simulation. No real money will be deducted.
                </div>
            </div>
        </div>
    );
}
