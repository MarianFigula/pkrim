import React, {useEffect, useState} from "react";
import "./UserProfile.css"
import "../../components/form/form.css"
import UserPhoto from "../../assets/user-pictures/22.png"
import AdminPhoto from "../../assets/user-pictures/21.png"
import {useNavigate} from "react-router-dom";
import {useAuth} from "../../components/auth/AuthContext";
import axios from "axios";

export function UserProfileSite() {

    const navigate = useNavigate();
    const [userData, setUserData] = useState(null); // Store user profile data
    const [error, setError] = useState(null); // Handle errors

    const { token } = useAuth();

    const serverUrl = process.env.REACT_APP_SERVER_URL
    // Fetch user profile data
    const fetchUserData = async () => {
        try {
            const response = await axios.get(`${serverUrl}/api/user/read.php`, {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}` // Add token from context to headers
                },
            });

            const data = await response.data
            if (data.success) {
                console.log(data.data)
                setUserData(data.data); // Set user data
                console.log(userData)
            }
        } catch (err) {
            setError("Failed to fetch user profile data.");
            console.error(err);
        }
    };

    useEffect(() => {
        fetchUserData();
    }, []);

    // Log changes to userData
    useEffect(() => {
        if (userData) {
            console.log("Updated userData:", userData);
        }
    }, [userData]);

    const handleMyPostsClick = () => {
        navigate(`/my-arts`);
    };

    const handleReviewHistoryClick = () => {
        navigate(`/review-history`);
    };
    return (
        <>
            <div className="profile-wrapper mb-5 mt-7">
                <div className="profile-picture">
                    <img src={UserPhoto} alt="ProfilePicture"/>
                </div>
                <div className="profile-details mb-1">
                    <div className="">
                        <h2 className="text-center">Your profile</h2>
                        {userData ? ( // Only render form when userData is not null
                            <form className="mb-3">
                                <div className="info">
                                    <label className="label mb-0-25">Username</label>
                                    <input
                                        type="text"
                                        defaultValue={userData.username} // Use fetched data
                                        className="input"
                                        readOnly
                                    />
                                </div>
                                <div className="info mb-1">
                                    <label className="label mb-0-25">Email</label>
                                    <input
                                        type="text"
                                        defaultValue={userData.email} // Use fetched data
                                        className="input"
                                        readOnly
                                    />
                                </div>
                            </form>
                        ) : (
                            <p>Loading profile...</p>
                        )}
                        <div className="buttons">
                            {/*<button className="button-dark">order history</button>*/}
                            <button
                                className="button-dark"
                                onClick={handleMyPostsClick}
                            >
                                My posts
                            </button>
                            <button
                                className="button-dark"
                                onClick={handleReviewHistoryClick}
                            >
                                Review history
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </>
    )
}