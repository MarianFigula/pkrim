import { useNavigate } from "react-router-dom";
import { Form } from "../../components/form/Form";
import { FormInput } from "../../components/formInput/FormInput";
import { Modal } from "../../components/modal/Modal";
import React, { useEffect, useState } from "react";
import { Table } from "../../components/table/Table";
import { getReviewColumns } from "../../assets/table-columns/tableReviewColumns";
import axios from "axios";
import {useAuth} from "../../components/auth/AuthContext";

export function UserReviewsSite() {

    const [error, setError] = useState("");
    const [isReviewModalOpen, setIsReviewModalOpen] = useState(false);
    const [reviewData, setReviewData] = useState([]);
    const [reviewRecords, setReviewRecords] = useState(reviewData);
    const [selectedReviewRows, setSelectedReviewRows] = useState([])

    const handleReviewChange = React.useCallback(state => {
        setSelectedReviewRows(state.selectedRows);
    }, []);

    const handleClearReviewRows = async () => {
        const confirmDelete = window.confirm("Are you sure you want to delete these reviews?");

        if (!confirmDelete) {
            return;
        }

        const reviewIds = selectedReviewRows.map(review => review.id);

        try {
            const response= await axios.get(`${serverUrl}/api/review/delete.php`, {
                params: {
                    action: "delete",
                    review_id: reviewIds,
                },
                paramsSerializer: (params) =>
                    new URLSearchParams(params).toString(), // Ensures proper serialization
            });

            const result = response.data

            result.success ? alert("Reviews deleted successfully") : alert(`Error deleting art: ${result.message}`)
            window.location.reload()
        } catch (error) {
            console.error("Error fetching user data:", error);
        }
    }

    const { token } = useAuth();

    const serverUrl = process.env.REACT_APP_SERVER_URL;

    const [reviewEditData, setReviewEditData] = useState({
        id: null,
        review_text: "",
        rating: "",
    });

    const fetchReviewData = async () => {
        try {
            const response = await axios.get(`${serverUrl}/api/review/read.php`, {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}` // Add token from context to headers
                },
            });

            const result = response.data;
            if (result.success){
                setReviewData(result.data);
                setReviewRecords(result.data);
            }else {
                alert("Error updating artwork")
            }
        } catch (error) {
            alert("Error updating artwork")
        }
    };

    useEffect(() => {
        fetchReviewData();
    }, []);

    const editReviewsHandler = (row) => {
        console.log(row);
        setReviewEditData({
            id: row.id,
            review_text: row.review_text,
            rating: row.rating,
        });
        setIsReviewModalOpen(true);
    };

    const columnsReviews = getReviewColumns(editReviewsHandler);

    const handleReviewFilter = (event) => {
        const eventValue = event.target.value;
        const newData = reviewData.filter((row) => {
            return (
                row.id.toString().toLowerCase().includes(eventValue) ||
                row.review_text
                    .toLowerCase()
                    .includes(eventValue.toLowerCase()) ||
                row.rating.toString().toLowerCase().includes(eventValue) ||
                row.review_creation_date
                    .toString()
                    .toLowerCase()
                    .includes(eventValue)
            );
        });
        setReviewRecords(newData);
    };

    const handleEditReviewSubmit = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.put(`${serverUrl}/api/review/update.php`,
                {
                    id: reviewEditData.id,
                    review_text: reviewEditData.review_text,
                    rating: reviewEditData.rating,
                },
                {
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`,
                    },
                }
            );

            const result = response.data
            if (result.success) {
                alert("Successfully updated review.");
                window.location.reload(); // Reload the page
            }else {
                alert("Failed to update review, please try again")
            }
        } catch (error) {
            setError(error);
            console.error("Error updating review:", error);
        }
    };

    return (
        <>
            <Modal
                isOpen={isReviewModalOpen}
                onClose={() => setIsReviewModalOpen(false)}
                title="Edit Review"
            >
                <Form
                    onSubmit={handleEditReviewSubmit}
                    error={error}
                    submitLabel="Apply changes"
                    buttonClassName="button-confirm"
                >
                    <FormInput
                        label="Review"
                        type="textarea"
                        rows="7"
                        value={reviewEditData.review_text}
                        onChange={(e) =>
                            setReviewEditData({
                                ...reviewEditData,
                                review_text: e.target.value,
                            })
                        }
                        required
                    />
                    <FormInput
                        label="Rating"
                        type="number"
                        max="5"
                        min="0"
                        value={reviewEditData.rating}
                        onChange={(e) => {
                            const value = e.target.value;
                            setReviewEditData({
                                ...reviewEditData,
                                rating: value === "" ? value : Number(value), // Allow empty string
                            });
                        }}
                        required
                    />
                </Form>
            </Modal>

            <h1 className="text-center mb-4 mt-10">Reviews</h1>
            <Table
                columns={columnsReviews}
                records={reviewRecords}
                handleFilter={handleReviewFilter}
                handleChange={handleReviewChange}
                refreshData={fetchReviewData}
                searchId="search-review-id"
                deleteHandler={handleClearReviewRows}
                selectedRows={selectedReviewRows}
            />
        </>
    );
}
