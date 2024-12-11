import React from "react";

export function FormInput({
                              label,
                              type = "text",
                              value,
                              onChange,
                              options = [],
                              required = false,
                              ...props
                          }) {


    const id = `form-input-${label}`;

    return (
        <div>
            <label htmlFor={id} className="label mb-0-25">
                {label}
            </label>
            {type === 'select' ? (
                <select
                    id={id}
                    value={value}
                    onChange={onChange}
                    className="input"
                    required={required}
                    {...props}
                >
                    {options.map((option, index) => (
                        <option
                            key={index}
                            value={index === 0 ? "" : option}>
                            {option}
                        </option>
                    ))}
                </select>
            ) : type === "textarea" ? (
                <textarea
                    id={id}
                    value={value}
                    onChange={onChange}
                    className="input"
                    required={required}
                    {...props}
                />
            ) : (
                <input
                    id={id}
                    type={type}
                    value={value}
                    onChange={onChange}
                    className="input"
                    required={required}
                    {...props}
                />
            )}
        </div>
    );
}
