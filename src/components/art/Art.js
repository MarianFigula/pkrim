import { ArtImage } from "../artImage/artImage";
import { Modal } from "../modal/Modal";
import { useEffect, useState } from "react";
import axios from "axios";
import { useCart } from "../cartProvider/CartProvider";

export function Art({ art }) {
    const { cartArtIds, incrementCartCount } = useCart();
    const [isArtImageModalOpen, setIsArtImageModalOpen] = useState(false);
    const [isAddedToCart, setIsAddedToCart] = useState(
        cartArtIds.includes(art.art_id)
    ); // Initialize based on cart state

    useEffect(() => {
        setIsAddedToCart(cartArtIds.includes(art.art_id)); // Update state when cartArtIds change
    }, [cartArtIds, art.art_id]);

    async function handleAddToCartClick() {
        const serverUrl = process.env.REACT_APP_SERVER_URL;
        const token = localStorage.getItem("jwtToken");

        try {
            const response = await axios.post(
                `${serverUrl}/api/cartArt/create.php`,
                {
                    art_id: art.art_id,
                },
                {
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`,
                    },
                }
            );

            const result = response.data;
            if (result.success) {
                incrementCartCount();
                setIsAddedToCart(true); // Disable the button and mark as added
            }else {
                alert("An error occurred while adding art to cart.");
            }
        } catch (error) {
            console.error("Error adding art to cart");
            alert("An error occurred while adding art to cart.");
        }
    }

    return (
        <>
            <Modal
                isOpen={isArtImageModalOpen}
                title={art.title}
                onClose={() => setIsArtImageModalOpen(false)}
            >
                <div className="cart-modal-img">
                    <ArtImage imgUrl={art.img_url}></ArtImage>
                </div>
                <div className="space-between-for-two-components">
                    <p>{art.username}</p>
                    <p className="price">{art.price}€</p>
                </div>
            </Modal>

            <div className="art-wrapper">
                <div
                    className="img-wrapper"
                    onClick={() => setIsArtImageModalOpen(true)}
                >
                    <ArtImage imgUrl={art.img_url} />
                </div>
                <div className="img-info">
                    <div className="space-between-for-two-components">
                        <h3>{art.title}</h3>
                        <p>{art.username}</p>
                    </div>
                    <p className="art-description">{art.description}</p>
                </div>
                <div className="space-between-for-two-components">
                    <p className="price">{art.price}€</p>
                    {isAddedToCart ? (
                        <button className="button-disabled" disabled>
                            <i className="bi bi-check-circle"></i>
                            Added
                        </button>
                    ) : (
                        <button
                            className="button-add-to-cart"
                            onClick={handleAddToCartClick}
                        >
                            <i className="bi bi-cart"></i>
                            Add to cart
                        </button>
                    )}
                </div>
            </div>
        </>
    );
}
