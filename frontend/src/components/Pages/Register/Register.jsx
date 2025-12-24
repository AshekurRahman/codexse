import React, { useState } from "react";
import { Link, Navigate } from "react-router-dom";
import { ToastContainer, toast } from "react-toastify";
import Button from "../../Button/Button";
import Checkbox from "../../Checkbox/Checkbox";
import Input from "../../Input/Input";
import Logo from "../../Navbar/Logo";
import styles from "./Register.module.css";

import { BsFacebook, BsGoogle, BsTwitter } from "react-icons/bs";
import Form, { Info } from "../../Form/Form";
import SiteHeader from "../../SiteHeader/SiteHeader";

const Register = () => {
  const [username, setUsername] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [privacyChecked, setPrivacyChecked] = useState(false);
  const [redirect, setRedirect] = useState();

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!username || !email || !password) {
      toast("Please fill out all fields");
      return;
    }
    if (!validateEmail(email)) {
      toast("Please enter a valid email address");
      return;
    }
    if (!privacyChecked) {
      toast("Please agree to the privacy policy");
      return;
    }
    try {
      const requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          username,
          email,
          password,
          privacyChecked,
        }),
      };
      const response = await fetch(
        "http://localhost:4000/register",
        requestOptions
      );
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      const data = await response.json();
      console.log(data);
      if (data.emailExists) {
        toast("This email address is already registered");
      } else {
        setRedirect(true);
      }
    } catch (error) {
      toast("Something went wrong. Please try again later.");
    }
  };

  const validateEmail = (email) => {
    const re = /\S+@\S+\.\S+/;
    return re.test(String(email).toLowerCase());
  };

  if (redirect) {
    toast("working");
    return <Navigate to={`/login`} />;
  }

  return (
    <>
      <SiteHeader title={`Registration`} />
      <div className={`section-padding`}>
        <div className="container">
          <div className="row">
            <div className="col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
              <Form onSubmit={handleSubmit}>
                <Logo className="mb-5" />
                <Input
                  type="text"
                  placeholder="Name"
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                />
                <Input
                  type="email"
                  placeholder="Email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                />
                <Input
                  type="password"
                  placeholder="Password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                />
                <Checkbox
                  id="privacy"
                  name="privacy"
                  checked={privacyChecked}
                  onChange={(e) => setPrivacyChecked(e.target.checked)}
                >
                  I agree to the{" "}
                  <Link to="/privacy-policy">Privacy Policy</Link>
                </Checkbox>
                <Button type="submit">Register Now!</Button>
                <div className="py-4 text-center">OR</div>
                <div className={styles.register_social}>
                  <Link className={styles.fb} to="/login">
                    <BsFacebook />
                  </Link>
                  <Link className={styles.tw} to="/login">
                    <BsTwitter />
                  </Link>
                  <Link className={styles.gl} to="/login">
                    <BsGoogle />
                  </Link>
                </div>
                <Info>
                  Already have an account? <Link to="/login">Login Now!</Link>
                </Info>
              </Form>
            </div>
          </div>
        </div>
        <ToastContainer />
      </div>
    </>
  );
};

export default Register;
