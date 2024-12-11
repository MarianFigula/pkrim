import { render, screen, fireEvent } from '@testing-library/react';
import { BrowserRouter } from 'react-router-dom';
import { MainSite } from './MainSite';
import '@testing-library/jest-dom';
import { CartProvider } from '../../components/cartProvider/CartProvider';
import { AuthProvider } from '../../components/auth/AuthContext';

import axios from 'axios';
jest.mock('axios'); // Mock axios

test('activates the sort by price ascending button and toggles the active class', () => {

    render(
        <BrowserRouter>
            <AuthProvider>
                <CartProvider>
                    <MainSite />
                </CartProvider>
            </AuthProvider>
        </BrowserRouter>
    );

    const priceButton = screen.getByText(/Price/i, {
        selector: 'button:has(i.bi.bi-arrow-up)',
    });
    fireEvent.click(priceButton);

    expect(priceButton).toHaveClass('active');

    fireEvent.click(priceButton);
    expect(priceButton).not.toHaveClass('active');
});

test('activates the sort by price descending button and toggles the active class', () => {

    render(
        <BrowserRouter>
            <AuthProvider>
                <CartProvider>
                    <MainSite />
                </CartProvider>
            </AuthProvider>
        </BrowserRouter>
    );

    const priceButton = screen.getByText(/Price/i, {
        selector: 'button:has(i.bi.bi-arrow-down)',
    });
    fireEvent.click(priceButton);

    expect(priceButton).toHaveClass('active');

    fireEvent.click(priceButton);
    expect(priceButton).not.toHaveClass('active');
});


test('price ascending button does not have "active" class when not clicked', () => {
    const toggleSortByPriceAsc = jest.fn();

    render(
        <BrowserRouter>
            <AuthProvider>
            <CartProvider>
                <MainSite toggleSortByPriceAsc={toggleSortByPriceAsc} />
            </CartProvider>
            </AuthProvider>
        </BrowserRouter>
    );

    const priceButton = screen.getByText(/Price/i, {
        selector: 'button:has(i.bi.bi-arrow-up)',
    });

    expect(priceButton).not.toHaveClass('active');
});

test('price descending button does not have "active" class when not clicked', () => {
    const toggleSortByPriceAsc = jest.fn();

    render(
        <BrowserRouter>
            <AuthProvider>
                <CartProvider>
                    <MainSite toggleSortByPriceAsc={toggleSortByPriceAsc} />
                </CartProvider>
            </AuthProvider>
        </BrowserRouter>
    );

    const priceButton = screen.getByText(/Price/i, {
        selector: 'button:has(i.bi.bi-arrow-down)',
    });

    expect(priceButton).not.toHaveClass('active');
});

test('activates the sort by rating ascending button and toggles the active class', () => {

    render(
        <BrowserRouter>
            <AuthProvider>
                <CartProvider>
                    <MainSite />
                </CartProvider>
            </AuthProvider>
        </BrowserRouter>
    );

    const priceButton = screen.getByText(/Rating/i, {
        selector: 'button:has(i.bi.bi-arrow-up)',
    });
    fireEvent.click(priceButton);

    expect(priceButton).toHaveClass('active');

    fireEvent.click(priceButton);
    expect(priceButton).not.toHaveClass('active');
});

test('activates the sort by rating descending button and toggles the active class', () => {

    render(
        <BrowserRouter>
            <AuthProvider>
                <CartProvider>
                    <MainSite />
                </CartProvider>
            </AuthProvider>
        </BrowserRouter>
    );

    const priceButton = screen.getByText(/Rating/i, {
        selector: 'button:has(i.bi.bi-arrow-down)',
    });
    fireEvent.click(priceButton);

    expect(priceButton).toHaveClass('active');

    fireEvent.click(priceButton);
    expect(priceButton).not.toHaveClass('active');
});

test('Rating ascending button does not have "active" class when not clicked', () => {
    const toggleSortByPriceAsc = jest.fn();

    render(
        <BrowserRouter>
            <AuthProvider>
                <CartProvider>
                    <MainSite toggleSortByPriceAsc={toggleSortByPriceAsc} />
                </CartProvider>
            </AuthProvider>
        </BrowserRouter>
    );

    const priceButton = screen.getByText(/Rating/i, {
        selector: 'button:has(i.bi.bi-arrow-up)',
    });

    expect(priceButton).not.toHaveClass('active');
});

test('rating descending button does not have "active" class when not clicked', () => {
    const toggleSortByPriceAsc = jest.fn();

    render(
        <BrowserRouter>
            <AuthProvider>
                <CartProvider>
                    <MainSite toggleSortByPriceAsc={toggleSortByPriceAsc} />
                </CartProvider>
            </AuthProvider>
        </BrowserRouter>
    );

    const priceButton = screen.getByText(/Rating/i, {
        selector: 'button:has(i.bi.bi-arrow-down)',
    });

    expect(priceButton).not.toHaveClass('active');
});
