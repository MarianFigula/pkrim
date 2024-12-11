import React from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
import { MemoryRouter, Routes, Route } from 'react-router-dom';
import { Header } from './Header';
import CartSite from '../../sites/cartSite/CartSite.js';
import { LoginSite } from '../../sites/loginSite/LoginSite.js';
import { MainSite } from '../../sites/mainSite/MainSite.js';
import '@testing-library/jest-dom';
import { AuthProvider } from '../auth/AuthContext';

import { CartProvider } from '../cartProvider/CartProvider'; // Import CartProvider

test('navigates to CartSite when bi bi-cart is clicked', () => {
    render(
        <MemoryRouter initialEntries={['/']}>
            <AuthProvider>
            <CartProvider>  {/* Wrap with CartProvider */}
                <Routes>
                    <Route path="/" element={<Header />} />
                    <Route path="/cart" element={<CartSite />} />
                </Routes>
            </CartProvider>
            </AuthProvider>
        </MemoryRouter>
    );

    const cartLink = screen.getByRole('link', { name: /cart/i });
    fireEvent.click(cartLink);

    expect(screen.getByText(/Your items/i)).toBeInTheDocument();
});


test('open sidebar when click on sidebar icon', () => {
    render(
        <MemoryRouter initialEntries={['/']}>
            <AuthProvider>
            <CartProvider>
                <Routes>
                    <Route path="/" element={<Header />} />
                </Routes>
            </CartProvider>
            </AuthProvider>
        </MemoryRouter>
    );

    const sidebarLink = screen.getByRole('link', { name: /sidebar/i });


    fireEvent.click(sidebarLink);

    expect(screen.getByText(/Logout/i)).toBeInTheDocument();
});


test('navigates to MainSite when the link is clicked', () => {
    render(
        <MemoryRouter initialEntries={['/']}>
            <AuthProvider>
            <CartProvider>
                <Routes>
                    {/* Render both Header and MainSite at the root path */}
                    <Route path="/" element={<><Header /><MainSite /></>} />
                </Routes>
            </CartProvider>
            </AuthProvider>

        </MemoryRouter>
    );

    const link = screen.getByRole('link', { name: /MainSite/ });

    fireEvent.click(link);
    expect(screen.getByText(/Discover new Arts/i)).toBeInTheDocument();
});