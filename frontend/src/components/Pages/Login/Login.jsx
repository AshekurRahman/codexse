import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import { Link } from "react-router-dom";
import { ToastContainer } from "react-toastify";
import Button from "../../Button/Button";
import Checkbox from "../../Checkbox/Checkbox";
import Input from "../../Input/Input";
import Message from "../../LoadinError/Error";
import Loading from "../../LoadinError/Loading";
import Logo from "../../Navbar/Logo";
import styles from "./Login.module.css";

import { BsFacebook, BsGoogle, BsTwitter } from "react-icons/bs";
import { login } from "../../../Redux/Actions/UserActions";
import Form, { Info } from "../../Form/Form";
import SiteHeader from "../../SiteHeader/SiteHeader";

const Login = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");

  const dispatch = useDispatch();
  const userLogin = useSelector((state) => state.userLogin);
  const { error, loading, userInfo } = userLogin;

  const { search = "" } = location || {};
  const redirect = search ? search.split("=")[1] : "/";

  useEffect(() => {
    if (userInfo) {
      history.push(redirect);
    }
  }, [userInfo, history, redirect]);

  const submitHandler = (e) => {
    e.preventDefault();
    dispatch(login(email, password));
  };

  return (
    <>
      <SiteHeader title={"Login"} />
      <div className={`section-padding`}>
        <div className="container">
          <div className="row">
            <div className="col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
              {error && <Message variant={`alert-danger`}>{error}</Message>}
              {loading && <Loading />}
              <Form onSubmit={submitHandler}>
                <Logo className="mb-5" />
                <Input
                  type="email"
                  placeholder="Email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                />
                <Input
                  type="password"
                  placeholder="Password"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                />
                <Checkbox id="remember" name="remember" label="Remember Me" />
                <Button type="submit">Login Now!</Button>
                <div className="py-4 text-center">OR</div>
                <div className={styles.login_social}>
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
                  Don't have an account?{" "}
                  <Link
                    to={
                      redirect ? `/register?redirect=${redirect}` : `/register`
                    }
                  >
                    Register Now!
                  </Link>
                </Info>
                <Info>
                  <Link to="/forgot">Forgot password?</Link>
                </Info>
              </Form>
            </div>
          </div>
        </div>
      </div>
      <ToastContainer />
    </>
  );
};

export default Login;
