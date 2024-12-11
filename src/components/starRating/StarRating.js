import React, {useState} from "react";

export function StarRating({rating, setRating}) {
    const totalStars = 5;
    const [hoverRating, setHoverRating] = useState(0);

    const handleStarClick = (index) => {
        setRating(index + 1);
    };

    const handleStarMouseEnter = (index) => {
        setHoverRating(index + 1);
    };

    const handleStarMouseLeave = () => {
        setHoverRating(0);
    };

    return (
        <div>
            <label className="label mb-0-25">Rating</label>
            <div className="star-rating">
                {Array.from({length: totalStars}, (_, i) => (
                    <i
                        key={i}
                        className={i < (hoverRating || rating) ? "bi bi-star-fill" : "bi bi-star"}
                        style={{cursor: "pointer", color: i < (hoverRating || rating) ? 'var(--yellow)' : ''}}
                        onClick={() => handleStarClick(i)}
                        onMouseEnter={() => handleStarMouseEnter(i)}
                        onMouseLeave={handleStarMouseLeave}
                    ></i>
                ))}
            </div>
        </div>
    );
}
