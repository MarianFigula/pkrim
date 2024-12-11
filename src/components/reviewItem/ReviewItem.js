import UserIcon  from  "../../assets/user-pictures/22.png"

export function ReviewItem({ username, date, reviewText, rating }) {
    const totalStars = 5; // Always display 5 stars

    return (
        <div className="review mb-2">
            <div className="reviewer">
                <img src={UserIcon} alt="user-img" />
                <p className="reviewer-name">{username}</p>
                <p className="review-date">{date}</p>
            </div>
            <p className="stars">
                {/* Create 5 stars, and fill them based on the stars prop */}
                {Array.from({ length: totalStars }, (_, i) => (
                    <i
                        key={i}
                        className={i < rating ? "bi bi-star-fill" : "bi bi-star"}
                        style={i < rating ? { color: 'var(--yellow)' } : {}}
                    ></i>
                ))}
            </p>
            <p>{reviewText}</p>
        </div>
    );
}