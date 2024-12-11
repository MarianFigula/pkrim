// src/components/tableUserColumns.js

export const getReviewColumns = (editHandler) => [
    {
        name: "Id",
        selector: row => row.id,
        sortable: true,
    },
    {
        name: "Review Text",
        selector: row => row.review_text,
        sortable: true,
    },
    {
        name: 'Rating',
        selector: row => row.rating,
        sortable: true,
    },
    {
        name: 'Date',
        selector: row => row.review_creation_date,
        sortable: true
    },
    {
        name: "Edit",
        cell: (row) => <button className="button-edit" onClick={() => editHandler(row)}>
            <i className="bi bi-pencil-square"></i>
        </button>
    }
];