import CryptoJS from "crypto-js";

export const hashString = (string) => {
    return CryptoJS.MD5(string).toString();
};