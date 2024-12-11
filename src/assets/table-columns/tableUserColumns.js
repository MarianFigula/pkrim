// src/components/tableUserColumns.js

export const getUserColumns = (editHandler) => [
    {
        name: "Id",
        selector: row => row.id,
        sortable: true,
    },
    {
        name: "Username",
        selector: row => row.username,
        sortable: true,
    },
    {
        name: 'Email',
        selector: row => row.email,
        sortable: true,
    },
    {
        name: 'Security Question',
        selector: row => row.security_question,
        sortable: true
    },
    {
        name: "Security Answer",
        selector: row => row.security_answer,
        sortable: true
    },
    {
        name: "Edit",
        cell: (row) => <button className="button-edit" onClick={() => editHandler(row)}>
            <i className="bi bi-pencil-square"></i>
        </button>
    }
];