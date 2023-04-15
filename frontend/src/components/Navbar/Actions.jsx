import React, { useContext, useEffect, useState } from "react";
import { BsFillCloudMoonFill, BsFillCloudSunFill } from "react-icons/bs";
import { Link } from "react-router-dom";
import { UserContext } from "../../UserContext";
import styles from "./Navbar.module.css";

const Actions = () => {
  const { setUserInfo, userInfo } = useContext(UserContext);
  const [scheme, setScheme] = useState(false);

  useEffect(() => {
    const localTheme = JSON.parse(localStorage.getItem("theme")) || false;
    setScheme(localTheme);
  }, []);

  useEffect(() => {
    document.body.classList.toggle("light", scheme);
    localStorage.setItem("theme", scheme);
  }, [scheme]);

  const handleLogout = async () => {
    try {
      await fetch("http://localhost:4000/logout", {
        method: "POST",
        credentials: "include",
      });
      setUserInfo(null);
    } catch (err) {
      console.log("Logout failed: ", err);
    }
  };

  return (
    <div className={styles.nav_actions}>
      <button
        className={`${styles.colormode} ${scheme && styles.active}`}
        onClick={() => setScheme(!scheme)}
      >
        <span>{scheme ? <BsFillCloudSunFill /> : <BsFillCloudMoonFill />}</span>
      </button>

      {userInfo ? (
        <>
          <Link className={styles.link} to="/post-create">
            Create a post
          </Link>
          <button className={styles.link_button} onClick={handleLogout}>
            Logout
          </button>
        </>
      ) : (
        <Link className={styles.link_button} to="/login">
          Sign in
        </Link>
      )}
    </div>
  );
};

export default Actions;
