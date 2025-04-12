export const getJwtToken = () => {
    return localStorage.getItem("jwtToken");
};

export const isTokenValid = (token) => {
    if (!token) return false;

    try {
        const decoded = JSON.parse(atob(token.split('.')[1]));
        const currentTime = Math.floor(Date.now() / 1000);
        return decoded.exp > currentTime;
    } catch (e) {
        return false;
    }
};

export const checkAuth = (navigate) => {
    const token = getJwtToken();
    if (!token || !isTokenValid(token)) {
        navigate("/login");
    }
};