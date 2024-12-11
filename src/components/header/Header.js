// src/components/Header.js
import React, {useState} from 'react';
import {Link, useLocation} from 'react-router-dom';
import "./Header.css"
import {SideBar} from "../sidebar/SideBar";
import {useCart} from "../cartProvider/CartProvider";

export function Header() {
    const {cartCount} = useCart();
    // Define routes where icons should not be displayed
    const noIconRoutes = ["/login", "/register", "/forgot-password"];
    const location = useLocation();

    const [sidebarVisible, setSidebarVisible] = useState(false);
    const hideIcons = noIconRoutes.includes(location.pathname);

    const toggleSidebar = () => {
        console.log("open")
        console.log("before:", sidebarVisible)
        setSidebarVisible(prevState => !prevState);
        console.log("after", sidebarVisible)
    }
    const closeSidebar = () => {
        setSidebarVisible(false);
    }
    return (
        <>
            <header>
                <nav>
                    <ul>
                        <li><Link to="/"><span className="hidden">MainSite</span><h1>FEI Art Gallery</h1></Link></li>
                        {!hideIcons && (
                            <>
                                <li>
                                    <Link to="/cart">
                                        <i className="bi bi-cart" style={{ fontSize: "28px" }}>
                                            {cartCount > 0 && <span className="cart-count">{cartCount}</span>}
                                        </i>
                                        <span className="hidden">Cart</span>
                                    </Link>
                                </li>
                                <li>
                                    <Link to="/user-profile">
                                        <i className="bi bi-person"></i>
                                        <span className="hidden">SignIn</span>
                                    </Link>
                                </li>
                                <li onClick={toggleSidebar}>
                                    <Link to="#">
                                        <i className="bi bi-list"></i>
                                        <span className="hidden">sidebar</span>
                                    </Link>
                                </li>
                            </>
                        )}
                    </ul>
                </nav>
            </header>
            <div className={`grey-zone ${sidebarVisible ? 'visible' : ''}`} onClick={closeSidebar}></div>
            <SideBar show={sidebarVisible} closeSidebar={closeSidebar}/>
        </>
    );
}
