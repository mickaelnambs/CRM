import React from 'react';

const Select = ({name, value, label, error = "", onChange, children}) => {
    return (  
        <div className="form-group">
            <label htmlFor={name}>{label}</label>
            <select
                className={"form-control" + (error && " is-invalid")}
                onChange={onChange}
                name={name}
                id={name}
                value={value}
            >
                {children}
            </select>
            <p className="invalid-feedback">{error}</p>
        </div>
    );
}
 
export default Select;