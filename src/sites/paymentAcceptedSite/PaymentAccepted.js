import {PaymentDialog} from "../../components/paymentDialog/PaymentDialog";

export function PaymentAccepted(){
    return (
        <PaymentDialog
        textHeader="Payment successful"
        icon="bi-check2-circle color-green"
        textDescription="Thank you for your purchase!"
        buttonText="Continue shoppig"
        />
    )
}