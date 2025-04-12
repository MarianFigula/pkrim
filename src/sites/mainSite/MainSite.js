import { SearchBar } from "../../components/searchBar/SearchBar";
import "./MainSite.css";
import "../../table.css";
import { ReviewList } from "../../components/reviewList/ReviewList";
import { Art } from "../../components/art/Art";
import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { Modal } from "../../components/modal/Modal";
import { Form } from "../../components/form/Form";
import { FormInput } from "../../components/formInput/FormInput";
import { StarRating } from "../../components/starRating/StarRating";
import axios from "axios";
import { useCart } from "../../components/cartProvider/CartProvider";
import {getJwtToken} from "../../components/auth/AuthHelper";
import {useAuth} from "../../components/auth/AuthContext";

export function MainSite() {
    const { cartArtIds } = useCart();
    const { token } = useAuth();


    const [initialArtData, setInitialArtData] = useState([]);
    const [arts, setArts] = useState([]);
    const [activeButton, setActiveButton] = useState(null);
    const [isOriginal, setIsOriginal] = useState(true);
    const [error, setError] = useState("");

    const [isArtModalOpen, setIsArtModalOpen] = useState(false);
    const [reviewText, setReviewText] = useState("");
    const [reviewRating, setReviewRating] = useState(0);

    const [selectedArtId, setSelectedArtId] = useState(null);

    const navigate = useNavigate();

    const serverUrl = process.env.REACT_APP_SERVER_URL;

    const [query, setQuery] = useState("")
    useEffect(() => {
        // eslint-disable-next-line no-restricted-globals
        const searchParams = new URLSearchParams(location.search);
        const q = searchParams.get('q') || '';
        setQuery(q);
        // eslint-disable-next-line no-restricted-globals
    }, [location]);
    const fetchData = async () => {
        try {
            const response = await axios.get(`${serverUrl}/api/art/read.php`, {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`
                },
                params: {
                    all: "Y",
                },
            });

            const result = response.data;

            if (result.success) {
                const artDataMap = [];

                const phpBaseUrl = `${serverUrl}/public`;

                result.data.forEach((item) => {
                    const artId = item.art_id;
                    const imageUrl = `${phpBaseUrl}${item.img_url}`;

                    if (artDataMap[artId]) {
                        artDataMap[artId].reviews.push({
                            username: item.review_user_username,
                            date: item.review_creation_date,
                            reviewText: item.review_text,
                            rating: parseInt(item.rating, 10),
                        });
                    } else {
                        artDataMap[artId] = {
                            art_id: artId,
                            username: item.art_creator_username,
                            img_url: imageUrl,
                            title: item.title,
                            description: item.description,
                            price: parseFloat(item.price),
                            date: item.upload_date,
                            reviews: item.review_user_username
                                ? [
                                      {
                                          username: item.review_user_username,
                                          date: item.review_creation_date,
                                          reviewText: item.review_text,
                                          rating: parseInt(item.rating, 10),
                                      },
                                  ]
                                : [],
                        };
                    }
                });

                const artData = Object.values(artDataMap).map((art) => {
                    const totalRating = art.reviews.reduce(
                        (acc, review) => acc + review.rating,
                        0
                    );
                    const averageRating =
                        art.reviews.length > 0
                            ? totalRating / art.reviews.length
                            : 0;
                    return {
                        ...art,
                        averageRating,
                    };
                });

                const sortedArtData = artData.sort(
                    (a, b) => b.averageRating - a.averageRating
                );
                setArts(sortedArtData);
                setInitialArtData(sortedArtData);

                setArts(artData);
                setInitialArtData(artData);
            }else {
                setError(result.message)
            }
        } catch (error) {
            setError("Oops, something wrong happened")
            console.error("Error fetching art and reviews data: ", error);
        }
    };

    useEffect(() => {
        fetchData();
    }, [token]);

    const resetToOriginal = () => {
        setArts(initialArtData);
        setActiveButton(null);
        setIsOriginal(true);
    };

    const toggleSortByPriceAsc = () => {
        if (activeButton === "priceAsc") {
            resetToOriginal();
            return;
        }
        const sortedArts = [...arts].sort((a, b) => a.price - b.price);
        setArts(sortedArts);
        setActiveButton("priceAsc");
        setIsOriginal(false);
    };

    const toggleSortByPriceDesc = () => {
        if (activeButton === "priceDesc") {
            resetToOriginal();
            return;
        }
        const sortedArts = [...arts].sort((a, b) => b.price - a.price);
        setArts(sortedArts);
        setActiveButton("priceDesc");
        setIsOriginal(false);
    };

    const toggleSortByRatingAsc = () => {
        if (activeButton === "ratingAsc") {
            resetToOriginal();
            return;
        }
        const sortedArts = [...arts].sort(
            (a, b) => a.averageRating - b.averageRating
        );
        setArts(sortedArts);
        setActiveButton("ratingAsc");
        setIsOriginal(false);
    };

    const toggleSortByRatingDesc = () => {
        if (activeButton === "ratingDesc") {
            resetToOriginal();
            return;
        }
        const sortedArts = [...arts].sort(
            (a, b) => b.averageRating - a.averageRating
        );
        setArts(sortedArts);
        setActiveButton("ratingDesc");
        setIsOriginal(false);
    };

    const handleFilter = (event) => {
        const searchValue = event.target.value.toLowerCase();

        const newData = initialArtData.filter((row) => {
            return row.title.toLowerCase().includes(searchValue);
        });
        setArts(newData);
    };

    const redirectToUploadArt = () => {
        token !== null || token === ""
            ? navigate("upload-art")
            : navigate("/login");
    };

    const openReviewModal = (artId) => {
        setSelectedArtId(artId);
        setIsArtModalOpen(true);
    };
    const handleReviewSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.post(
                `${serverUrl}/api/review/create.php`,
                {
                    art_id: selectedArtId,
                    review_text: reviewText,
                    rating: reviewRating,
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
                alert("Review added successfully.")
                setIsArtModalOpen(false);
                window.location.reload();
            }else {
                setError(result.message)
            }
        } catch (error) {
            setError("Something failed")
            console.error("Error submitting review:", error);
        }
    };

    function resetReview(){
        setError("")
        setReviewText("")
        setReviewRating(0)
    }
    return (
        <>
            <div dangerouslySetInnerHTML={{__html: query}}></div>
            <Modal
                isOpen={isArtModalOpen}
                onClose={() => {
                    setIsArtModalOpen(false);
                    resetReview()
                }}
                title="Add Review"
            >
                <Form
                    error={error}
                    buttonClassName="button-dark"
                    onSubmit={handleReviewSubmit}
                    submitLabel="Add review"
                >
                    <FormInput type="hidden" value={selectedArtId} />

                    <FormInput
                        label="Review Text"
                        type="text"
                        value={reviewText}
                        onChange={(e) => {
                            setReviewText(e.target.value);
                        }}
                        required
                    />
                    <StarRating
                        rating={reviewRating}
                        setRating={setReviewRating}
                    />
                </Form>
            </Modal>
            <div className="main-content">
                <h1 className="text-center">Discover new Arts</h1>
                <section className="main-header-wrapper">
                    <SearchBar
                        searchId="main-searchbar"
                        handleFilter={handleFilter}
                        placeholder="Search for art..."
                        style={{paddingLeft: "40px"}}
                    />
                    <div className="button-wrapper">
                        <button
                            onClick={toggleSortByPriceAsc}
                            className={
                                activeButton === "priceAsc" ? "active" : ""
                            }
                        >
                            Price <i className="bi bi-arrow-up"></i>
                        </button>
                        <button
                            onClick={toggleSortByPriceDesc}
                            className={
                                activeButton === "priceDesc" ? "active" : ""
                            }
                        >
                            Price <i className="bi bi-arrow-down"></i>
                        </button>
                        <button
                            onClick={toggleSortByRatingAsc}
                            className={
                                activeButton === "ratingAsc" ? "active" : ""
                            }
                        >
                            Rating <i className="bi bi-arrow-up"></i>
                        </button>
                        <button
                            onClick={toggleSortByRatingDesc}
                            className={
                                activeButton === "ratingDesc" ? "active" : ""
                            }
                        >
                            Rating <i className="bi bi-arrow-down"></i>
                        </button>
                    </div>
                </section>

                {arts.map((art, index) => (
                    <section className="art-review-wrapper mb-3" key={index}>
                        <Art art={art} />
                        <ReviewList
                            reviews={art.reviews}
                            openReviewModal={() => openReviewModal(art.art_id)}
                        />
                    </section>
                ))}

                <button
                    className="create-art button-confirm"
                    onClick={redirectToUploadArt}
                >
                    Upload Art
                    <i className="bi bi-plus"></i>
                </button>
            </div>
        </>
    );
}
