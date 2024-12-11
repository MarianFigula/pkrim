import {PaymentDialog} from "../../components/paymentDialog/PaymentDialog";

export function PaymentDenied(){
    return (
        <PaymentDialog
        textHeader="Payment failed"
        icon="bi-x-circle color-danger"
        textDescription="Sorry, we couldn’t process your payment."
        buttonText="Back to shopping"
        />
    )
}