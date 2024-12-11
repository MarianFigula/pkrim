import React, { createContext, useContext, useEffect, useState } from "react";
import axios from "axios";
import {useLocation, useNavigate} from "react-router-dom";
import {useAuth} from "../auth/AuthContext";

// Create Cart Context
const CartContext = createContext();

// Cart Provider Component
export function CartProvider({ children }) {
    const [cartCount, setCartCount] = useState(0);
    const [cartArtIds, setCartArtIds] = useState([]);
    const [cartArtDetails, setCartArtDetails] = useState([]); // Store full art details

    const serverUrl = process.env.REACT_APP_SERVER_URL;
    const location = useLocation();
    const { token } = useAuth();  // Get the token from the AuthContext
    const navigate = useNavigate()

    async function fetchCartArtIds() {
        try {
            const response = await axios.get(
                `${serverUrl}/api/cartArt/read.php`,
                {
                    headers: {
                        Authorization: `Bearer ${token}`, // Use token from AuthContext
                    },
                }
            );
            const data = response.data;
            if (data.success) {
                const cartArtIds = response.data.data;
                setCartArtIds(cartArtIds);
                setCartCount(cartArtIds.length);
                await fetchArtDetails(cartArtIds);
            } else {
                console.error("Error response", response);
            }
        } catch (error) {
            console.log()
        }
    }

    async function fetchArtDetails(artIds) {
        try {
            const response = await axios.post(
                `${serverUrl}/api/cartArt/artDetails.php`,
                {
                    art_ids: artIds,
                }
            );

            // Assuming the response contains an array of art details
            const data = response.data;
            if (data.success) {
                setCartArtDetails(response.data.data);
            }else {
                console.error("Error respnse art artDetails.php:", response);
            }
        } catch (error) {
            console.error("Error fetching art details:", error);
        }
    }

    useEffect(() => {
        // Fetch cart data on all routes except /login and /register
        const excludedRoutes = ["/login", "/register", "/forgot-password"];

        if (token && !excludedRoutes.includes(location.pathname)) {
            fetchCartArtIds();
        }
    }, [location.pathname, token, cartCount]);

    async function removeFromCart(artId) {
        try {
            const response = await axios.delete(`${serverUrl}/api/cartArt/delete.php`, {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`, // Use token from AuthContext
                },
                data: {
                    art_id: artId,
                },
            });

            const data = response.data
            if (data.success){
                setCartArtIds((prevIds) => prevIds.filter((id) => id !== artId));
                setCartArtDetails((prevDetails) =>
                    prevDetails.filter((art) => art.art_id !== artId)
                );
                decrementCartCount();
            }

        } catch (error) {
            console.error("Error removing item from cart:", error);
        }
    }

    const clearCart = () => {
        setCartArtIds([]); // Clear all art IDs
        setCartCount(0); // Reset count
    };

    const incrementCartCount = () => setCartCount((prev) => prev + 1);
    const decrementCartCount = () =>
        setCartCount((prev) => Math.max(prev - 1, 0));

    console.log("car count", cartCount);
    return (
        <CartContext.Provider
            value={{
                cartArtIds,
                cartCount,
                cartArtDetails,
                removeFromCart,
                clearCart,
                incrementCartCount,
                decrementCartCount,
            }}
        >
            {children}
        </CartContext.Provider>
    );
}

export function useCart() {
    return useContext(CartContext);
}
