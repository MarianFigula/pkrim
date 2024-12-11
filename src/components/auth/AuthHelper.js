// AuthHelper.js

export const getJwtToken = () => {
    return localStorage.getItem("jwtToken");
};

export const isTokenValid = (token) => {
    if (!token) return false;

    try {
        const decoded = JSON.parse(atob(token.split('.')[1])); // Decode the JWT payload
        const currentTime = Math.floor(Date.now() / 1000); // Current timestamp in seconds
        return decoded.exp > currentTime; // Check if token is expired
    } catch (e) {
        console.error("Error decoding token", e);
        return false;
    }
};

export const checkAuth = (navigate) => {
    const token = getJwtToken();
    if (!token || !isTokenValid(token)) {
        navigate("/login"); // Redirect to login if token is invalid or expired
    }
};