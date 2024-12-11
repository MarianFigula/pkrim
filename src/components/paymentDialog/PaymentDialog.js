import "./PaymentDialog.css"
import {useNavigate} from "react-router-dom";

export function PaymentDialog({textHeader, icon, textDescription, buttonText}){

    const navigate = useNavigate()
    return(
        <div className="login-container text-center">
            <h2>{textHeader}</h2>
            <i className={"bi " + icon}></i>
            <p>{textDescription}</p>
            <button onClick={() => navigate("/")} className="button-dark mb-1">{buttonText}</button>
        </div>
    )
}