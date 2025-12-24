import React from "react";
import Actions from "./Actions";
import Logo from "./Logo";
import Menu from "./Menu";
import styles from "./Navbar.module.css";

const Navbar = () => {
  return (
    <div className={styles.navbar}>
      <div className="container">
        <div className={styles.nav_row}>
          <Logo />
          <Menu />
          <Actions />
        </div>
      </div>
    </div>
  );
};

export default Navbar;
