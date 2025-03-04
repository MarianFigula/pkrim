import React, {useEffect, useState} from "react";
import {getArtColumns} from "../../assets/table-columns/tableArtColumns";
import {Form} from "../../components/form/Form";
import {FormInput} from "../../components/formInput/FormInput";
import {Modal} from "../../components/modal/Modal";
import {Table} from "../../components/table/Table";
import axios from "axios";
import {useAuth} from "../../components/auth/AuthContext";

export function UserArtsSite() {
    const serverUrl = process.env.REACT_APP_SERVER_URL;

    const [userArtData, setUserArtData] = useState([]);
    const [userArtRecords, setUserArtRecords] = useState(userArtData);
    const [error, setError] = useState("");
    const {token} = useAuth();
    const [isArtModalOpen, setIsArtModalOpen] = useState(false);
    const [selectedArtRows, setSelectedArtRows] = useState([])

    const [artEditData, setArtEditData] = useState({
        id: null,
        title: "",
        description: "",
        price: 0,
    });

    const handleArtChange = React.useCallback(state => {
        setSelectedArtRows(state.selectedRows);
    }, []);

    const handleClearArtRows = async () => {
        const confirmDelete = window.confirm("Are you sure you want to delete these arts?");

        if (!confirmDelete) {
            return;
        }

        const artIds = selectedArtRows.map(art => art.id); // Extract all art_id values
        try {
            const response= await axios.get(`${serverUrl}/api/art/delete.php`, {
                params: {
                    action: "delete",
                    art_id: artIds,
                },
                paramsSerializer: (params) =>
                    new URLSearchParams(params).toString(), // Ensures proper serialization
            });

            const result = response.data

            result.success ? alert("Arts deleted successfully") : alert(`Error deleting art: ${result.message}`)
            window.location.reload()
        } catch (error) {
            console.error("Error fetching user data:", error);
        }
    };

    // NEW WAY
    const fetchArtData = async () => {
        try {
            const response = await axios.get(`${serverUrl}/api/art/read.php`, {
                headers: {
                    "Content-Type": "application/json",
                    Authorization: `Bearer ${token}`, // Include JWT for authentication
                }
            });

            const result = response.data;
            console.log(result);
            if (result.success) {
                setUserArtData(result.data);
                setUserArtRecords(result.data);
            }else {
                alert("Error fetching art data")
            }
        }  catch (error) {
            alert("Error fetching art data")
        }
    };

    useEffect(() => {
        fetchArtData();
    }, []);

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

    const columnsArts = getArtColumns(editArtsHandler);

    const handleArtFilter = (event) => {
        const eventValue = event.target.value.toLowerCase();
        const newData = userArtData.filter((row) => {
            return (
                row.id.toString().toLowerCase().includes(eventValue) ||
                row.img_url.toLowerCase().includes(eventValue) ||
                row.title.toLowerCase().includes(eventValue) ||
                row.description.toLowerCase().includes(eventValue) ||
                row.price.toString().toLowerCase().includes(eventValue) ||
                row.upload_date.toString().toLowerCase().includes(eventValue)
            );
        });
        console.log("new data:", newData)
        setUserArtRecords(newData);
    };

    const handleEditArtSubmit = async () => {
        console.log(artEditData);
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
                        Authorization: `Bearer ${token}`, // Add JWT for authentication
                    },
                }
            );

            const result = response.data;
            if (result.success) {
                alert("Artwork successfully updated.");
                window.location.reload();
            }else {
                alert("Error updating artwork")
            }
        } catch (error) {
            alert("Error updating artwork")
            console.warn("Error updating artwork:", error);
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
            <h1 className="text-center mb-4 mt-10">Arts</h1>
            <Table
                columns={columnsArts}
                records={userArtRecords}
                handleFilter={handleArtFilter}
                handleChange={handleArtChange}
                refreshData={fetchArtData}
                searchId="search-art-id"
                deleteHandler={handleClearArtRows}
                selectedRows={selectedArtRows}
            />
        </>
    );
}
