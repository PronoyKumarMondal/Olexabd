import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import Dropdown from '@/Components/Dropdown';

export default function ShopLayout({ children }) {
    const { auth, flash } = usePage().props;
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="bg-white border-b border-gray-100 sticky top-0 z-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex">
                            <div className="shrink-0 flex items-center">
                                <Link href="/">
                                    <ApplicationLogo className="block h-9 w-auto fill-current text-indigo-600" />
                                </Link>
                                <span className="ml-2 font-bold text-xl tracking-tight text-gray-800">ApplianceStore</span>
                            </div>
                        </div>

                        <div className="hidden sm:flex sm:items-center sm:ml-6">
                            {auth.user ? (
                                <div className="ml-3 relative">
                                    <Dropdown>
                                        <Dropdown.Trigger>
                                            <span className="inline-flex rounded-md">
                                                <button type="button" className="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                    {auth.user.name}
                                                    <svg className="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                                                    </svg>
                                                </button>
                                            </span>
                                        </Dropdown.Trigger>
                                        <Dropdown.Content>
                                            {auth.user.is_admin && (
                                                <Dropdown.Link href={route('admin.dashboard')}>Admin Panel</Dropdown.Link>
                                            )}
                                            <Dropdown.Link href={route('profile.edit')}>Profile</Dropdown.Link>
                                            <Dropdown.Link href={route('logout')} method="post" as="button">Log Out</Dropdown.Link>
                                        </Dropdown.Content>
                                    </Dropdown>
                                </div>
                            ) : (
                                <div className="space-x-4">
                                    <Link href={route('login')} className="text-gray-700 hover:text-indigo-600 font-medium">Log in</Link>
                                    <Link href={route('register')} className="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">Register</Link>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </nav>

            <main>
                {flash && flash.success && (
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                        <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <strong className="font-bold">Success!</strong>
                            <span className="block sm:inline"> {flash.success}</span>
                        </div>
                    </div>
                )}
                {children}
            </main>

            <footer className="bg-gray-800 text-white py-8 mt-12">
                <div className="max-w-7xl mx-auto px-4 text-center">
                    <p>&copy; 2025 ApplianceStore. All rights reserved.</p>
                </div>
            </footer>
        </div>
    );
}
