import "./CartSite.css"
import CartItem from "../../components/CartItem/CartItem";

import Camera from "../../assets/user-pictures/camera.png"
import Camera2 from "../../assets/user-pictures/camera-2.png"
import {Modal} from "../../components/modal/Modal";
import {ArtImage} from "../../components/artImage/artImage";
import {useEffect, useState} from "react";
import {useCart} from "../../components/cartProvider/CartProvider";
import {redirect, useNavigate} from "react-router-dom";

export default function CartSite() {
    const { cartArtDetails, removeFromCart } = useCart();
    const [isCartModalOpen, setIsCartModalOpen] = useState(false)
    const [totalToPay, setTotalToPay ] = useState(0)
    const [selectedArt, setSelectedArt] = useState(null);

    const navigate = useNavigate()

    const serverUrl = process.env.REACT_APP_SERVER_URL
    const phpBaseUrl = `${serverUrl}/public`;

    useEffect(() => {
        const total = cartArtDetails.reduce((sum, art) => {
            return parseInt(sum) + (parseInt(art.price) || 0);
        }, 0);
        setTotalToPay(total);
    }, [cartArtDetails]);

    function showArtModal(art) {
        setSelectedArt(art);
        setIsCartModalOpen(true);
    }

    return (
        <>
            {(selectedArt) && (
                <Modal
                    isOpen={isCartModalOpen}
                    title={selectedArt.title}
                    onClose={() => setIsCartModalOpen(false)}
                >
                    <div className="cart-modal-img">
                        <ArtImage imgUrl={`${phpBaseUrl}${selectedArt.img_url}`} />
                    </div>
                    <div className="space-between-for-two-components">
                        <p>by {selectedArt.author_name}</p>
                        <p className="price">{selectedArt.price}€</p>
                    </div>
                </Modal>
            )}


            <div className="cart-payment-wrapper">
                <div className="cart-payment-header">
                    <h1>Shopping Cart</h1>
                    <p className="mb-3">Your Items</p>
                </div>

                {cartArtDetails.length > 0 ? (
                    <section className="cart-items">
                        {cartArtDetails.map((art, index) => (
                            <CartItem
                                key={index}
                                artTitle={art.title}
                                imgUrl={`${phpBaseUrl}${art.img_url}`}
                                authorName={art.author_name}
                                price={art.price}
                                onClickDisplayImage={() => showArtModal(art)}
                                onClickDeleteArtFromCart={() => removeFromCart(art.art_id)}
                            />
                        ))}
                    </section>
                ) : (
                    <h1 className="text-center">Your shopping cart is empty</h1>
                )}
            </div>

            {cartArtDetails.length > 0 && (
                <section className="order-summary">
                    <div>
                        <button className="button-light" onClick={() => navigate("/")}>Continue shopping</button>
                    </div>
                    <h3 className="mb-0 mt-0">
                        Total: <span className="price">{totalToPay}€</span>
                    </h3>
                    <button className="button-confirm" onClick={() => navigate("/payment", { state: { totalToPay } })}
                    >Continue</button>
                </section>
            )}
        </>
    );
}