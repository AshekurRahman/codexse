import React from "react";
import { Link } from "react-router-dom";
import styles from "./Navbar.module.css";

import LogoLink from "../../assets/images/logo-icon.png";

const Logo = ({ className }) => {
  return (
    <Link to="/" className={`${className} ${styles.nav_logo}`}>
      <span className={styles.logo_icon}>
        <img src={LogoLink} alt="Codexse" />
      </span>
      <span className={styles.logo_label}>Codexse</span>
    </Link>
  );
};

export default Logo;
