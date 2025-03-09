import "./PaymentDialog.css"
import {useNavigate} from "react-router-dom";

export function PaymentDialog({textHeader, icon, textDescription, buttonText, paymentStatus, handleDownloadInvoice, error}){

    const navigate = useNavigate()
    return(
        <div className="login-container text-center">
            <h2>{textHeader}</h2>
            <i className={"bi " + icon}></i>
            <p>{textDescription}</p>
            <div className="buttons" style={{flexWrap: "wrap"}}>
                {paymentStatus === "accepted" && (
                    <button onClick={handleDownloadInvoice} className="button-confirm mb-1">
                        Download Invoice
                    </button>
                )}
                <button onClick={() => navigate("/")} className="button-dark mb-1 mt-0">{buttonText}</button>

            </div>
            {error && <p style={{color: 'red'}} className="error">{error}</p>}
        </div>
    )
}