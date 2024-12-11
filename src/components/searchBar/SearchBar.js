import React from "react";


export function SearchBar({searchId, handleFilter, placeholder = "Search", style }) {

    return (
        <>
            <label htmlFor={searchId} className="label">
                <input type="search"
                       id={searchId}
                       className="input border-15"
                       onChange={handleFilter}
                       placeholder={placeholder}
                       style={style}
                />
                <i className="bi bi-search search-icon"></i>
            </label>
        </>
    )
}

export default SearchBar;