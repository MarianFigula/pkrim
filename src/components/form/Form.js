import React from "react";

export function Form({
                         onSubmit,
                         error,
                         children,
                         submitLabel = "Submit",
                         buttonClassName
                     }) {

    return (
        <form onSubmit={onSubmit}>
            {children}
            {error && <p style={{color: 'red'}} className="mt-0">{error}</p>}
            <button type="submit" className={buttonClassName}>
                {submitLabel}
            </button>


        </form>

    )

}