import React from 'react';
import {ReviewItem} from "../reviewItem/ReviewItem";


export function ReviewList({ reviews, openReviewModal }) {
    return (
        <div className="review-wrapper">
            <div className="space-between-for-two-components">
                <h3>Reviews</h3>
                <i className="bi bi-plus-circle-fill" onClick={openReviewModal}></i>
            </div>
            {reviews.length === 0 ? <div className="mt-2 text-center">None</div> :
                reviews.map((review, index) => (
                <ReviewItem
                    key={index}
                    username={review.username}
                    date={review.date}
                    reviewText={review.reviewText}
                    rating={review.rating}
                />
            ))}
        </div>
    );
}