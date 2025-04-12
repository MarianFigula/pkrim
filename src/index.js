import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './components/app/App';
import {BrowserRouter} from "react-router-dom";
import {CartProvider} from "./components/cartProvider/CartProvider";
import {AuthProvider} from "./components/auth/AuthContext";

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
    <React.StrictMode>
        <BrowserRouter>
            <AuthProvider>
                <CartProvider>
                    <App/>
                </CartProvider>
            </AuthProvider>
        </BrowserRouter>
    </React.StrictMode>
);