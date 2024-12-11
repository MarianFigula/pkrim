import React, { useEffect, useState } from "react";
import { getArtColumns } from "../../assets/table-columns/tableArtColumns";
import { getReviewColumns } from "../../assets/table-columns/tableReviewColumns";
import { Table } from "../../components/table/Table";
import { Form } from "../../components/form/Form";
import { FormInput } from "../../components/formInput/FormInput";
import "./AdminEditUserSite.css";
import { Modal } from "../../components/modal/Modal";
import { useLocation, useParams } from "react-router-dom";
import axios from "axios";
import {useAuth} from "../../components/auth/AuthContext";

// admin page
// TODO ked zmenim id v url a aj ked tam na zaciatku nic neni
//  ale user s id funguje tak ho updatne, treba zo zmenit ci to nechame
//  ako naschval bug ?

// TODO: overit vstupy pri editoch (modaly)
export function AdminEditUserSite() {
    const { id } = useParams();
    const location = useLocation();
    const { username: initialUsername, email: initialEmail } =
        location.state || {};
    const serverUrl = process.env.REACT_APP_SERVER_URL;

    const [artData, setArtData] = useState([]);
    const [artRecords, setArtRecords] = useState(artData);

    const [reviewData, setReviewData] = useState([]);
    const [reviewRecords, setReviewRecords] = useState(reviewData);
    const [error, setError] = useState("");
    const [displayUsername, setDisplayUsername] = useState(initialUsername || "");
    const [username, setUsername] = useState(initialUsername || "");
    const [email, setEmail] = useState(initialEmail || "");
    const [isArtModalOpen, setIsArtModalOpen] = useState(false);
    const [isReviewModalOpen, setIsReviewModalOpen] = useState(false);

    const {token} = useAuth()

    const [artEditData, setArtEditData] = useState({
        id: null,
        title: "",
        description: "",
        price: 0,
    });

    const [reviewEditData, setReviewEditData] = useState({
        id: null,
        review_text: "",
        rating: "",
    });

    const fetchArtData = async () => {
        try {
            const response = await axios.get(`${serverUrl}/api/art/read.php`, {
                params: {
                    user_id: id,
                    admin_all: "Y"
                },
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`, // Add JWT token for authentication
                },
            });

            const result = response.data;
            console.log("result:", result)
            if (result.success) {
                setArtData(result.data); // Set fetched art data into state
                setArtRecords(result.data); // Optionally set into another state for records
            } else {
                alert("Error fetching art data:")
                console.error("Error: ", result);
            }
        } catch (error) {
            console.error("Error fetching art data:", error);
            // Handle errors gracefully
        }
    };

    const fetchReviewData = async () => {
        try {
            const response = await axios.get(
                `${serverUrl}/api/review/read.php`,
                {
                    params: {
                        user_id: id,
                        admin_all: "Y"
                    },
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`, // Include JWT for authentication
                    },
                }
            );

            const result = response.data;
            console.log("REVOEWS", result)
            if (result.success) {
                setReviewData(result.data); // Store the fetched reviews in state
                setReviewRecords(result.data); // Optionally store for records
            } else {
                alert("Error fetching review data")
                console.error("Error: ", result.message, result);
            }
        } catch (error) {
            console.error("Error fetching review data:", error);
            // Optionally handle errors in the UI, e.g., show an error message
        }
    };

    useEffect(() => {
        if (id) {
            window.scrollTo(0, 0);
            fetchUserData();
            fetchArtData();
            fetchReviewData();
        } else {
            setError("No id provided");
        }
    }, [id]);

    const editArtsHandler = (row) => {
        console.log(row);
        setArtEditData({
            id: row.id,
            title: row.title,
            description: row.description,
            price: Number(row.price),
        });
        setIsArtModalOpen(true);
    };

    const editReviewsHandler = (row) => {
        console.log(row);
        setReviewEditData({
            id: row.id,
            review_text: row.review_text,
            rating: row.rating,
        });
        setIsReviewModalOpen(true);
    };

    const columnsArts = getArtColumns(editArtsHandler);
    const columnsReviews = getReviewColumns(editReviewsHandler);

    const handleChange = ({ selectedRows }) => {
        console.log("Selected Rows: ", selectedRows);
    };

    const handleArtFilter = (event) => {
        const eventValue = event.target.value;
        const newData = artData.filter((row) => {
            return (
                row.id.toString().toLowerCase().includes(eventValue) ||
                row.img_url.toLowerCase().includes(eventValue.toLowerCase()) ||
                row.title.toLowerCase().includes(eventValue.toLowerCase()) ||
                row.description
                    .toLowerCase()
                    .includes(eventValue.toLowerCase()) ||
                row.price.toString().toLowerCase().includes(eventValue) ||
                row.upload_date.toString().toLowerCase().includes(eventValue)
            );
        });
        setArtRecords(newData);
    };

    const handleReviewFilter = (event) => {
        const eventValue = event.target.value.toLowerCase();
        const newData = reviewData.filter((row) => {
            return (
                row.id.toString().includes(eventValue) ||
                row.review_text.toLowerCase().includes(eventValue) ||
                row.rating.toString().toLowerCase().includes(eventValue) ||
                row.review_creation_date
                    .toString()
                    .toLowerCase()
                    .includes(eventValue)
            );
        });
        setReviewRecords(newData);
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        console.log("Submitting user edit");

        try {
            const response = await axios.put(
                `${serverUrl}/api/user/update.php`,
                {
                    id,
                    username,
                    email,
                },
                {
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`, // Add JWT token for authentication
                    },
                }
            );

            const result = response.data;
            console.log(result);

            if (result.success) {
                setUsername(result.data.username);
                setDisplayUsername(result.data.username);
                setEmail(result.data.email);
                alert("Updated successfully");
            }else {
                setError("An error occurred while updating the user.");
            }
        } catch (error) {
            setError("An error occurred while updating the user.");
            console.warn("Error updating user:", error);
        }
    };

    const fetchUserData = async () => {
        try {
            const response = await axios.get(
                `${serverUrl}/api/user/read.php`,
                {
                    params: { "email": email },
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`,
                    },
                }
            );
    
            const result = response.data;
    
            if (result.success) {
                setUsername(result.data.username);
                setDisplayUsername(result.data.username);
                setEmail(result.data.email);      
            } else {
                setError("Failed to fetch user data.");
            }
        } catch (error) {
            setError("Error fetching user data.");
            console.error("Fetch user data error:", error);
        }
    };

    const handleEditArtSubmit = async (e) => {
        e.preventDefault();

        try {
            const response = await axios.put(
                `${serverUrl}/api/art/update.php`,
                {
                    id: artEditData.id,
                    title: artEditData.title,
                    description: artEditData.description,
                    price: artEditData.price,
                },
                {
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`, // Add JWT token for authentication
                    },
                }
            );

            const result = response.data;

            if (result.success) {
                alert("Artwork updated successfully.");
                window.location.reload(); // Reload the page to reflect changes
            } else {
                console.error("Error:", result.message);
                setError("An error occurred while updating the artwork."
                );
            }
        } catch (error) {
            setError("An error occurred while updating the artwork."
            );
            console.warn("Error updating artwork:", error);
        }
    };

    const handleEditReviewSubmit = async (e) => {
        e.preventDefault();

        try {
            const response = await axios.put(
                `${serverUrl}/api/review/update.php`,
                {
                    id: reviewEditData.id,
                    review_text: reviewEditData.review_text,
                    rating: reviewEditData.rating,
                },
                {
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${token}`, // Add JWT token for authentication
                    },
                }
            );

            const result = response.data;

            if (result.success) {
                alert("Review updated successfully.");
                window.location.reload(); // Reload the page to reflect changes
            } else {
                setError("An error occurred while updating the review.");
                console.error("Error:", result.message);
            }
        } catch (error) {
            setError("An error occurred while updating the review.");
            console.warn("Error updating review:", error);
        }
    };
    return (
        <>
            <Modal
                isOpen={isArtModalOpen}
                onClose={() => setIsArtModalOpen(false)}
                title="Edit Art"
            >
                <Form
                    onSubmit={handleEditArtSubmit}
                    error={error}
                    submitLabel="Apply changes"
                    buttonClassName="button-confirm"
                >
                    <FormInput
                        label="Title"
                        type="text"
                        value={artEditData.title}
                        onChange={(e) =>
                            setArtEditData({
                                ...artEditData,
                                title: e.target.value,
                            })
                        }
                        required
                    />
                    <FormInput
                        label="Description"
                        type="textarea"
                        rows="7"
                        value={artEditData.description}
                        onChange={(e) =>
                            setArtEditData({
                                ...artEditData,
                                description: e.target.value,
                            })
                        }
                        required
                    />
                    <FormInput
                        label="Price (€)"
                        type="number"
                        value={artEditData.price}
                        onChange={(e) => {
                            const value = e.target.value;
                            setArtEditData({
                                ...artEditData,
                                price: value === "" ? value : Number(value),
                            })
                        }}
                        required
                    />
                </Form>
            </Modal>

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
                    {
                        //TODO osetrit min max
                        // hodnoty aj na serveri
                        // alebo to nechame ako feature bug
                        // ze user moze v html prepisat min a max
                        // a potom bude moct davat vyssi rating
                        // alebo tam miesto input type number pridame hviezdicky
                    }
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
                                rating: value === "" ? value: Number(value),
                            })
                        }}
                        required
                    />
                </Form>
            </Modal>
            <h1 className="text-center mb-2 mt-10">User - {displayUsername}</h1>
            <div className="edit-user-wrapper mb-4">
                <Form
                    onSubmit={handleSubmit}
                    error={error}
                    submitLabel="Edit"
                    buttonClassName="button-confirm"
                >
                    <FormInput
                        label="Username"
                        type="text"
                        value={username}
                        onChange={(e) => setUsername(e.target.value)}
                        required
                    />
                    <FormInput
                        label="Email"
                        type="email"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        required
                    />
                </Form>
            </div>
            <h1 className="text-center mb-1">Arts</h1>
            <Table
                columns={columnsArts}
                records={artRecords}
                handleFilter={handleArtFilter}
                handleChange={handleChange}
                refreshData={fetchArtData}
                searchId="search-art-id"
            />

            <h1 className="text-center mb-1">Reviews</h1>
            <Table
                columns={columnsReviews}
                records={reviewRecords}
                handleFilter={handleReviewFilter}
                handleChange={handleChange}
                refreshData={fetchReviewData}
                searchId="search-review-id"
            />
        </>
    );
}
