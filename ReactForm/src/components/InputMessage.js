import React from "react";

export const InputMessage = ({ form, handleChange }) => (
    <div className="input-field">
        <label className="labels">
            Message
        </label>
        <textarea
            name="message"
            placeholder="Enter your message"
            value={form.message}
            onChange={handleChange}
        />
    </div>
);
