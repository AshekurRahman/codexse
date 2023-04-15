import React from "react";
import Button from "../../Button/Button";
import Input from "../../Input/Input";
import Logo from "../../Navbar/Logo";
import styles from "./Forgot.module.css";

const Forgot = () => {
  return (
    <div className={styles.forgot_page}>
      <div className="container">
        <div className="row">
          <div className="col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <form action="#" className={styles.forgot_form}>
              <Logo className="mb-5" />
              <Input type="email" placeholder="Email" />
              <Button type="submit">Forgot Password</Button>
              <div className={styles.forgot_text}>
                Enter your email adn we'll send you a link to reset your
                password.
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Forgot;
