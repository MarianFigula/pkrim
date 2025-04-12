import {PaymentDialog} from "../../components/paymentDialog/PaymentDialog";
import {useLocation} from "react-router-dom";
import axios from "axios";
import {useAuth} from "../../components/auth/AuthContext";
import {useState} from "react";

export function PaymentAccepted(){
    const location = useLocation();
    const { cartArtDetails, totalToPay } = location.state || {};
    const { token } = useAuth();
    const [error, setError] = useState(null);
    const serverUrl = process.env.REACT_APP_SERVER_URL


    const handleDownloadInvoice = async () => {
        setError(null);
        try {
            const response = await axios.get(`${serverUrl}/api/invoice/generate.php`, {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`
                },
                params: {
                    url: "http://localhost/public/arts/invoice-template.html",
                    arts: JSON.stringify(cartArtDetails),
                    total: totalToPay
                },
                responseType: "blob",
            });
            const contentType = response.headers["content-type"];
            if (contentType === "application/pdf") {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute("download", "invoice.pdf");
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
            } else {
                const text = await response.data.text();
                const errorResponse = JSON.parse(text);

                if (errorResponse.message) {
                    setError(errorResponse.message);
                } else {
                    setError("An unexpected error occurred. Please try again.");
                }
            }

        }catch (error) {
            if (error.response.headers["content-type"] === "application/json") {
                const text = await error.response.data.text();
                const errorResponse = JSON.parse(text);
                setError(errorResponse.message);
            }
            else {
                setError("Error downloading invoice, please try again later.");
            }
        }
    }

    return (
        <PaymentDialog
        textHeader="Payment successful"
        icon="bi-check2-circle color-green"
        textDescription="Thank you for your purchase!"
        buttonText="Continue shoppig"
        paymentStatus="accepted"
        handleDownloadInvoice={handleDownloadInvoice}
        error={error}
        />
    )
}