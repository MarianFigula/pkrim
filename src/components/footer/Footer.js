import "./Footer.css"

export function Footer(){

    return (
        <>
            <footer className="footer">
                <p className="text-center footer-text">
                    © Copyright {new Date().getFullYear()} PKRIM | Marián Figula, Ema Ševčíková
                </p>
            </footer>
        </>
    )
}
