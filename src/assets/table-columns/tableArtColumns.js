// src/components/tableUserColumns.js

export const getArtColumns = (editHandler) => [
    {
        name: "Id",
        selector: row => row.id,
        sortable: true,
    },
    {
        name: "Img URL",
        selector: row => row.img_url,
        sortable: true,
    },
    {
        name: "Title",
        selector: row => row.title,
        sortable: true,
    },
    {
        name: 'Description',
        selector: row => row.description,
        sortable: true,
    },
    {
        name: 'Price (€)',
        selector: row => row.price,
        sortable: true
    },
    {
        name: "Date",
        selector: row => row.upload_date,
        sortable: true
    },
    {
        name: "Edit",
        cell: (row) => <button className="button-edit" onClick={() => editHandler(row)}>
            <i className="bi bi-pencil-square"></i>
        </button>
    }
];