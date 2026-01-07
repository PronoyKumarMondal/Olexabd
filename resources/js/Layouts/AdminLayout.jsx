import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function AdminLayout({ header, children }) {
    const user = usePage().props.auth.user;
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    return (
        <div className="min-h-screen bg-gray-100 flex">
            {/* Sidebar - Desktop */}
            <div className="hidden sm:flex flex-col w-64 bg-white border-r border-gray-200 min-h-screen">
                <div className="h-16 flex items-center justify-center border-b border-gray-200">
                     <Link href="/">
                        <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800" />
                    </Link>
                </div>
                <div className="flex-1 overflow-y-auto py-4">
                    <nav className="space-y-1 px-2">
                        <NavLink href={route('admin.dashboard')} active={route().current('admin.dashboard')} className="w-full flex">
                            Dashboard
                        </NavLink>
                        <NavLink href={route('admin.products.index')} active={route().current('admin.products.*')} className="w-full flex">
                            Products
                        </NavLink>
                        <NavLink href={route('admin.categories.index')} active={route().current('admin.categories.*')} className="w-full flex">
                            Categories
                        </NavLink>
                        <NavLink href={route('admin.orders.index')} active={route().current('admin.orders.*')} className="w-full flex">
                            Orders
                        </NavLink>
                    </nav>
                </div>
            </div>

            {/* Mobile Nav & Content Wrapper */}
            <div className="flex-1 flex flex-col">
                <nav className="border-b border-gray-100 bg-white sm:hidden">
                     {/* Mobile Header content (Simplified for now) */}
                     <div className="px-4 py-4 flex justify-between items-center">
                        <div className="font-bold text-lg">Admin Panel</div>
                        <button
                            onClick={() => setShowingNavigationDropdown((previousState) => !previousState)}
                            className="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none"
                        >
                             <svg className="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path className={!showingNavigationDropdown ? 'inline-flex' : 'hidden'} strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path className={showingNavigationDropdown ? 'inline-flex' : 'hidden'} strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                     </div>
                      {/* Mobile Menu */}
                    <div className={(showingNavigationDropdown ? 'block' : 'hidden') + ' sm:hidden'}>
                        <div className="space-y-1 pb-3 pt-2">
                            <ResponsiveNavLink href={route('admin.dashboard')} active={route().current('admin.dashboard')}>
                                Dashboard
                            </ResponsiveNavLink>
                            <ResponsiveNavLink href={route('admin.products.index')} active={route().current('admin.products.*')}>
                                Products
                            </ResponsiveNavLink>
                            <ResponsiveNavLink href={route('admin.categories.index')} active={route().current('admin.categories.*')}>
                                Categories
                            </ResponsiveNavLink>
                             <ResponsiveNavLink href={route('admin.orders.index')} active={route().current('admin.orders.*')}>
                                Orders
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </nav>

                {/* Top Bar (Desktop) */}
                <div className="bg-white shadow h-16 hidden sm:flex justify-between items-center px-6">
                    <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                        {header}
                    </h2>
                     <div className="relative ms-3">
                        <Dropdown>
                            <Dropdown.Trigger>
                                <span className="inline-flex rounded-md">
                                    <button type="button" className="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                                        {user.name}
                                        <svg className="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </Dropdown.Trigger>
                            <Dropdown.Content>
                                <Dropdown.Link href={route('profile.edit')}>Profile</Dropdown.Link>
                                <Dropdown.Link href={route('logout')} method="post" as="button">Log Out</Dropdown.Link>
                            </Dropdown.Content>
                        </Dropdown>
                    </div>
                </div>

                {/* Main Content */}
                <main className="p-6 flex-1 overflow-y-auto">
                    {children}
                </main>
            </div>
        </div>
    );
}
