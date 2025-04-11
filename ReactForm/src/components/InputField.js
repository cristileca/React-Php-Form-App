import React from "react";

export const InputField = ({ label, placeholder, type = "text", name, value, onChange, required = false }) => (
    <div className="input-field">
      <label className="labels">{label}</label>
      <input
        type={type}
        name={name}
        placeholder={placeholder}
        value={value}
        onChange={onChange}
        required={required}
      />
    </div>
);

