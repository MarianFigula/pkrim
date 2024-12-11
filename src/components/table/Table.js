import React from "react";
import DataTable from "react-data-table-component";
import {SearchBar} from "../searchBar/SearchBar";


export function Table({
                          columns,
                          records,
                          handleFilter,
                          handleChange,
                          refreshData,
                          searchId
                      }) {

    return (
        <div className='table-wrapper'>
            <div className="search-wrapper mb-1">
                <SearchBar searchId={searchId}
                           handleFilter={handleFilter}
                />
                <button
                    onClick={refreshData}
                    className="button-refresh">

                    <i className="bi bi-arrow-repeat"></i>
                </button>
            </div>

            <DataTable
                columns={columns}
                data={records}
                className="table mb-2"
                selectableRows
                onSelectedRowsChange={handleChange}
                pagination
                persistTableHead
            />
        </div>
    )
}