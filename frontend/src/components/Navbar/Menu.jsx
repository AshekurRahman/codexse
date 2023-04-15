import React from "react";
import { NavLink } from "react-router-dom";
import styles from "./Navbar.module.css";

const Menu = () => {
  return (
    <nav className={styles.nav_menu}>
      <ul>
        <li>
          <NavLink exact="true" to="/">
            Home
          </NavLink>
        </li>
        <li>
          <NavLink to="/products">Products</NavLink>
        </li>
        <li>
          <NavLink to="/news">News</NavLink>
        </li>
        <li>
          <NavLink to="/contact">Contact</NavLink>
        </li>
      </ul>
    </nav>
  );
};

export default Menu;
