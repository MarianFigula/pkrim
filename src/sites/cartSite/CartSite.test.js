import React from 'react';
import { render, screen, fireEvent } from '@testing-library/react';
import { MemoryRouter, Routes, Route } from 'react-router-dom';
import CartSite  from './CartSite.js';
import '@testing-library/jest-dom';

import {CartProvider} from '../../components/cartProvider/CartProvider';

test("test to see when you first time click on page to see if cart is empty" , () => {

render(
    <MemoryRouter initialEntries={['/']}>
        <CartProvider>
            <Routes>
                <Route path="/" element={<CartSite />} />
            </Routes>
        </CartProvider>
    </MemoryRouter>
);

expect(screen.getByText(/Shopping Cart is empty/i)).toBeInTheDocument();

});
