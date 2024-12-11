import { Form } from "../../components/form/Form";
import { FormInput } from "../../components/formInput/FormInput";
import { useLocation } from "react-router-dom";
import { useNavigate } from "react-router-dom";
import React, { useState } from "react";
import axios from "axios";
import {useAuth} from "../../components/auth/AuthContext";

export function CreateArtSite() {
    const [error, setError] = useState("");
    const [title, setTitle] = useState("");
    const [description, setDescription] = useState("");
    const [price, setPrice] = useState(0);
    const [file, setFile] = useState(null); // Update state for file
    const serverUrl = process.env.REACT_APP_SERVER_URL;

    const navigate = useNavigate();

    const { token } = useAuth(); // Access the token directly from the context

    const uploadArt = async (event) => {
        event.preventDefault();
        setError("");

        if (title === "" || description === "" || !file){
            setError("All fields are required");
            return
        }

        if (price <= 0){
            setError("Price should be more than 0");
            return
        }

        try {
            const formData = new FormData();
            formData.append("title", title);
            formData.append("description", description);
            formData.append("price", price);
            formData.append("file", file); // Append the file itself, not just the name

            const response = await axios.post(
                `${serverUrl}/api/art/create.php`,
                formData,
                {
                    headers: {
                        "Content-Type": "multipart/form-data",
                        Authorization: `Bearer ${token}`, // Add JWT token
                    },
                }
            );

            const data = response.data;
            console.log(data);

            if (data.success) {
                alert("Art Successfully Created");
                navigate("/");
            }else {
                setError(data.message)
            }
        } catch (error) {
            alert("Failed to create art");
            console.error("Error creating art: ", error);
        }
    };

    return (
        <div className="login-container">
            <h2>Upload Art</h2>
            <Form
                onSubmit={uploadArt}
                error={error}
                submitLabel="Upload Art"
                buttonClassName="button-dark mb-1"
            >
                <FormInput
                    label="Title"
                    type="text"
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    required
                />
                <FormInput
                    label="Description"
                    type="textarea"
                    value={description}
                    onChange={(e) => setDescription(e.target.value)}
                    required
                />
                <FormInput
                    label="Price €"
                    type="number"
                    value={price}
                    onChange={(e) => setPrice(e.target.value)}
                    required
                />
                <FormInput
                    label="File"
                    type="file"
                    name="file"
                    id="file"
                    onChange={(e) => setFile(e.target.files[0])} // Update file state
                    required
                />
            </Form>
        </div>
    );
}
