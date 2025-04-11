import React, { useState } from "react";
import "./Form.css";
import { InputField } from "./InputField";
import { InputMessage } from "./InputMessage";
import Success from "./Success";

const Form = () => {
  const [form, setForm] = useState({
    name: "",
    email: "",
    message: "",
    consent: false,
    image: null,
  });
  const [err, setErr] = useState(false);
  const [success, setSuccess] = useState("none");
  const [formDisplay, setFormDisplay] = useState("true");
  const [errDuplicate, setErrDuplicate]= useState(false);
  const [genErr, setGenError] = useState(false);

  const handleChange = (e) => {
    const { name, value, type, checked, files } = e.target;
    setForm((prev) => ({
      ...prev,
      [name]: type === "checkbox" ? checked : type === "file" ? files[0] : value,
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErrDuplicate(false);

    const formData = new FormData();
    formData.append("name", form.name);
    formData.append("email", form.email);
    formData.append("message", form.message);
    formData.append("consent", form.consent ? "true" : "false");

    if(null !== form.image){
      if(!form.consent){
        setErr(true);
        return;
      }
      formData.append("image", form.image);
    }

    setForm((prev) => ({
      ...prev,
      name: "",
      email: "",
      message: "",
      consent: false,
      image: null,
    }));
    document.querySelector('input[type="file"]').value = null;
    setErr(false);

    const res = await fetch("http://localhost:8000/api/submit.php", {
      method: "POST",
      headers: {
      Authorization: `Bearer ${process.env.REACT_APP_API_KEY}`,
      },
      body: formData,
    });

    if (res.status === 401) {
      console.error("Failed to submit the form:", res.statusText);
      setGenError(true);
      return;
    }

    await res.json();
    if(res.status === 422){
        setErrDuplicate(true) 
    }
    else{ 
      setSuccess(true);
      setErrDuplicate(false)
      setFormDisplay("none");
    }
  };
  return (
    <div className="form-container">
      <img className="round-image" src={require("./sample.jpg")} alt="profile" />
      <form onSubmit={handleSubmit} className="form" style={{ display: formDisplay}}>
        <InputField
          label="Name"
          placeholder="Enter your Name"
          type="text"
          name="name"
          value={form.name}
          onChange={handleChange}
          required
        />
        <InputField
          label="Email"
          placeholder="Enter a valid email address"
          type="email"
          name="email"
          value={form.email}
          onChange={handleChange}
          required
        />
        <InputMessage form={form} handleChange={handleChange} />
        <input
          type="file"
          name="image"
          accept="image/*"
          onChange={handleChange}
        />
        <label className="checkbox">
          <input
            type="checkbox"
            name="consent"
            checked={form.consent}
            onChange={handleChange}
          />
          I accept the&nbsp;<a className="termsOfService" href="/">Terms of Service</a>
        </label>
        {err && <p className="error-message">Please ensure all fields are filled and consent is given.</p>}
        {errDuplicate && <p className="error-message">This email was already used.</p>}
        {genErr && <p className="error-message">The server encountered and error.</p>}
        <button type="submit">Submit</button>
      </form>
      <Success display={success}/>
    </div>
  );
};

export default Form;
