import React, { createContext, useState, useEffect, useContext } from 'react';
import { useNavigate } from 'react-router-dom';

const AuthContext = createContext();

export const useAuth = () => useContext(AuthContext);

export const AuthProvider = ({ children }) => {
    const [token, setToken] = useState(localStorage.getItem('jwtToken') || null);
    const navigate = useNavigate();

    const login = (newToken) => {
        localStorage.setItem('jwtToken', newToken);
        setToken(newToken);
        navigate('/')
    };

    const logout = () => {
        localStorage.removeItem('jwtToken');
        setToken(null);
        navigate('/login');  // Redirect to login after logout
    };

    return (
        <AuthContext.Provider value={{ token, login, logout }}>
            {children}
        </AuthContext.Provider>
    );
};