import React from "react";
import "./SideBar.css"
import {useAuth} from "../auth/AuthContext";

export function SideBar({show, closeSidebar }){

    const {logout} = useAuth()

    const handleLogout = () => {
        logout(); // Log out the user
        closeSidebar(); // Close the sidebar
    };

    return(
        <>
            <section className={`sidebar ${show ? "show" : ""}`} id="sidebar-id">
                <h1 className="text-center sidebar-title">
                    FEI Art Gallery
                </h1>
                <hr className="mb-2"/>
                <div className="sidebar-content text-center">
                    <p
                        onClick={handleLogout}
                        style={{ cursor: "pointer", textDecoration: "underline" }}
                    >
                        Logout
                    </p>
                </div>

            </section>
        </>
    )
}